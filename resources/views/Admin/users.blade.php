@extends('layouts.admin')

@section('title', 'User Management')
@section('subtitle', 'Find registered accounts, review their details, and manage access.')
@section('active', 'users')

@section('content')
<div class="ad-page">
    <div class="ad-page-head">
        <div><span class="ad-overline">Platform accounts</span><h2>User directory</h2><p>Search registered accounts and open a profile to review or manage access.</p></div>
        <a class="ad-btn ad-btn-primary" href="{{ route('admin.registrations') }}">Review registrations</a>
    </div>

    @if($errors->any())
        <div class="ad-note" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
    @endif

    <section class="ad-summary-grid" aria-label="Account totals">
        <div class="ad-mini-stat"><span>Total users</span><strong>{{ number_format($stats['total']) }}</strong><small>Includes administrators</small></div>
        <div class="ad-mini-stat"><span>Buyers</span><strong>{{ number_format($stats['buyers']) }}</strong></div>
        <div class="ad-mini-stat"><span>Sellers</span><strong>{{ number_format($stats['sellers']) }}</strong></div>
        <div class="ad-mini-stat"><span>Logistics & riders</span><strong>{{ number_format($stats['delivery']) }}</strong></div>
    </section>

    <nav class="ad-tabs" aria-label="User role">
        @foreach($roles as $key => $label)
            <a class="ad-tab {{ $role === $key ? 'is-active' : '' }}" @if($role === $key) aria-current="page" @endif href="{{ route('admin.users', ['role' => $key, 'status' => $status, 'q' => $search]) }}">{{ $label }}</a>
        @endforeach
    </nav>

    <section class="ad-card" id="user-table">
        <form class="ad-filter-bar" action="{{ route('admin.users') }}" method="GET" role="search">
            <input type="hidden" name="role" value="{{ $role }}">
            <label class="ad-filter-search">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input name="q" type="search" value="{{ $search }}" maxlength="100" placeholder="Search name, email, or user ID" aria-label="Search name, email, or user ID">
            </label>
            <div class="ad-inline-actions">
                <select class="ad-select" name="status" aria-label="Account status">
                    @foreach($statuses as $key => $label)<option value="{{ $key }}" @selected($status === $key)>{{ $label }}</option>@endforeach
                </select>
                <button class="ad-btn ad-btn-primary ad-btn-sm" type="submit">Search</button>
                <a class="ad-btn ad-btn-secondary ad-btn-sm" href="{{ route('admin.users') }}">Clear</a>
            </div>
        </form>
        <div class="ad-table-wrap">
            <table class="ad-table">
                <thead><tr><th>User</th><th>Role</th><th>Joined</th><th>Status</th><th style="text-align:right">Actions</th></tr></thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td><div class="ad-cell-user"><span class="ad-avatar is-soft">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span><span><strong>{{ $user->name }}</strong><small>USR-{{ $user->id }} · {{ $user->email }}</small></span></div></td>
                            <td>{{ in_array($user->role, ['rider', 'courier']) ? 'Rider' : ucfirst($user->role) }}</td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td><span class="ad-status is-{{ $user->status }}">{{ $user->status === 'pending' ? 'Pending approval' : ucfirst($user->status) }}</span></td>
                            <td><div class="ad-row-actions"><a class="ad-btn ad-btn-secondary ad-btn-sm" href="{{ route('admin.users.show', $user) }}">View account</a></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No accounts match your search.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="ad-pagination">
            <span>Showing {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} of {{ $users->total() }} accounts</span>
            <nav aria-label="User pages">
                @if($users->onFirstPage())<span class="ad-page-btn" aria-disabled="true">Previous</span>@else<a class="ad-page-btn" rel="prev" href="{{ $users->previousPageUrl() }}">Previous</a>@endif
                <span class="ad-page-btn is-active" aria-current="page">{{ $users->currentPage() }}</span>
                @if($users->hasMorePages())<a class="ad-page-btn" rel="next" href="{{ $users->nextPageUrl() }}">Next</a>@else<span class="ad-page-btn" aria-disabled="true">Next</span>@endif
            </nav>
        </div>
    </section>
</div>
@endsection
