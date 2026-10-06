<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerReportsPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_can_view_business_intelligence_and_download_a_pdf_brief(): void
    {
        $this->withoutVite();
        $this->seed(DatabaseSeeder::class);
        $seller = User::query()->where('email', 'seller@likhae.com')->sole();

        $this->actingAs($seller)
            ->get(route('seller.reports', ['report' => 'products']))
            ->assertOk()
            ->assertSee('Product performance')
            ->assertSee('Download PDF brief')
            ->assertSee('No sales');

        $response = $this->actingAs($seller)
            ->get(route('seller.reports.download', ['report' => 'products']));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-1.4', $response->getContent());
    }
}
