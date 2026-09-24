<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PhilippineAddressApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        config([
            'services.psgc.token' => 'fake-token',
            'services.psgc.version' => 'Q2_2024',
            'services.psgc.local_first' => false,
        ]);
    }

    public function test_regions_are_loaded_from_the_psa_psgc_api(): void
    {
        Http::fake([
            'classification.psa.gov.ph/psgc/Q2_2024/regions*' => Http::response([
                'results' => [
                    [
                        'psgc_code' => '0400000000',
                        'area_name' => 'Region IV-A (CALABARZON)',
                        'geographic_level' => 'Reg',
                        'reg' => 4,
                        'prv' => 0,
                        'mun' => 0,
                    ],
                ],
            ]),
        ]);

        $this->getJson('/address/philippines/regions')
            ->assertOk()
            ->assertJson([
                [
                    'code' => '0400000000',
                    'name' => 'Region IV-A (CALABARZON)',
                    'prv' => 0,
                    'mun' => 0,
                    'level' => 'Reg',
                ],
            ]);

        Http::assertSent(fn ($request) => $request->url() === 'https://classification.psa.gov.ph/psgc/Q2_2024/regions?token=fake-token&page_size=1000');
    }

    public function test_child_address_requests_pass_psgc_parent_filters(): void
    {
        Http::fake([
            'classification.psa.gov.ph/psgc/Q2_2024/provinces*' => Http::response([
                'results' => [
                    [
                        'psgc_code' => '0403400000',
                        'area_name' => 'Laguna',
                        'geographic_level' => 'Prov',
                        'reg' => 4,
                        'prv' => 34,
                        'mun' => 0,
                    ],
                ],
            ]),
            'classification.psa.gov.ph/psgc/Q2_2024/municipalities*' => Http::response([
                'results' => [
                    [
                        'psgc_code' => '0403404000',
                        'area_name' => 'Bay',
                        'geographic_level' => 'Mun',
                        'reg' => 4,
                        'prv' => 34,
                        'mun' => 4,
                    ],
                ],
            ]),
            'classification.psa.gov.ph/psgc/Q2_2024/barangays*' => Http::response([
                'results' => [
                    [
                        'psgc_code' => '0403404001',
                        'area_name' => 'Bitin',
                        'geographic_level' => 'Bgy',
                        'reg' => 4,
                        'prv' => 34,
                        'mun' => 4,
                    ],
                ],
            ]),
        ]);

        $this->getJson('/address/philippines/regions/0400000000/provinces')
            ->assertOk()
            ->assertJsonFragment([
                'code' => '0403400000',
                'name' => 'Laguna',
            ]);

        $this->getJson('/address/philippines/provinces/0403400000/municipalities')
            ->assertOk()
            ->assertJsonFragment([
                'code' => '0403404000',
                'name' => 'Bay',
            ]);

        $this->getJson('/address/philippines/municipalities/0403404000/barangays?prv=34&mun=4')
            ->assertOk()
            ->assertJsonFragment([
                'code' => '0403404001',
                'name' => 'Bitin',
            ]);

        Http::assertSent(fn ($request) => str_contains($request->url(), 'provinces') && str_contains($request->url(), 'reg=04'));
        Http::assertSent(fn ($request) => str_contains($request->url(), 'municipalities') && str_contains($request->url(), 'prv=34'));
        Http::assertSent(fn ($request) => str_contains($request->url(), 'barangays') && str_contains($request->url(), 'prv=34') && str_contains($request->url(), 'mun=4'));
    }

    public function test_quezon_municipalities_include_lucena_city(): void
    {
        Http::fake([
            'classification.psa.gov.ph/psgc/Q2_2024/municipalities*' => Http::response([
                'results' => [],
            ]),
        ]);

        $this->getJson('/address/philippines/provinces/0405600000/municipalities?prv=56')
            ->assertOk()
            ->assertJsonFragment([
                'code' => '0405643000',
                'name' => 'City of Lucena',
                'prv' => 56,
                'mun' => 43,
            ]);
    }

    public function test_missing_psgc_token_uses_fallback_instead_of_503(): void
    {
        config(['services.psgc.token' => null]);

        Http::fake([
            'psgc.cloud/api/v2/regions' => Http::response([
                'data' => [
                    ['code' => '0400000000', 'name' => 'Region IV-A (CALABARZON)', 'type' => 'Reg'],
                ],
            ]),
        ]);

        $this->getJson('/address/philippines/regions')
            ->assertOk()
            ->assertJsonFragment([
                'code' => '0400000000',
                'name' => 'Region IV-A (CALABARZON)',
            ]);
    }

    public function test_local_address_data_is_used_when_both_remote_providers_are_unavailable(): void
    {
        Http::fake(fn () => Http::response(['message' => 'Service unavailable'], 503));

        $this->getJson('/address/philippines/regions')
            ->assertOk()
            ->assertJsonFragment([
                'code' => '0400000000',
                'name' => 'Region IV-A (CALABARZON)',
            ]);
    }

    public function test_production_local_first_mode_does_not_require_an_http_provider(): void
    {
        config(['services.psgc.local_first' => true]);
        Http::preventStrayRequests();

        $this->getJson('/address/philippines/regions')
            ->assertOk()
            ->assertJsonFragment(['code' => '0400000000']);
        $this->getJson('/address/philippines/regions/0400000000/provinces')
            ->assertOk()
            ->assertJsonFragment(['code' => '0403400000', 'name' => 'Laguna']);
        $municipalities = $this->getJson('/address/philippines/provinces/0403400000/municipalities')
            ->assertOk()
            ->json();
        $this->assertContains(
            ['code' => '0403424000', 'name' => 'City of San Pablo', 'prv' => 34, 'mun' => 24, 'level' => 'City'],
            $municipalities
        );
        $this->getJson('/address/philippines/municipalities/0403424000/barangays')
            ->assertOk()
            ->assertJsonFragment(['code' => '0403424001', 'name' => 'Bagong Bayan II-A']);

        Http::assertNothingSent();
    }

    public function test_postal_code_is_resolved_from_selected_location(): void
    {
        $this->getJson('/address/philippines/postal-code?province=0403400000&municipality=0403424000&province_name=Laguna&municipality_name=City%20of%20San%20Pablo')
            ->assertOk()
            ->assertJson([
                'postal_code' => '4000',
            ]);
    }
}
