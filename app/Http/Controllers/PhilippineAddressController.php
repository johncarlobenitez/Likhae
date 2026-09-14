<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class PhilippineAddressController extends Controller
{
    private const BASE_URL = 'https://classification.psa.gov.ph/psgc';
    private const FALLBACK_BASE_URL = 'https://psgc.cloud/api/v2';

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

    public function postalCode(Request $request): JsonResponse
    {
        $postalCode = $this->resolvePostalCode(
            province: (string) $request->query('province', ''),
            municipality: (string) $request->query('municipality', ''),
            provinceName: (string) $request->query('province_name', ''),
            municipalityName: (string) $request->query('municipality_name', '')
        );

        if ($postalCode === null) {
            return response()->json([
                'message' => 'Postal code is not available for the selected location.',
                'postal_code' => null,
            ], 404);
        }

        return response()->json([
            'postal_code' => $postalCode,
        ]);
    }

    public static function expectedPostalCodeFor(
        string $province = '',
        string $municipality = '',
        string $provinceName = '',
        string $municipalityName = ''
    ): ?string {
        return (new self)->resolvePostalCode($province, $municipality, $provinceName, $municipalityName);
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

        $filters = collect($filters)->filter(fn ($value) => filled($value))->all();
        $cacheKey = 'psgc.'.$version.'.'.$level.'.'.md5(json_encode($filters));

        return Cache::remember($cacheKey, now()->addWeek(), function () use ($level, $filters, $token, $version) {
            try {
                if (filled($token)) {
                    return $this->psaLocations($level, $filters, (string) $token, $version);
                }

                throw new \RuntimeException('PSGC_API_TOKEN is not configured; using fallback source.');
            } catch (Throwable $e) {
                report($e);

                try {
                    return $this->fallbackLocations($level, $filters);
                } catch (Throwable $fallbackException) {
                    report($fallbackException);

                    return $this->localLocations($level, $filters);
                }
            }
        });
    }

    private function psaLocations(string $level, array $filters, string $token, string $version): array
    {
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

        return $this->normalizeLocations($records);
    }

    private function fallbackLocations(string $level, array $filters): array
    {
        $path = match ($level) {
            'regions' => '/regions',
            'provinces' => filled($filters['reg'] ?? null)
                ? '/regions/'.rawurlencode((string) $filters['reg']).(((string) $filters['reg'] === '13') ? '/cities-municipalities' : '/provinces')
                : '/provinces',
            'municipalities' => filled($filters['prv'] ?? null)
                ? '/provinces/'.rawurlencode((string) $filters['prv']).'/cities-municipalities'
                : '/cities-municipalities',
            'barangays' => filled($filters['mun'] ?? null)
                ? '/cities-municipalities/'.rawurlencode((string) $filters['mun']).'/barangays'
                : '/barangays',
            default => '/'.$level,
        };

        $response = Http::acceptJson()
            ->timeout(15)
            ->retry(2, 250)
            ->get(self::FALLBACK_BASE_URL.$path);

        $response->throw();

        $records = $response->json('data') ?? $response->json() ?? [];

        return $this->normalizeLocations($records);
    }

    private function localLocations(string $level, array $filters): array
    {
        if ($level === 'barangays') {
            if (filled($filters['prv'] ?? null) && filled($filters['mun'] ?? null)) {
                return $this->localBarangays((string) $filters['prv'], (string) $filters['mun']);
            }

            throw new \RuntimeException('Barangay fallback requires province and municipality filters.');
        }

        $path = storage_path('app/psgc/'.$level.'.json');

        if (! is_file($path)) {
            throw new \RuntimeException('Local PSGC cache is unavailable.');
        }

        $records = json_decode((string) file_get_contents($path), true);

        if (! is_array($records)) {
            throw new \RuntimeException('Local PSGC cache is invalid.');
        }

        return $this->filterLocations(
            $this->normalizeLocations($records),
            $level,
            $filters
        );
    }

    private function filterLocations(array $records, string $level, array $filters): array
    {
        return collect($records)
            ->filter(function (array $record) use ($level, $filters) {
                if ($level === 'provinces' && filled($filters['reg'] ?? null)) {
                    return Str::startsWith((string) $record['code'], (string) $filters['reg']);
                }

                if ($level === 'municipalities' && filled($filters['prv'] ?? null)) {
                    return (string) $record['prv'] === (string) $filters['prv'];
                }

                if ($level === 'barangays') {
                    $matchesProvince = blank($filters['prv'] ?? null)
                        || (string) $record['prv'] === (string) $filters['prv'];

                    $matchesMunicipality = blank($filters['mun'] ?? null)
                        || (string) $record['mun'] === (string) $filters['mun'];

                    return $matchesProvince && $matchesMunicipality;
                }

                return true;
            })
            ->values()
            ->all();
    }

    private function localBarangays(string $province, string $municipality): array
    {
        $path = storage_path('app/psgc/barangays/'.$province.'-'.$municipality.'.json');

        if (! is_file($path)) {
            return [];
        }

        $records = json_decode((string) file_get_contents($path), true);

        if (! is_array($records)) {
            return [];
        }

        return $this->normalizeLocations($records);
    }

    private function normalizeLocations(array $records): array
    {
        return collect($records)
            ->map(function (array $record) {
                $code = (string) ($record['psgc_code'] ?? $record['code'] ?? '');
                $type = $record['geographic_level'] ?? $record['type'] ?? null;

                return [
                    'code' => $code,
                    'name' => $record['area_name'] ?? $record['name'] ?? '',
                    'prv' => $record['prv'] ?? $record['province_code'] ?? ($code ? $this->provinceCode($code) : null),
                    'mun' => $record['mun'] ?? $record['city_municipality_code'] ?? ($code ? $this->municipalityCode($code) : null),
                    'level' => $type ? (Str::contains(Str::lower((string) $type), 'city') ? 'City' : (string) $type) : null,
                ];
            })
            ->filter(fn (array $record) => filled($record['code']) && filled($record['name']))
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();
    }

    private function provinceCode(string $province): string
    {
        return ltrim(substr($province, 2, 3), '0') ?: $province;
    }

    private function municipalityCode(string $municipality): string
    {
        return ltrim(substr($municipality, 5, 2), '0') ?: $municipality;
    }

    private function resolvePostalCode(
        string $province = '',
        string $municipality = '',
        string $provinceName = '',
        string $municipalityName = ''
    ): ?string {
        $provinceCode = $this->provinceCode($province);
        $municipalityCode = $this->municipalityCode($municipality);
        $byCode = config('philippine_postal_codes.by_code', []);

        if ($provinceCode !== '' && $municipalityCode !== '') {
            $key = $provinceCode.'-'.$municipalityCode;

            if (isset($byCode[$key])) {
                return (string) $byCode[$key];
            }
        }

        $byName = config('philippine_postal_codes.by_name', []);
        $nameKey = Str::of($provinceName)
            ->lower()
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->append('|')
            ->append(Str::of($municipalityName)->lower()->replaceMatches('/\s+/', ' ')->trim())
            ->toString();

        return isset($byName[$nameKey]) ? (string) $byName[$nameKey] : null;
    }
}
