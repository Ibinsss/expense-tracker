<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ExchangeRate
{
    protected string $key;
    protected string $baseUrl;
    protected string $baseCurrency;

    public function __construct()
    {
        $this->key          = config('services.exchange_rate.key');
        $this->baseUrl      = config('services.exchange_rate.base_url');
        $this->baseCurrency = config('services.exchange_rate.base_currency');
    }

    /**
     * Fetch & cache the full rates table for 24h.
     * @param  bool  $force  ignore cache and re-fetch
     */
    public function rates(bool $force = false): array
    {
        return Cache::remember(
            "fx_rates_{$this->baseCurrency}",
            now()->addHours(24),
            fn() => $this->fetchRates()
        );
    }

    /** Actually call the third-party API */
    protected function fetchRates(): array
    {
        $url = "{$this->baseUrl}/{$this->key}/latest/{$this->baseCurrency}";

        $response = Http::get($url)->throw();

        // The API returns JSON like { "conversion_rates": { "USD":1.23, ... } }
        return $response->json('conversion_rates', []);
    }

    /**
     * Convert an RM amount into $to currency,
     * rounded to 2 decimals.
     */
    public function convert(float $amount, string $to): float
    {
        $rates = $this->rates();
        $rate  = $rates[$to] ?? 1.0;
        return round($amount * $rate, 2);
    }
}
