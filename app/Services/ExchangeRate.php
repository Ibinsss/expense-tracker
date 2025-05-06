<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ExchangeRate
{
    /**
     * The base currency we convert _from_.
     * You can make this configurable if you like.
     */
    protected string $base = 'MYR';

    /**
     * Fetch and cache all rates for the base currency.
     *
     * @return array<string, float>
     */
    public function rates(): array
    {
        // Cache for 12 hours (720 minutes)
        return Cache::remember("exchange_rates_{$this->base}", 720, function () {
            $apiKey = config('services.exchangerate.key');
            $url    = "https://v6.exchangerate-api.com/v6/{$apiKey}/latest/{$this->base}";

            $response = Http::get($url);

            if (! $response->successful()) {
                return [];
            }

            $data = $response->json();

            return $data['conversion_rates'] ?? [];
        });
    }

    /**
     * Convert an amount from the base currency into the target.
     *
     * @param  float        $amount
     * @param  string       $toCurrency
     * @return float        Rounded to 2 decimals
     */
    public function convert(float $amount, string $toCurrency): float
    {
        $rates = $this->rates();

        if (isset($rates[$toCurrency])) {
            return round($amount * $rates[$toCurrency], 2);
        }

        // If we don't have a rate for that currency, return original amount
        return $amount;
    }
}
