<?php

namespace App\Services;

// use App\Services\Traits\PaymentInstallmentFormatter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PapelService
{
    // use PaymentInstallmentFormatter; // Trait missing from codebase

    public function __construct()
    {
        $this->config['client_id'] = config('services.papel.client_id', 'TEST1234');
        $this->config['api_user'] = config('services.papel.merchant_id', 'default_api_user'); 
        $this->config['api_pass'] = config('services.papel.api_key', 'default_api_pass');
        $this->config['currency'] = 'TRY';
        $this->baseUrl = 'https://pf-payment-external.papel.com.tr';
    }

    protected $settings;
    protected $pos;
    protected array $config = [];
    protected string $baseUrl;

    public function setSettings($settings): self
    {
        $this->settings = $settings;



        return $this;
    }

    public function setPos($pos): self
    {
        $this->pos = $pos;

        if (!$this->pos || !$this->pos->contents) {
            return $this;
        }

        $posData = json_decode($this->pos->contents);
        $settings = $posData->settings ?? [];

        $this->config['client_id']  = config('services.papel.client_id');
        $this->config['api_user']   = config('services.papel.merchant_id');
        $this->config['api_pass']   = config('services.papel.api_key');
        $this->config['currency']   = 'TRY';

        $this->baseUrl = rtrim($settings->base_url->value ?? 'https://pf-payment-external.papel.com.tr', '/');

        return $this;
    }


    protected function generateMeta(): array
    {
        $rnd = Str::random(6);
        $timeSpan = now()->format('YmdHis');

        $hash = base64_encode(hash('sha512', $this->config['api_pass'] . $this->config['client_id'] . $this->config['api_user'] . $rnd . $timeSpan, true));

        return [
            'clientId' => $this->config['client_id'],
            'apiUser' => $this->config['api_user'],
            'rnd' => $rnd,
            'timeSpan' => $timeSpan,
            'hash' => $hash,
        ];
    }

    protected function post(string $uri, array $data): array
    {
	
        $response = Http::post("{$this->baseUrl}{$uri}", array_merge($this->generateMeta(), $data));

        return json_decode($response->body(), true); // 👈 burada array olarak döner
    }


    protected function get(string $uri): string
    {
        return Http::get("{$this->baseUrl}{$uri}")->body();
    }

    public function startThreeD(array $options): array
    {
        return $this->post('/api/Payment/ThreeDPayment', [
            'orderId' => $options['order_code'] ?? null,
            'callbackUrl' => route('subscription.callback', ['bank' => 'papel']) . "?type=manuel&order_code=" . $options['order_code'] . "&transc_id=",
            'amount' => $options['amount'],
            'currency' => $options['currency'] ?? 949,
            'installmentCount' => $options['installmentCount'] ?? 0,
        ]);
    }

    public function getThreeDForm(string $threeDSessionId): string
    {
        return $this->get("/api/Payment/ThreeDSecure/{$threeDSessionId}");
    }

    public function submitCard(array $card, string $threeDSessionId): string
    {
        $data = [
            'cardNo' => $card['cardNo'],
            'cvv' => $card['cvv'],
            'expireDate' => $card['expireDate'],
            'cardHolderName' => $card['cardHolderName'],
            'threeDSessionId' => $threeDSessionId,
            'language' => $card['language'] ?? 'tr-TR',
            'amount' => $card['amount'] ?? null,
            'ComissionCalculated' => $card['ComissionCalculated'], // merchant ödüyorsa true
        ];

        // Müşteri bilgileri
        if (isset($card['customerCity'])) {
            $data['CustomerCity'] = $card['customerCity'];
        }
        if (isset($card['customerName'])) {
            $data['CustomerName'] = $card['customerName'];
        }
        if (isset($card['customerSurname'])) {
            $data['CustomerSurname'] = $card['customerSurname'];
        }
        if (isset($card['customerAddress'])) {
            $data['CustomerAddress'] = $card['customerAddress'];
        }
        if (isset($card['customerEmail'])) {
            $data['CustomerEmail'] = $card['customerEmail'];
        }
        if (isset($card['customerGsm'])) {
            $data['CustomerGsm'] = $card['customerGsm'];
        }
        // IP adresi - eğer gönderilmediyse request'ten al
        $data['clientIpAddress'] = request()->ip();

        // Basket objesi
        if (isset($card['basketId']) && isset($card['amount'])) {
            // Amount kuruş cinsinden, price lira cinsinden olmalı (100'e böl)
            $amountInKurus = (float)preg_replace('/\D/', '', $card['amount']);
            $priceInLira = $amountInKurus / 100;
            
            $data['basket'] = [
                'basketId' => $card['basketId'],
                'items' => [
                    [
                        'name' => 'tahsilat işlemi',
                        'price' => $priceInLira,
                        'quantity' => 1
                    ]
                ],
                'total' => 1
            ];
        }
        
        return Http::withHeaders(['Accept' => 'text/html'])
            ->post("{$this->baseUrl}/api/Payment/ProcessCardForm", $data)->body();
    }

    public function cancel(string $orderId): array
    {
        return $this->post('/api/Payment/Void', ['orderId' => $orderId]);
    }

    public function refund(string $orderId, int $amount): array
    {
        return $this->post('/api/Payment/Void', [
            'orderId' => $orderId,
            'amount' => $amount,
        ]);
    }

    public function listTransactions(string $date, int $page = 1, int $pageSize = 10, string $orderId = null): array
    {
        return $this->post('/api/Payment/History', array_filter([
            'transactionDate' => $date,
            'page' => $page,
            'pageSize' => $pageSize,
            'orderId' => $orderId,
        ]));
    }


    public function getInstallments(int $amount, ?string $cardNumber = null, string $commissionBy = 'merchant'): array
{
    $formatted = ['bank_data' => []];
    $isUserPaysCommission = ($commissionBy === 'user');

    // Eski API formatını işleyen yardımcı (BIN sonucunda geliyor)
    $addInstallments = function (string $bankName, array $packages) use (&$formatted, $amount, $isUserPaysCommission) {
        $parseNumber = function ($value): float {
            if ($value === null || $value === '') return 0.0;
            $clean = preg_replace('/[^\d,\.]/', '', (string) $value);
            if (strpos($clean, ',') !== false && strpos($clean, '.') !== false) {
                $clean = str_replace('.', '', $clean);
                $clean = str_replace(',', '.', $clean);
            } else {
                $clean = str_replace(',', '.', $clean);
            }
            return (float) $clean;
        };

        foreach ($packages as $pkg) {
            $installment = (int) ($pkg['Installment'] ?? 0);

            $apiRate    = $pkg['InterestRate'] !== null ? round($parseNumber($pkg['InterestRate']), 2) : 0.0;
            $apiTotal   = round($parseNumber($pkg['Amount'] ?? 0), 2);
            $apiMonthly = $pkg['MonthlyInstallmentAmount'] !== null
                ? round($parseNumber($pkg['MonthlyInstallmentAmount']), 2)
                : null;

            if ($isUserPaysCommission) {
                $total   = $apiTotal > 0 ? $apiTotal : round($amount, 2);
                $monthly = $apiMonthly !== null
                    ? $apiMonthly
                    : ($installment > 0 ? round($total / $installment, 2) : $total);
                $rate    = $apiRate;
            } else {
                $total   = round($amount, 2);
                $monthly = $installment > 0 ? round($total / $installment, 2) : $total;
                $rate    = $apiRate;
            }

            if (!$isUserPaysCommission || $apiRate > 0 || $installment === 1) {
                $formatted['bank_data'][$bankName][] = [
                    'installment' => $installment,
                    'rate'        => round($rate, 2),
                    'total'       => round($total, 2),
                    'monthly'     => round($monthly, 2),
                ];
            }
        }
    };

    // Yeni API formatını (GetAllCommissionAndInstallmentInfo) işleyen yardımcı
  $processAllBanksPackages = function (string $bankName, array $packages) use (&$formatted, $amount, $isUserPaysCommission) {
    $byInstallment = [];

    $choose = function (array $current = null, array $candidate) use ($isUserPaysCommission) {
        if ($current === null) return $candidate;
        if ($isUserPaysCommission) {
            return ($candidate['total'] < $current['total']) ? $candidate : $current;
        }
        return ($candidate['rate'] < $current['rate']) ? $candidate : $current;
    };

    $hasValidData = false; // 👈 bankada gerçekten taksit/komisyon var mı kontrolü

    foreach ($packages as $pkg) {
        $ccCommission = (float)($pkg['BankCommissionForCreditCard'] ?? 0);
        $installmentRates = $pkg['InstallmentRate'] ?? null;

        // hiçbir veri yoksa bu paketi tamamen atla
        if (empty($installmentRates) && $ccCommission <= 0) {
            continue;
        }

        $hasValidData = true; // en az bir veri bulundu

        // 1 TAKSİT (tek çekim)
        if ($ccCommission > 0) {
            $rate1 = round($ccCommission, 2);
            if ($isUserPaysCommission) {
                $total1   = round($amount * (1 + ($rate1 / 100)), 2);
                $monthly1 = $total1;
            } else {
                $total1   = round($amount, 2);
                $monthly1 = $total1;
            }
            $row1 = [
                'installment' => 1,
                'rate'        => $rate1,
                'total'       => $total1,
                'monthly'     => $monthly1,
            ];
            $byInstallment[1] = $choose($byInstallment[1] ?? null, $row1);
        }

        // T2..T12 (varsa)
        if (is_array($installmentRates)) {
            foreach ($installmentRates as $key => $data) {
                $n = (int)preg_replace('/\D+/', '', (string)$key);
                if ($n < 2) continue;

                $r = isset($data['Rate']) ? (float)$data['Rate'] : 0.0;
                if ($r <= 0) continue;

                $rate = round($r, 2);

                if ($isUserPaysCommission) {
                    $total   = round($amount * (1 + ($rate / 100)), 2);
                    $monthly = round($total / $n, 2);
                } else {
                    $total   = round($amount, 2);
                    $monthly = round($amount / $n, 2);
                }

                $row = [
                    'installment' => $n,
                    'rate'        => $rate,
                    'total'       => $total,
                    'monthly'     => $monthly,
                ];
                $byInstallment[$n] = $choose($byInstallment[$n] ?? null, $row);
            }
        }
    }

    // 👇 hiçbir geçerli veri yoksa bankayı ekleme
    if ($hasValidData && !empty($byInstallment)) {
        ksort($byInstallment);
        $formatted['bank_data'][$bankName] = array_values($byInstallment);
    }
};


    if ($cardNumber) {
        // BIN’e göre tek kart sorgusu (eski format)
        $bin = substr(preg_replace('/\D/', '', $cardNumber), 0, 6);
        $res = $this->post('/api/Payment/GetPublicInstallmentOptions', [
            'MerchantID'                     => preg_replace('/\D/', '', $this->config['api_user']),
            'bin'                            => $bin,
            'amount'                         => $amount,
            'customizableInstallmentOptions' => [],
        ]);

        $bankName = $res['BankName'] ?? 'Bilinmeyen Banka';
        $packages = $res['CommissionPackages'] ?? [];

        $formatted['card'] = [
            'card_bank_name' => $bankName,
            'card_scheme'    => $res['CardClass']  ?? '',
            'card_family'    => $res['CardType']   ?? '',
            'BIN'            => $res['CardPrefix'] ?? $bin,
        ];

        $addInstallments($bankName, $packages);
    } else {
        // TÜM BANKALAR (yeni format)
        $resAll = $this->post('/api/Payment/GetAllCommissionAndInstallmentInfo', []);
        $banks  = $resAll['CommissionAndInstallmentInfoList'] ?? [];

        foreach ($banks as $bank) {
            $bankName = $bank['BankName'] ?? 'Bilinmeyen Banka';
            $packages = $bank['CommissionPackages'] ?? [];

            // Paketlerde InstallmentRate yoksa yine de tek çekim satırı üretilecek
            $processAllBanksPackages($bankName, $packages);
        }
    }

    return $formatted;
}

    /**
     * Standart formatta taksit bilgilerini döndürür (cardCheck için).
     * 
     * @param float $amount Tutar
     * @param string|null $cardNumber Kart numarası (opsiyonel)
     * @param string $commissionBy 'user' veya 'merchant'
     * @param array $activeInstallments Aktif taksitler (filtreleme için)
     * @param bool $isStore Mağaza kullanıcısı mı?
     * @param array $noCommissionInstallments Komisyonsuz taksitler
     * @param object|null $order Sipariş objesi (link kontrolü için)
     * @return array Standart format: ['card_program' => ..., 'card_scheme' => ..., 'card_bank' => ..., 'installments' => [...]]
     */
    /*
    public function getFormattedInstallments(
        float $amount,
        ?string $cardNumber = null,
        string $commissionBy = 'merchant',
        array $activeInstallments = [],
        bool $isStore = false,
        array $noCommissionInstallments = [],
        $order = null
    ): array {
        $data = $this->getInstallments($amount, $cardNumber, $commissionBy);
        
        if (empty($data['bank_data'])) {
            return [
                'card_program' => null,
                'card_scheme' => null,
                'card_bank' => null,
                'installments' => []
            ];
        }

        // Tüm bankalardan taksitleri topla
        $allInstallments = [];
        foreach ($data['bank_data'] as $bankName => $installments) {
            foreach ($installments as $item) {
                $allInstallments[] = $item;
            }
        }

        $rawData = [
            'installments' => $allInstallments,
            'bank_data' => isset($data['card']) ? [
                'card_brand' => $data['card']['card_family'] ?? null,
                'bank_name' => $data['card']['card_bank_name'] ?? null,
                'brand' => $data['card']['card_scheme'] ?? null,
            ] : []
        ];

        $formatted = $this->formatInstallmentsFromRawData(
            $amount,
            $rawData,
            $commissionBy,
            $activeInstallments,
            $isStore,
            $noCommissionInstallments,
            $order
        );

        // Tekrarları kaldır
        $uniqueRows = [];
        $seen = [];
        foreach ($formatted['installments'] as $row) {
            if (!isset($seen[$row['installment']])) {
                $uniqueRows[] = $row;
                $seen[$row['installment']] = true;
            }
        }
        usort($uniqueRows, fn($a, $b) => $a['installment'] <=> $b['installment']);

        $formatted['installments'] = $uniqueRows;
        return $formatted;
    }
    */

    /**
     * View için özel format (elekse view'ı için).
     */
    /*
    public function getFormattedInstallmentsForView(
        float $amount,
        ?string $cardNumber = null,
        string $commissionBy = 'merchant',
        array $activeInstallments = [],
        bool $isStore = false,
        array $noCommissionInstallments = [],
        $order = null
    ): array {
        $posData = $this->getFormattedInstallments(
            $amount,
            $cardNumber,
            $commissionBy,
            $activeInstallments,
            $isStore,
            $noCommissionInstallments,
            $order
        );

        return $this->formatForElekseView($posData, !empty($cardNumber));
    }
    */

}
