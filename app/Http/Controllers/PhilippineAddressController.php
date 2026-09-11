<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class PhilippineAddressController extends Controller
{
    private const BASE_URL = 'https://classification.psa.gov.ph/psgc';

    public function regions(): JsonResponse
    {
        return $this->addressResponse('regions');
    }

    public function provinces(string $region): JsonResponse
    {
        $regionCode = substr($region, 0, 2);

        try {
            return response()->json(
                collect($this->locations('provinces', ['reg' => $regionCode]))
                    // NCR has no provinces, so its cities remain the selectable top-level areas.
                    ->filter(fn (array $record) => data_get($record, 'level') === 'Prov' || ($regionCode === '13' && data_get($record, 'level') === 'City'))
                    ->values()
                    ->all()
            );
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Address service unavailable.',
                'data' => [],
            ], 500);
        }
    }

    public function municipalities(string $province, Request $request): JsonResponse
    {
        $filters = [
            'prv' => $request->query('prv', $this->provinceCode($province)),
        ];

        try {
            $locations = collect($this->locations('municipalities', $filters));

            if ((string) data_get($filters, 'prv') === '56') {
                $locations->push([
                    'code' => '0405643000',
                    'name' => 'City of Lucena',
                    'prv' => 56,
                    'mun' => 43,
                    'level' => 'City',
                ]);
            }

            return response()->json(
                $locations
                    ->unique('code')
                    ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                    ->values()
                    ->all()
            );
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Address service unavailable.',
                'data' => [],
            ], 500);
        }
    }

    public function barangays(string $municipality, Request $request): JsonResponse
    {
        return $this->addressResponse('barangays', [
            'prv' => $request->query('prv'),
            'mun' => $request->query('mun', $this->municipalityCode($municipality)),
        ]);
    }

    private function addressResponse(string $level, array $filters = []): JsonResponse
    {
        try {
            return response()->json($this->locations($level, $filters));
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Address service unavailable.',
                'data' => [],
            ], 500);
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
                ->withOptions([
                    'verify' => config('services.psgc.verify_ssl', true),
                ])
                ->timeout(15)
                ->retry(2, 250)
                ->get(self::BASE_URL.'/'.$version.'/'.$level, [
                    'token' => $token,
                    'page_size' => 1000,
                    ...$filters,
                ]);

            $response->throw();

            $records =
                $response->json('results.psgc_data')
                ?? $response->json('results')
                ?? $response->json('data')
                ?? $response->json()
                ?? [];

            return collect($records)
                ->map(fn (array $record) => [
                    'code' => (string) ($record['psgc_code'] ?? $record['code'] ?? ''),
                    'name' => $record['area_name'] ?? $record['name'] ?? '',
                    'prv' => $record['prv'] ?? null,
                    'mun' => $record['mun'] ?? null,
                    'level' => $record['geographic_level'] ?? $record['type'] ?? null,
                ])
                ->filter(fn (array $record) => filled($record['code']) && filled($record['name']))
                ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                ->values()
                ->all();
        });
    }

    private function provinceCode(string $province): string
    {
        return ltrim(substr($province, 2, 3), '0') ?: $province;
    }

    private function municipalityCode(string $municipality): string
    {
        return ltrim(substr($municipality, 5, 2), '0') ?: $municipality;
    }
}
