<?php

namespace Tests\Unit;

use App\Services\Reports\SellerReportPdfService;
use Tests\TestCase;

class SellerReportPdfServiceTest extends TestCase
{
    public function test_it_creates_a_valid_pdf_business_intelligence_brief(): void
    {
        $pdf = app(SellerReportPdfService::class)->render([
            'title' => 'Product Performance Report',
            'scope' => 'All catalog products',
            'range_label' => 'Oct 01, 2026 - Oct 06, 2026',
            'generated_at' => 'Oct 06, 2026 8:30 PM',
            'summary' => ['gross' => 174965, 'net' => 162467.50, 'orders' => 2, 'completion_rate' => 50],
            'performance_counts' => ['high' => 1, 'mid' => 1, 'low' => 1, 'no_sales' => 1],
            'insights' => ['Woven Bag is the strongest performer with PHP 12,000.00 from 20 completed units.'],
            'products' => [
                ['name' => 'Woven Bag', 'tier' => 'high', 'units' => 20, 'orders' => 2, 'revenue' => 12000],
                ['name' => 'Ceramic Cup', 'tier' => 'no_sales', 'units' => 0, 'orders' => 0, 'revenue' => 0],
            ],
        ]);

        $this->assertStringStartsWith('%PDF-1.4', $pdf);
        $this->assertStringContainsString('/Type /Page', $pdf);
        $this->assertStringContainsString('Product Performance Report', $pdf);
        $this->assertStringContainsString('Woven Bag', $pdf);
        $this->assertStringContainsString('%%EOF', $pdf);
    }
}
