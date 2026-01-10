<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class UserController extends Controller
{
    public function index()
    {
        $tenant = \App\Models\Tenant::find(auth()->user()->tenant_id);
        $plan = $tenant->plan;
        
        $limit = $plan->limits['user_count'] ?? 0;
        $currentCount = User::where('tenant_id', $tenant->id)->count();
        $limitReached = ($limit != -1 && $currentCount >= $limit);

        $users = User::where('tenant_id', $tenant->id)->withCount('proposals')->latest()->get();
        $availablePermissions = User::getAvailablePermissions();
        return view('tenant.users.index', compact('users', 'availablePermissions', 'limitReached', 'plan'));
    }

    public function store(Request $request)
    {
        $tenant = \App\Models\Tenant::find(auth()->user()->tenant_id);
        $plan = $tenant->plan;
        
        $limit = $plan->limits['user_count'] ?? 0;
        
        if ($limit != -1) {
             if (User::where('tenant_id', $tenant->id)->count() >= $limit) {
                return redirect()->route('subscription.plans')->with('error', 'Paketinizin kullanıcı ekleme limiti doldu. Lütfen paketinizi yükseltin.');
             }
        }


        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'permissions' => 'nullable|array',
        ]);

        $user = User::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_owner' => false,
            'is_owner' => false,
            'status' => 'active',
            'permissions' => $request->permissions,
        ]);

        // Tenant veritabanına da ekle
        $dbName = 'tenant_' . auth()->user()->tenant_id . '_teklif';
        $connectionName = 'tenant_temp';
        Config::set("database.connections.{$connectionName}", array_merge(
            Config::get('database.connections.mysql'),
            ['database' => $dbName]
        ));

        DB::connection($connectionName)->table('users')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_owner' => false,
            'status' => 'active',
            'permissions' => json_encode($request->permissions),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Alt kullanıcı başarıyla oluşturuldu.');
    }

    public function update(Request $request, User $user)
    {


        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'permissions' => 'nullable|array',
        ]);

        $oldEmail = $user->email;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->permissions = $request->permissions;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        // Tenant veritabanını güncelle
        $dbName = 'tenant_' . auth()->user()->tenant_id . '_teklif';
        $connectionName = 'tenant_temp';
        Config::set("database.connections.{$connectionName}", array_merge(
            Config::get('database.connections.mysql'),
            ['database' => $dbName]
        ));

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'permissions' => json_encode($request->permissions),
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        DB::connection($connectionName)->table('users')
            ->where('email', $oldEmail)
            ->update($updateData);

        return back()->with('success', 'Kullanıcı bilgileri güncellendi.');
    }

    public function toggleStatus(User $user)
    {


        if ($user->id === auth()->id()) {
            return back()->with('error', 'Kendi durumunuzu değiştiremezsiniz.');
        }

        $user->status = $user->status === 'active' ? 'passive' : 'active';
        $user->save();

        // Tenant veritabanını güncelle
        $dbName = 'tenant_' . auth()->user()->tenant_id . '_teklif';
        $connectionName = 'tenant_temp';
        Config::set("database.connections.{$connectionName}", array_merge(
            Config::get('database.connections.mysql'),
            ['database' => $dbName]
        ));

        DB::connection($connectionName)->table('users')
            ->where('email', $user->email)
            ->update(['status' => $user->status, 'updated_at' => now()]);

        $message = $user->status === 'active' ? 'Kullanıcı aktif edildi.' : 'Kullanıcı pasife alındı.';
        return back()->with('success', $message);
    }

    public function destroy(User $user)
    {


        if ($user->id === auth()->id()) {
            return back()->with('error', 'Kendinizi silemezsiniz.');
        }

        $email = $user->email;
        $user->delete();

        // Tenant veritabanından da sil
        $dbName = 'tenant_' . auth()->user()->tenant_id . '_teklif';
        $connectionName = 'tenant_temp';
        Config::set("database.connections.{$connectionName}", array_merge(
            Config::get('database.connections.mysql'),
            ['database' => $dbName]
        ));
        
        DB::connection($connectionName)->table('users')->where('email', $email)->delete();

        return back()->with('success', 'Kullanıcı silindi.');
    }
}
