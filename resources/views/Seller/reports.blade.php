@extends('layouts.seller')

@php $currentReport = $report ?? request('report','sales'); @endphp

@section('title','Reports')
@section('active','reports')
@section('subtitle','Generate focused reports for sales, profit, orders, and products.')

@section('content')
<div class="sl-page">
    <div class="sl-page-toolbar"><div><span class="sl-eyebrow">Business Intelligence</span><h2>Report Center</h2><p>Choose a report type and date range, then download the current database results.</p></div></div>
    <section class="sl-stat-grid sl-stat-grid-4">
        <x-seller.stat-card label="Orders in Range" :value="$reportSummary['orders']" icon="orders" />
        <x-seller.stat-card label="Gross Sales" :value="'₱'.number_format($reportSummary['gross'],2)" icon="sales" />
        <x-seller.stat-card label="Commission" :value="'₱'.number_format($reportSummary['commission'],2)" icon="finance" />
        <x-seller.stat-card label="Net" :value="'₱'.number_format($reportSummary['net'],2)" icon="revenue" />
    </section>
    <section class="sl-card sl-report-builder">
        <div class="sl-report-types">
            @foreach(['sales'=>['Sales Report','Revenue and order totals'],'profit'=>['Profit Report','Commission and net earnings'],'orders'=>['Order Report','Fulfillment counts'],'products'=>['Product Performance','Sold units by products']] as $key=>$item)
                <a href="{{ route('seller.reports',['report'=>$key,'from'=>$reportFrom->toDateString(),'to'=>$reportTo->toDateString()]) }}" class="{{ $currentReport===$key?'is-active':'' }}"><span><svg viewBox="0 0 24 24"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg></span><div><strong>{{ $item[0] }}</strong><small>{{ $item[1] }}</small></div><i>→</i></a>
            @endforeach
        </div>
        <form class="sl-report-controls" method="GET" action="{{ route('seller.reports.download') }}">
            <input type="hidden" name="report" value="{{ $currentReport }}">
            <div><span class="sl-eyebrow">Report Configuration</span><h2>{{ str($currentReport)->headline() }} Report</h2><p>CSV output opens cleanly in Excel or Google Sheets.</p></div>
            <div class="sl-form-grid"><label class="sl-field"><span>From Date</span><input name="from" type="date" value="{{ $reportFrom->toDateString() }}" required></label><label class="sl-field"><span>To Date</span><input name="to" type="date" value="{{ $reportTo->toDateString() }}" required></label><label class="sl-field"><span>Output</span><select disabled><option>CSV spreadsheet</option></select></label></div>
            <button type="submit" class="sl-btn sl-btn-primary sl-btn-block">Generate & Download Report</button>
        </form>
    </section>
    <section class="sl-card"><header class="sl-card-head"><div><span class="sl-eyebrow">Range Summary</span><h2>{{ $reportFrom->format('M d, Y') }} – {{ $reportTo->format('M d, Y') }}</h2><p>Current values update whenever the date range changes.</p></div></header><div class="sl-mini-stats"><div><span>Completed</span><strong>{{ $reportSummary['completed'] }}</strong></div><div><span>Cancelled</span><strong>{{ $reportSummary['cancelled'] }}</strong></div><div><span>Gross</span><strong>₱{{ number_format($reportSummary['gross'],2) }}</strong></div><div><span>Net</span><strong>₱{{ number_format($reportSummary['net'],2) }}</strong></div></div></section>
</div>
@endsection
