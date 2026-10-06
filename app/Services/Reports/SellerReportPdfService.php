<?php

namespace App\Services\Reports;

/** Creates a compact, dependency-free PDF report using the PDF core fonts. */
class SellerReportPdfService
{
    private const PAGE_WIDTH = 612.0;
    private const PAGE_HEIGHT = 792.0;
    private array $pages = [];
    private array $commands = [];
    private float $cursorY = 0.0;

    public function render(array $report): string
    {
        $this->pages = [];
        $this->commands = [];
        $this->newPage($report, false);
        $this->text(42, 684, 'BUSINESS INTELLIGENCE', 8, 'FDF5F1', 'F2');
        $this->text(42, 658, $report['title'], 21, 'FFFFFF', 'F2');
        $this->text(42, 639, $report['scope'].' | '.$report['range_label'], 9, 'F7EDEA');
        $this->text(42, 619, 'Generated '.$report['generated_at'], 8, 'DAB9B0');

        $this->sectionHeading('Performance at a glance', $report);
        $summary = $report['summary'];
        $this->metricCard(42, $this->cursorY - 48, 'Gross sales', $this->money($summary['gross']));
        $this->metricCard(180, $this->cursorY - 48, 'Net earnings', $this->money($summary['net']));
        $this->metricCard(318, $this->cursorY - 48, 'Orders', (string) $summary['orders']);
        $this->metricCard(456, $this->cursorY - 48, 'Completion', $summary['completion_rate'].'%');
        $this->cursorY -= 78;

        $this->sectionHeading('Catalog performance mix', $report);
        $mix = $report['performance_counts'];
        $this->text(42, $this->cursorY, 'High performers', 9, '4D1712', 'F2');
        $this->text(154, $this->cursorY, (string) $mix['high'], 15, '641F19', 'F2');
        $this->text(222, $this->cursorY, 'Steady sellers', 9, '4D1712', 'F2');
        $this->text(325, $this->cursorY, (string) $mix['mid'], 15, '641F19', 'F2');
        $this->text(390, $this->cursorY, 'Low / no sales', 9, '4D1712', 'F2');
        $this->text(506, $this->cursorY, (string) ($mix['low'] + $mix['no_sales']), 15, '641F19', 'F2');
        $this->cursorY -= 24;
        $this->line(42, $this->cursorY, 570, $this->cursorY, 'E8D8C8');
        $this->cursorY -= 20;

        $this->sectionHeading('What to act on', $report);
        foreach ($report['insights'] as $insight) {
            $this->ensureSpace(20, $report);
            $this->fillRect(42, $this->cursorY - 3, 4, 4, 'B86A5B');
            $this->text(55, $this->cursorY - 6, $insight, 9, '4D3329');
            $this->cursorY -= 19;
        }
        $this->cursorY -= 8;

        $this->sectionHeading('Product detail', $report);
        $this->tableHeader();
        foreach ($report['products'] as $product) {
            $this->ensureSpace(25, $report);
            $this->productRow($product);
        }
        $this->cursorY -= 4;
        $this->ensureSpace(30, $report);
        $this->text(42, $this->cursorY, 'Method: product revenue and units use completed orders in the selected date range. Gross sales includes all orders created in range.', 7, '80675A');
        $this->text(42, $this->cursorY - 12, 'High and low labels compare products in this report scope. Products without completed sales are shown separately.', 7, '80675A');

        return $this->compile();
    }

    private function newPage(array $report, bool $continued): void
    {
        if ($this->commands !== []) {
            $this->pages[] = implode("\n", $this->commands);
        }
        $this->commands = [];
        $this->fillRect(0, 0, self::PAGE_WIDTH, self::PAGE_HEIGHT, 'FFFDF9');
        $this->fillRect(0, 594, self::PAGE_WIDTH, 198, '641F19');
        $this->text(42, 754, 'LIKHAE', 10, 'FFFFFF', 'F2');
        $this->text(486, 754, 'CONFIDENTIAL', 7, 'DAB9B0', 'F2');
        $this->cursorY = $continued ? 548 : 574;
        if ($continued) {
            $this->text(42, 558, $report['title'].' - Product detail (continued)', 13, 'FFFFFF', 'F2');
            $this->tableHeader();
        }
    }

    private function sectionHeading(string $value, array $report): void
    {
        $this->ensureSpace(30, $report);
        $this->text(42, $this->cursorY, strtoupper($value), 8, '9A6558', 'F2');
        $this->cursorY -= 17;
    }

    private function metricCard(float $x, float $y, string $label, string $value): void
    {
        $this->fillRect($x, $y, 124, 55, 'FAF3EC');
        $this->text($x + 10, $y + 39, strtoupper($label), 6.5, '9A6558', 'F2');
        $this->text($x + 10, $y + 19, $value, 13, '4D1712', 'F2');
    }

