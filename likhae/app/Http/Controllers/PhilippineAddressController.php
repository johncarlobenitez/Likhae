<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PhilippineAddressController extends Controller
{
    private const BASE_URL = 'https://classification.psa.gov.ph/psgc';

    public function provinces(): JsonResponse
    {
        return response()->json($this->locations('provinces'));
    }

    public function municipalities(string $province): JsonResponse
    {
        return response()->json($this->locations('municipalities', ['prv' => $province]));
    }

    public function barangays(string $municipality, Request $request): JsonResponse
    {
        return response()->json($this->locations('barangays', [
            'mun' => $municipality,
            'prv' => $request->query('prv'),
        ]));
    }

    /** Keep the PSA token on the server and return only the fields the form needs. */
    private function locations(string $level, array $filters = []): array
    {
        $token = config('services.psgc.token');
        $version = config('services.psgc.version');

        if (blank($token)) {
            abort(503, 'PSGC_API_TOKEN is not configured.');
        }

        $cacheKey = 'psgc.'.$version.'.'.$level.'.'.md5(json_encode($filters));

        $filters = collect($filters)->filter(fn ($value) => filled($value))->all();

        return Cache::remember($cacheKey, now()->addWeek(), function () use ($level, $filters, $token, $version) {
            $response = Http::acceptJson()
                ->timeout(15)
                ->retry(2, 250)
                ->get(self::BASE_URL.'/'.$version.'/'.$level, [
                    'token' => $token,
                    ...$filters,
                    // PSA's API returns paginated results and supports up to 1,000 items per page.
                    'page_size' => 1000,
                ]);

            $response->throw();

            // The PSA API uses Django REST Framework pagination, placing location
            // records under "results" alongside metadata such as "count".
            $records = $response->json('results') ?? $response->json('data') ?? [];

            return collect($records)
                ->map(fn (array $record) => [
                    'code' => (string) ($record['code'] ?? $record['psgc_code'] ?? ''),
                    'name' => $record['area_name'] ?? '',
                    'prv'  => $record['prv'] ?? null,
                    'mun'  => $record['mun'] ?? null,
                ])
                ->filter(fn (array $record) => filled($record['code']) && filled($record['name']))
                ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                ->values()
                ->all();
        });
    }
}
