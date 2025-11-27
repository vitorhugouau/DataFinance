<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class QuoteController extends Controller
{
    public function currencies(): JsonResponse
    {
        $currencies = Cache::remember('currency_quotes', 300, function () {
            try {
                // Usando API gratuita para cotações de moedas
                $response = Http::timeout(5)->get('https://api.exchangerate-api.com/v4/latest/BRL');

                if ($response->successful()) {
                    $data = $response->json();
                    $rates = $data['rates'] ?? [];

                    // API retorna: 1 BRL = X USD, então 1 USD = 1/X BRL
                    return [
                        'USD' => [
                            'name' => 'Dólar Americano',
                            'symbol' => 'USD',
                            'rate' => isset($rates['USD']) ? (1 / $rates['USD']) : 5.0,
                            'base' => 'BRL',
                        ],
                        'EUR' => [
                            'name' => 'Euro',
                            'symbol' => 'EUR',
                            'rate' => isset($rates['EUR']) ? (1 / $rates['EUR']) : 5.5,
                            'base' => 'BRL',
                        ],
                        'GBP' => [
                            'name' => 'Libra Esterlina',
                            'symbol' => 'GBP',
                            'rate' => isset($rates['GBP']) ? (1 / $rates['GBP']) : 6.3,
                            'base' => 'BRL',
                        ],
                        'JPY' => [
                            'name' => 'Iene Japonês',
                            'symbol' => 'JPY',
                            'rate' => isset($rates['JPY']) ? (1 / $rates['JPY']) : 0.035,
                            'base' => 'BRL',
                        ],
                        'CNY' => [
                            'name' => 'Yuan Chinês',
                            'symbol' => 'CNY',
                            'rate' => isset($rates['CNY']) ? (1 / $rates['CNY']) : 0.7,
                            'base' => 'BRL',
                        ],
                    ];
                }
            } catch (\Exception $e) {
                // Fallback com valores aproximados
            }

            return [
                'USD' => ['name' => 'Dólar Americano', 'symbol' => 'USD', 'rate' => 5.0, 'base' => 'BRL'],
                'EUR' => ['name' => 'Euro', 'symbol' => 'EUR', 'rate' => 5.5, 'base' => 'BRL'],
                'GBP' => ['name' => 'Libra Esterlina', 'symbol' => 'GBP', 'rate' => 6.3, 'base' => 'BRL'],
                'JPY' => ['name' => 'Iene Japonês', 'symbol' => 'JPY', 'rate' => 0.035, 'base' => 'BRL'],
                'CNY' => ['name' => 'Yuan Chinês', 'symbol' => 'CNY', 'rate' => 0.7, 'base' => 'BRL'],
            ];
        });

        return response()->json($currencies);
    }

    public function cryptocurrencies(): JsonResponse
    {
        $cryptos = Cache::remember('crypto_quotes', 60, function () {
            try {
                // Usando CoinGecko API (gratuita)
                $response = Http::timeout(5)->get('https://api.coingecko.com/api/v3/simple/price', [
                    'ids' => 'bitcoin,ethereum,binancecoin,cardano,solana,polkadot,chainlink,avalanche-2,polygon,uniswap',
                    'vs_currencies' => 'brl',
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    $cryptoMap = [
                        'bitcoin' => ['name' => 'Bitcoin', 'symbol' => 'BTC'],
                        'ethereum' => ['name' => 'Ethereum', 'symbol' => 'ETH'],
                        'binancecoin' => ['name' => 'Binance Coin', 'symbol' => 'BNB'],
                        'cardano' => ['name' => 'Cardano', 'symbol' => 'ADA'],
                        'solana' => ['name' => 'Solana', 'symbol' => 'SOL'],
                        'polkadot' => ['name' => 'Polkadot', 'symbol' => 'DOT'],
                        'chainlink' => ['name' => 'Chainlink', 'symbol' => 'LINK'],
                        'avalanche-2' => ['name' => 'Avalanche', 'symbol' => 'AVAX'],
                        'polygon' => ['name' => 'Polygon', 'symbol' => 'MATIC'],
                        'uniswap' => ['name' => 'Uniswap', 'symbol' => 'UNI'],
                    ];

                    $result = [];
                    foreach ($cryptoMap as $id => $info) {
                        if (isset($data[$id]['brl'])) {
                            $result[$info['symbol']] = [
                                'name' => $info['name'],
                                'symbol' => $info['symbol'],
                                'price' => $data[$id]['brl'],
                                'currency' => 'BRL',
                            ];
                        }
                    }

                    return $result;
                }
            } catch (\Exception $e) {
                // Fallback
            }

            // Valores fallback aproximados
            return [
                'BTC' => ['name' => 'Bitcoin', 'symbol' => 'BTC', 'price' => 250000, 'currency' => 'BRL'],
                'ETH' => ['name' => 'Ethereum', 'symbol' => 'ETH', 'price' => 15000, 'currency' => 'BRL'],
                'BNB' => ['name' => 'Binance Coin', 'symbol' => 'BNB', 'price' => 2000, 'currency' => 'BRL'],
                'ADA' => ['name' => 'Cardano', 'symbol' => 'ADA', 'price' => 3, 'currency' => 'BRL'],
                'SOL' => ['name' => 'Solana', 'symbol' => 'SOL', 'price' => 500, 'currency' => 'BRL'],
            ];
        });

        return response()->json($cryptos);
    }

    public function all(): JsonResponse
    {
        $currencies = $this->currencies()->getData(true);
        $cryptos = $this->cryptocurrencies()->getData(true);

        return response()->json([
            'currencies' => $currencies,
            'cryptocurrencies' => $cryptos,
        ]);
    }
}