    private function tableHeader(): void
    {
        $this->fillRect(42, $this->cursorY - 3, 528, 19, 'F3E4DE');
        $this->text(51, $this->cursorY + 4, 'PRODUCT', 7, '641F19', 'F2');
        $this->text(292, $this->cursorY + 4, 'TIER', 7, '641F19', 'F2');
        $this->text(360, $this->cursorY + 4, 'UNITS', 7, '641F19', 'F2');
        $this->text(422, $this->cursorY + 4, 'ORDERS', 7, '641F19', 'F2');
        $this->text(487, $this->cursorY + 4, 'REVENUE', 7, '641F19', 'F2');
        $this->cursorY -= 24;
    }

    private function productRow(array $product): void
    {
        $this->line(42, $this->cursorY - 3, 570, $this->cursorY - 3, 'E8D8C8');
        $this->text(51, $this->cursorY + 4, $this->truncate($product['name'], 38), 8.5, '4D3329', 'F2');
        $this->text(292, $this->cursorY + 4, strtoupper(str_replace('_', ' ', $product['tier'])), 7, $this->tierColor($product['tier']), 'F2');
        $this->text(370, $this->cursorY + 4, (string) $product['units'], 8.5, '4D3329');
        $this->text(433, $this->cursorY + 4, (string) $product['orders'], 8.5, '4D3329');
        $this->text(487, $this->cursorY + 4, $this->money($product['revenue']), 8.5, '4D3329', 'F2');
        $this->cursorY -= 24;
    }

    private function ensureSpace(float $height, array $report): void
    {
        if ($this->cursorY - $height >= 58) {
            return;
        }
        $this->newPage($report, true);
    }

    private function text(float $x, float $y, string $value, float $size, string $hex, string $font = 'F1'): void
    {
        $this->commands[] = sprintf('%s rg BT /%s %.2F Tf 1 0 0 1 %.2F %.2F Tm (%s) Tj ET', $this->color($hex), $font, $size, $x, $y, $this->pdfString($value));
    }

    private function fillRect(float $x, float $y, float $width, float $height, string $hex): void
    {
        $this->commands[] = sprintf('%s rg %.2F %.2F %.2F %.2F re f', $this->color($hex), $x, $y, $width, $height);
    }

    private function line(float $x1, float $y1, float $x2, float $y2, string $hex): void
    {
        $this->commands[] = sprintf('%s RG 0.50 w %.2F %.2F m %.2F %.2F l S', $this->color($hex), $x1, $y1, $x2, $y2);
    }

    private function compile(): string
    {
        $this->pages[] = implode("\n", $this->commands);
        $objects = [1 => '<< /Type /Catalog /Pages 2 0 R >>', 2 => ''];
        $pageIds = [];
        $pageCount = count($this->pages);
        $fontRegular = 3 + ($pageCount * 2);
        $fontBold = $fontRegular + 1;
        foreach ($this->pages as $index => $stream) {
            $pageId = 3 + ($index * 2);
            $contentId = $pageId + 1;
            $pageIds[] = $pageId;
            $objects[$pageId] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 '.$fontRegular.' 0 R /F2 '.$fontBold.' 0 R >> >> /Contents '.$contentId.' 0 R >>';
            $objects[$contentId] = '<< /Length '.strlen($stream).' >>' . "\nstream\n" . $stream . "\nendstream";
        }
        $objects[2] = '<< /Type /Pages /Kids ['.implode(' ', array_map(fn (int $id): string => $id.' 0 R', $pageIds)).'] /Count '.$pageCount.' >>';
        $objects[$fontRegular] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        $objects[$fontBold] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>';
        ksort($objects);
        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [0];
        foreach ($objects as $id => $object) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id." 0 obj\n".$object."\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= 'xref' . "\n0 ".(count($objects) + 1)."\n0000000000 65535 f \n";
        foreach (array_keys($objects) as $id) {
            $pdf .= sprintf('%010d 00000 n ', $offsets[$id])."\n";
        }
        return $pdf.'trailer << /Size '.(count($objects) + 1).' /Root 1 0 R >>' . "\nstartxref\n".$xref."\n%%EOF";
    }

    private function money(float|int $value): string { return 'PHP '.number_format((float) $value, 2); }
    private function tierColor(string $tier): string { return match ($tier) { 'high' => '236B45', 'mid' => '7C5C12', 'low' => 'A34A34', default => '80675A' }; }
    private function truncate(string $value, int $length): string { return mb_strimwidth($value, 0, $length, '...'); }
    private function pdfString(string $value): string { $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value; return str_replace(['\\', '(', ')', "\r", "\n"], ['\\\\', '\\(', '\\)', '', ' '], $ascii); }
    private function color(string $hex): string { return implode(' ', array_map(fn (string $channel): string => number_format(hexdec($channel) / 255, 3, '.', ''), str_split($hex, 2))); }
}
