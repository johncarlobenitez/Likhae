@extends('layouts.admin')

@section('title', 'Notifications')
@section('subtitle', 'Review risk, operations, finance, and system alerts in one prioritized inbox.')
@section('active', 'notifications')

@section('content')
@php
    $notifications = [
        ['type' => 'Risk', 'title' => '5 high-confidence product signals need review', 'message' => 'The prohibited-item monitor identified possible weapons or controlled products.', 'time' => '8 min ago', 'tone' => 'danger', 'href' => route('admin.products', ['view' => 'monitor'])],
        ['type' => 'Dispute', 'title' => 'Urgent courier conduct complaint', 'message' => 'Case DSP-1880 was escalated and requires an administrator assignment.', 'time' => '18 min ago', 'tone' => 'warning', 'href' => route('admin.complaints')],
        ['type' => 'Registration', 'title' => '7 applications exceeded the review target', 'message' => 'Buyer, seller, and logistics-center documents are ready for review.', 'time' => '42 min ago', 'tone' => 'warning', 'href' => route('admin.registrations')],
        ['type' => 'Finance', 'title' => 'Settlement TXN-980138 failed', 'message' => 'The payment provider returned a timeout. No duplicate payout was created.', 'time' => '1 hr ago', 'tone' => 'danger', 'href' => route('admin.finance', ['tab' => 'payments'])],
        ['type' => 'System', 'title' => 'Restricted products policy published', 'message' => 'Revision 3.2 is now visible to sellers and used during review.', 'time' => 'Yesterday', 'tone' => 'blue', 'href' => route('admin.settings', ['tab' => 'policies'])],
    ];
@endphp
<div class="ad-page">
    <div class="ad-page-head"><div><span class="ad-overline">Admin inbox</span><h2>Operational alerts</h2><p>Critical risk and enforcement notices stay visually distinct from routine updates.</p></div><div class="ad-inline-actions"><button class="ad-btn ad-btn-secondary" type="button" data-demo-action="All notifications marked read">Mark all as read</button><a class="ad-btn ad-btn-primary" href="{{ route('admin.account', ['tab' => 'notifications']) }}">Preferences</a></div></div>
    <section class="ad-card"><div class="ad-tabs" style="margin:12px"><button class="ad-tab is-active" type="button">All <b>8</b></button><button class="ad-tab" type="button">Risk</button><button class="ad-tab" type="button">Operations</button><button class="ad-tab" type="button">Finance</button><button class="ad-tab" type="button">System</button></div><div class="ad-settings-section" style="padding-top:4px">@foreach($notifications as $notification)<a href="{{ $notification['href'] }}" class="ad-setting-row"><span class="ad-stat-icon" style="color:{{ $notification['tone'] === 'danger' ? '#dc2626' : ($notification['tone'] === 'warning' ? '#b45309' : '#2563eb') }};background:{{ $notification['tone'] === 'danger' ? '#fef2f2' : ($notification['tone'] === 'warning' ? '#fffbeb' : '#eff6ff') }}">!</span><span style="flex:1"><span class="ad-overline">{{ $notification['type'] }}</span><strong style="margin-top:4px">{{ $notification['title'] }}</strong><p>{{ $notification['message'] }}</p></span><small class="ad-muted">{{ $notification['time'] }}</small><span>→</span></a>@endforeach</div></section>
</div>
@endsection
