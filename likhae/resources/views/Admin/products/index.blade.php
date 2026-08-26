@extends('Admin.layouts.app')
@section('title', 'Product Management — LIKHAE Admin')

@section('content')
<div class="page-header">
    <div>
        <p class="page-eyebrow">CATALOG</p>
        <h1 class="page-title">Product Management</h1>
        <p class="page-description">Monitor, review, and manage all products listed on the platform.</p>
    </div>
</div>

<div class="admin-stat-grid">
    @foreach([['TOTAL PRODUCTS','24,812',''],['ACTIVE','22,340','text-[#079B72]'],['FLAGGED','48','text-[#D92D2F]'],['OUT OF STOCK','424','text-[#D99A22]']] as [$label,$val,$tone])
        <div class="kpi-card">
            <p class="kpi-label">{{ $label }}</p>
            <strong class="mt-3 block text-2xl font-black tracking-[-.04em] {{ $tone }}">{{ $val }}</strong>
        </div>
    @endforeach
</div>

<div class="filter-bar mt-5">
    <div class="search-field flex-1">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <input type="search" placeholder="Search by product name, SKU, or seller...">
    </div>
    <select class="filter-input">
        <option>All Categories</option>
        <option>Handicrafts</option>
        <option>Food & Beverages</option>
        <option>Fashion</option>
        <option>Home & Living</option>
    </select>
    <select class="filter-input">
        <option>All Status</option>
        <option>Active</option>
        <option>Flagged</option>
        <option>Out of Stock</option>
    </select>
</div>

<div class="tabs-row mt-0 border-t-0">
    @foreach(['All (24,812)','Active (22,340)','Flagged (48)','Out of Stock (424)'] as $i => $tab)
        <button class="tab-button {{ $i === 0 ? 'is-active' : '' }}">{{ $tab }}</button>
    @endforeach
</div>

<div class="panel">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr><th>Product</th><th>SKU</th><th>Seller</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @foreach([
                    ['Baseus Wireless Earbuds',   'BAS-A3I-BLK', "Maria's Finds",  'Electronics',   '₱1,299', 42,  'Active'],
                    ['Capiz Shell Pendant Lamp',  'CAP-PEN-WHT', 'Capiz Crafts',   'Home & Living', '₱2,499', 8,   'Active'],
                    ['Rattan Tote Bag',           'RAT-TOTE-NAT','Rattan Co.',     'Fashion',       '₱850',   3,   'Active'],
                    ['Tablea Gift Pack 250g',     'TAB-250-BX',  'Tablea Shop',    'Food',          '₱480',   0,   'Out of Stock'],
                    ['Fake Branded Bag',          'FKE-BAG-001', 'Unknown Seller', 'Fashion',       '₱299',   100, 'Flagged'],
                ] as [$name, $sku, $seller, $category, $price, $stock, $status])
                    <tr>
                        <td><strong>{{ $name }}</strong></td>
                        <td class="text-[#96918B]">{{ $sku }}</td>
                        <td>{{ $seller }}</td>
                        <td>{{ $category }}</td>
                        <td>{{ $price }}</td>
                        <td>{{ $stock }}</td>
                        <td>
                            @php $tone = match($status) { 'Active' => 'status-success', 'Flagged' => 'status-danger', default => 'status-warning' }; @endphp
                            <span class="status-badge {{ $tone }}"><span class="status-dot"></span>{{ $status }}</span>
                        </td>
                        <td class="flex gap-2">
                            <button class="btn-secondary text-[10px]">View</button>
                            @if($status === 'Flagged')
                                <button class="btn-danger text-[10px]">Remove</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-row">
        <span>Showing 1–5 of 24,812 products</span>
        <div><button>‹</button><button class="is-active">1</button><button>2</button><button>3</button><button>›</button></div>
    </div>
</div>
@endsection
