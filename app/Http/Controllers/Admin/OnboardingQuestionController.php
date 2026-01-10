<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OnboardingQuestion;
use Illuminate\Http\Request;

class OnboardingQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $questions = OnboardingQuestion::orderBy('order')->get();
        return view('admin.onboarding.questions.index', compact('questions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.onboarding.questions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'step_id' => 'required|string|unique:onboarding_questions,step_id',
            'question' => 'required|string',
            'type' => 'required|in:radio,checkbox,text',
            'order' => 'required|integer',
        ]);

        $options = null;
        if ($request->has('options_list') && !empty($request->options_list)) {
            // Split by newline and filter empty
            $options = array_filter(array_map('trim', explode("\n", $request->options_list)));
            $options = array_values($options); // Reindex
        }

        OnboardingQuestion::create([
            'step_id' => $request->step_id,
            'question' => $request->question,
            'subtext' => $request->subtext,
            'type' => $request->type,
            'options' => $options,
            'has_other' => $request->has('has_other'),
            'order' => $request->order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.onboarding-questions.index')->with('success', 'Soru başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OnboardingQuestion $onboardingQuestion)
    {
        return view('admin.onboarding.questions.edit', compact('onboardingQuestion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OnboardingQuestion $onboardingQuestion)
    {
        $request->validate([
            'step_id' => 'required|string|unique:onboarding_questions,step_id,' . $onboardingQuestion->id,
            'question' => 'required|string',
            'type' => 'required|in:radio,checkbox,text',
            'order' => 'required|integer',
        ]);

        $options = null;
        if ($request->has('options_list')) {
             $options = array_filter(array_map('trim', explode("\n", $request->options_list)));
             $options = array_values($options);
        }

        $onboardingQuestion->update([
            'step_id' => $request->step_id,
            'question' => $request->question,
            'subtext' => $request->subtext,
            'type' => $request->type,
            'options' => $options,
            'has_other' => $request->has('has_other'),
            'order' => $request->order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.onboarding-questions.index')->with('success', 'Soru güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OnboardingQuestion $onboardingQuestion)
    {
        $onboardingQuestion->delete();
        return redirect()->route('admin.onboarding-questions.index')->with('success', 'Soru silindi.');
    }
}
