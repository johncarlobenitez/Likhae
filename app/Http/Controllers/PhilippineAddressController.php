<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PhilippineAddressController extends Controller
{
    private const BASE_URL = 'https://classification.psa.gov.ph/psgc';

    public function regions(): JsonResponse
    {
        return response()->json($this->locations('regions'));
    }

    public function provinces(string $region): JsonResponse
    {
        try {
            return response()->json($this->locations('provinces', ['reg' => substr($region, 0, 2)]));
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => 'Address service unavailable.', 'data' => []], 500);
        }
    }

    public function municipalities(string $province, Request $request): JsonResponse
    {
        try {
            return response()->json($this->locations('municipalities', [
                'prv' => $request->query('prv', $province),
            ]));
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => 'Address service unavailable.', 'data' => []], 500);
        }
    }

    public function barangays(string $municipality, Request $request): JsonResponse
    {
        try {
            return response()->json($this->locations('barangays', [
                'prv' => $request->query('prv'),
                'mun' => $request->query('mun', $municipality),
            ]));
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => 'Address service unavailable.', 'data' => []], 500);
        }
    }

    private function locations(string $level, array $filters = []): array
    {
        $token = config('services.psgc.token');
        $version = config('services.psgc.version', 'Q2_2024');

        abort_if(blank($token), 503, 'PSGC_API_TOKEN is not configured.');

        $filters = collect($filters)->filter(fn ($value) => filled($value))->all();
        $cacheKey = 'psgc.'.$version.'.'.$level.'.'.md5(json_encode($filters));

        return Cache::remember($cacheKey, now()->addWeek(), function () use ($level, $filters, $token, $version) {
            $response = Http::acceptJson()
                ->timeout(15)
                ->retry(2, 250)
                ->get(self::BASE_URL.'/'.$version.'/'.$level, [
                    'token' => $token,
                    'page_size' => 1000,
                    ...$filters,
                ]);

            $response->throw();

            $records = $response->json('results') ?? $response->json('data') ?? [];

            return collect($records)
                ->map(fn (array $record) => [
                    'code' => (string) ($record['code'] ?? $record['psgc_code'] ?? ''),
                    'name' => $record['area_name'] ?? $record['name'] ?? '',
                    'prv' => $record['prv'] ?? null,
                    'mun' => $record['mun'] ?? null,
                    'level' => $record['geographic_level'] ?? null,
                ])
                ->filter(fn (array $record) => filled($record['code']) && filled($record['name']))
                ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                ->values()
                ->all();
        });
    }
}
