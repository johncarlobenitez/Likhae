@extends('layouts.seller')

@php $currentReport = $report ?? request('report', 'sales'); @endphp

@section('title', 'Reports')
@section('active', 'reports')
@section('subtitle', 'Generate focused reports for sales, profit, orders, and products.')

@section('content')
<div class="sl-page">
    <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Business Intelligence</span><h2>Report Center</h2><p>Choose a report type and date range to analyze store performance.</p></div><button type="button" class="sl-btn sl-btn-ghost" data-demo-action="Report schedule opened.">Schedule Report</button></div>
    <section class="sl-card sl-report-builder">
        <div class="sl-report-types">
            @foreach (['sales' => ['Sales Report','Revenue, discounts, and net sales'], 'profit' => ['Profit Report','Margins, fees, and earnings'], 'orders' => ['Order Report','Volume and fulfillment performance'], 'products' => ['Product Performance','Views, conversion, and sold units']] as $key => $item)
                <a href="{{ route('seller.reports', ['report' => $key]) }}" class="{{ $currentReport === $key ? 'is-active' : '' }}"><span><svg viewBox="0 0 24 24"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg></span><div><strong>{{ $item[0] }}</strong><small>{{ $item[1] }}</small></div><i>→</i></a>
            @endforeach
        </div>
        <form class="sl-report-controls" data-demo-form data-success="{{ ucfirst($currentReport) }} report generated.">
            <div><span class="sl-eyebrow">Report Configuration</span><h2>{{ ['sales'=>'Sales Report','profit'=>'Profit Report','orders'=>'Order Report','products'=>'Product Performance'][$currentReport] ?? 'Sales Report' }}</h2><p>Set the reporting period and output format.</p></div>
            <div class="sl-form-grid"><label class="sl-field"><span>From Date</span><input type="date" value="2026-08-01" required></label><label class="sl-field"><span>To Date</span><input type="date" value="2026-09-04" required></label><label class="sl-field"><span>Compare With</span><select><option>Previous period</option><option>Same period last year</option><option>No comparison</option></select></label><label class="sl-field"><span>Format</span><select><option>On-screen summary</option><option>PDF document</option><option>CSV spreadsheet</option></select></label></div>
            <div class="sl-report-options"><label><input type="checkbox" checked> Include summary metrics</label><label><input type="checkbox" checked> Include detailed table</label><label><input type="checkbox" checked> Include visual charts</label></div>
            <button type="submit" class="sl-btn sl-btn-primary sl-btn-block">Generate Report</button>
        </form>
    </section>
    <section class="sl-card"><header class="sl-card-head"><div><span class="sl-eyebrow">Recent Exports</span><h2>Generated Reports</h2><p>Download reports created during the last 30 days.</p></div></header><div class="sl-report-history">@foreach ([['August Sales Report','Aug 01–31, 2026','PDF','Sep 01, 2026'],['August Profit Report','Aug 01–31, 2026','XLSX','Sep 01, 2026'],['Weekly Order Report','Aug 25–31, 2026','CSV','Aug 31, 2026']] as $history)<article><span class="sl-file-icon">{{ $history[2] }}</span><div><strong>{{ $history[0] }}</strong><small>{{ $history[1] }} · Generated {{ $history[3] }}</small></div><button type="button" class="sl-btn sl-btn-ghost sl-btn-sm" data-demo-action="{{ $history[0] }} downloaded.">Download</button></article>@endforeach</div></section>
</div>
@endsection
