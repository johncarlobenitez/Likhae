@extends('layouts.admin')
@section('title','Seller Compliance')
@section('subtitle','Product violations, warnings, and moderation history.')
@section('active','compliance')
@section('content')
<div style="display:grid;gap:16px"><div class="ad-page-head"><div><span class="ad-overline">Compliance</span><h2>Seller Compliance Cases</h2><p>Cases are created when an administrator suspends a product.</p></div><a class="ad-btn ad-btn-secondary" href="{{ route('admin.products') }}">Review Products</a></div><div class="ad-card"><div class="ad-table-wrap"><table class="ad-table"><thead><tr><th>Case</th><th>Seller</th><th>Product</th><th>Violation</th><th>Description</th><th>Status</th><th>Actions</th></tr></thead><tbody>@forelse($cases as $case)<tr><td>{{ $case->case_number }}</td><td>{{ $case->sellerProfile?->business_name }}</td><td>{{ $case->product?->name }}</td><td>{{ str($case->violation_type)->headline() }}</td><td>{{ $case->description }}</td><td>{{ str($case->status)->headline() }}</td><td>@foreach($case->actions as $action)<small style="display:block">{{ str($action->action_type)->headline() }} — {{ $action->reason }}</small>@endforeach</td></tr>@empty<tr><td colspan="7">No compliance cases.</td></tr>@endforelse</tbody></table></div><div class="ad-card-body">{{ $cases->links() }}</div></div></div>
@endsection
