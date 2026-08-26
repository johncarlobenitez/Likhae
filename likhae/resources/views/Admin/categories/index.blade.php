@extends('Admin.layouts.app')
@section('title', 'Category Management — LIKHAE Admin')

@section('content')
<div class="page-header">
    <div>
        <p class="page-eyebrow">CATALOG</p>
        <h1 class="page-title">Category Management</h1>
        <p class="page-description">Manage product categories and subcategories on the platform.</p>
    </div>
    <div class="flex gap-2">
        <button class="btn-primary" data-modal-open="addCategoryModal">+ Add Category</button>
    </div>
</div>

<div class="panel">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr><th>Category</th><th>Slug</th><th>Products</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @foreach([
                    ['Handicrafts & Decor', 'handicrafts-decor', 4820, 'Active'],
                    ['Food & Beverages',    'food-beverages',    3210, 'Active'],
                    ['Fashion & Bags',      'fashion-bags',      5640, 'Active'],
                    ['Home & Living',       'home-living',       3980, 'Active'],
                    ['Electronics',         'electronics',       2140, 'Active'],
                    ['Health & Beauty',     'health-beauty',     1980, 'Active'],
                    ['Toys & Hobbies',      'toys-hobbies',      1042, 'Inactive'],
                ] as [$name, $slug, $products, $status])
                    <tr>
                        <td><strong>{{ $name }}</strong></td>
                        <td class="text-[#96918B]">{{ $slug }}</td>
                        <td>{{ number_format($products) }}</td>
                        <td>
                            <span class="status-badge {{ $status === 'Active' ? 'status-success' : 'status-neutral' }}">
                                <span class="status-dot"></span>{{ $status }}
                            </span>
                        </td>
                        <td class="flex gap-2">
                            <button class="btn-secondary text-[10px]">Edit</button>
                            <button class="btn-danger text-[10px]">{{ $status === 'Active' ? 'Disable' : 'Enable' }}</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Add Category Modal --}}
<div id="addCategoryModal" class="modal-shell hidden">
    <div class="modal-card">
        <div class="modal-header">
            <div>
                <p class="panel-eyebrow">CATALOG</p>
                <h2>Add New Category</h2>
            </div>
            <button class="icon-button" data-modal-close="addCategoryModal">
                <svg viewBox="0 0 24 24"><path d="m6 6 12 12M18 6 6 18"/></svg>
            </button>
        </div>
        <div class="p-5">
            <form>
                @csrf
                <div class="form-grid">
                    <div>
                        <label class="form-label">Category Name <span class="text-[#D92D2F]">*</span></label>
                        <input type="text" class="form-input" placeholder="e.g. Handicrafts & Decor">
                    </div>
                    <div>
                        <label class="form-label">Slug <span class="text-[#D92D2F]">*</span></label>
                        <input type="text" class="form-input" placeholder="e.g. handicrafts-decor">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="form-label">Description</label>
                        <textarea class="form-input" rows="3" placeholder="Short description of this category..."></textarea>
                    </div>
                </div>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" class="btn-secondary" data-modal-close="addCategoryModal">Cancel</button>
                    <button type="submit" class="btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
