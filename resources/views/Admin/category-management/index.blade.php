@extends('layouts.admin')

@section('title', 'Category Management')
@section('subtitle', 'Manage Lines of Business and subcategories for product catalog organization.')
@section('active', 'categories')

@php
    $lineOfBusiness = $lineOfBusiness ?? [];
    $subcategories = $subcategories ?? [];
@endphp

@section('content')
<style>
    :root {
        --cat-bg: #FBF7F2;
        --cat-bg-soft: #F6EFE7;
        --cat-card: #FFFDF9;
        --cat-border: #EADCCC;
        --cat-maroon: #561C17;
        --cat-maroon-dark: #3E130F;
        --cat-text: #3B211B;
        --cat-muted: #987865;
        --cat-tan: #C19771;
        --cat-success: #256F4A;
        --cat-success-soft: #EAF7EF;
        --cat-warning: #9A5B11;
        --cat-warning-soft: #FFF6DE;
        --cat-shadow-soft: 0 8px 24px rgba(86, 28, 23, 0.055);
    }

    .ad-cat-page {
        color: var(--cat-text);
        display: grid;
        gap: 24px;
    }

    .ad-cat-page .ad-page-head {
        padding: 32px 36px;
        border: 1px solid var(--cat-border);
        border-radius: 28px;
        background: linear-gradient(135deg, #FFFDF9 0%, #F6EFE7 58%, #EFE7DE 100%);
        box-shadow: var(--cat-shadow-soft);
    }

    .ad-cat-page .ad-overline {
        color: var(--cat-maroon);
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .ad-cat-page .ad-page-head h2 {
        margin-top: 10px;
        color: var(--cat-text);
        font-family: "Instrument Serif", Georgia, serif;
        font-size: clamp(38px, 4.5vw, 64px);
        font-weight: 400;
        line-height: 0.95;
        letter-spacing: -0.055em;
    }

    .ad-cat-page .ad-page-head p {
        max-width: 660px;
        margin-top: 13px;
        color: var(--cat-muted);
        font-size: 13px;
        line-height: 1.7;
    }

    .ad-cat-page .ad-page-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 24px;
    }

    .ad-cat-page .ad-btn-primary {
        background: var(--cat-maroon);
        border-color: var(--cat-maroon);
        color: #FFFFFF;
        box-shadow: 0 10px 22px rgba(86, 28, 23, 0.16);
        border: 1px solid var(--cat-maroon);
        padding: 0 17px;
        min-height: 43px;
        border-radius: 13px;
        font-size: 12px;
        font-weight: 900;
        cursor: pointer;
    }

    .ad-cat-page .ad-btn-primary:hover {
        background: var(--cat-maroon-dark);
        border-color: var(--cat-maroon-dark);
    }

    .ad-cat-page .ad-btn-secondary {
        background: var(--cat-card);
        border: 1px solid var(--cat-tan);
        color: var(--cat-maroon);
        padding: 0 12px;
        min-height: 38px;
        border-radius: 13px;
        font-size: 12px;
        font-weight: 900;
        cursor: pointer;
    }

    .ad-cat-page .ad-btn-secondary:hover {
        background: #F3E4DE;
        border-color: var(--cat-maroon);
    }

    .ad-cat-page .ad-card {
        border: 1px solid var(--cat-border);
        border-radius: 24px;
        background: var(--cat-card);
        box-shadow: var(--cat-shadow-soft);
        overflow: hidden;
    }

    .ad-cat-page .ad-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        padding: 20px 22px;
        border-bottom: 1px solid var(--cat-border);
        background: linear-gradient(135deg, #FFFDF9 0%, #F8F0E8 100%);
    }

    .ad-cat-page .ad-card-head h2 {
        margin: 7px 0 0;
        color: var(--cat-text);
        font-size: 28px;
        font-weight: 400;
        line-height: 1;
        letter-spacing: -0.045em;
    }

    .ad-cat-page .ad-card-head p {
        margin: 8px 0 0;
        color: var(--cat-muted);
        font-size: 12px;
        line-height: 1.65;
    }

    .ad-cat-page .ad-card-body {
        padding: 22px;
    }

    .ad-cat-page .ad-line-of-business-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
    }

    .ad-cat-page .ad-lob-card {
        padding: 18px;
        border: 1px solid var(--cat-border);
        border-radius: 18px;
        background: var(--cat-card);
    }

    .ad-cat-page .ad-lob-card h4 {
        margin: 0 0 6px;
        color: var(--cat-text);
        font-size: 16px;
        font-weight: 900;
        letter-spacing: -0.03em;
    }

    .ad-cat-page .ad-lob-card p {
        margin: 0 0 12px;
        color: var(--cat-muted);
        font-size: 13px;
        line-height: 1.6;
    }

    .ad-cat-page .ad-lob-card small {
        display: block;
        color: var(--cat-muted);
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .ad-cat-page .ad-lob-card.is-inactive {
        opacity: 0.6;
    }

    .ad-cat-page .ad-lob-actions {
        margin-top: 12px;
        display: flex;
        gap: 8px;
    }

    .ad-cat-page .ad-subcat-list {
        display: grid;
        gap: 8px;
    }

    .ad-cat-page .ad-subcat-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 14px;
        border: 1px solid var(--cat-border);
        border-radius: 12px;
        background: var(--cat-card);
    }

    .ad-cat-page .ad-subcat-item.is-inactive {
        opacity: 0.6;
    }

    .ad-cat-page .ad-subcat-info {
        min-width: 0;
        flex: 1;
    }

    .ad-cat-page .ad-subcat-info strong {
        display: block;
        color: var(--cat-text);
        font-size: 13px;
        font-weight: 900;
    }

    .ad-cat-page .ad-subcat-info span {
        display: block;
        margin-top: 4px;
        color: var(--cat-muted);
        font-size: 11px;
    }

    .ad-cat-page .ad-row-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 7px;
    }

    .ad-cat-page .ad-btn-xs {
        padding: 0 10px;
        min-height: 32px;
        font-size: 11px;
    }

    .ad-cat-page .ad-btn-warning-soft {
        background: var(--cat-warning-soft);
        border: 1px solid #EAD39A;
        color: var(--cat-warning);
    }

    .ad-cat-page .ad-form-group {
        margin-bottom: 16px;
    }

    .ad-cat-page .ad-form-label {
        display: block;
        margin-bottom: 6px;
        color: var(--cat-text);
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .ad-cat-page .ad-form-input {
        width: 100%;
        min-height: 42px;
        padding: 0 14px;
        border: 1px solid var(--cat-border);
        border-radius: 14px;
        background: var(--cat-bg-soft);
        color: var(--cat-text);
        font-size: 12px;
        font-weight: 700;
        outline: none;
    }

    .ad-cat-page .ad-form-input:focus {
        border-color: var(--cat-tan);
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(86, 28, 23, 0.08);
    }

    .ad-cat-page .ad-form-textarea {
        min-height: 100px;
        padding: 12px;
        resize: vertical;
    }

    .ad-cat-page .ad-btn-bar {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 8px;
    }

    .ad-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .ad-modal.show {
        display: flex;
    }

    .ad-modal-content {
        background: #FFFDF9;
        border: 1px solid #EADCCC;
        border-radius: 24px;
        padding: 32px;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .ad-modal-content h3 {
        margin: 0 0 24px;
        color: #3B211B;
        font-size: 24px;
        font-weight: 900;
    }

    .ad-note {
        padding: 14px 16px;
        border: 1px solid var(--cat-border);
        border-radius: 16px;
        background: var(--cat-bg-soft);
        color: var(--cat-muted);
        font-size: 12px;
        line-height: 1.6;
        margin-bottom: 24px;
    }

    @media (max-width: 768px) {
        .ad-cat-page .ad-line-of-business-grid {
            grid-template-columns: 1fr;
        }

        .ad-cat-page .ad-subcat-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .ad-cat-page .ad-row-actions {
            width: 100%;
            justify-content: flex-end;
        }
    }
</style>

<div class="ad-page ad-cat-page">
    <div class="ad-page-head">
        <div>
            <span class="ad-overline">Catalog organization</span>
            <h2>Category Management</h2>
            <p>Manage Lines of Business and subcategories. Sellers will use these when listing products.</p>
        </div>
        <button type="button" class="ad-btn ad-btn-primary" onclick="openModal('lob')">
            + Add Line of Business
        </button>
    </div>

    @if(session('status'))
        <div class="ad-note" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <!-- Lines of Business Section -->
    <div class="ad-card">
        <header class="ad-card-head">
            <div>
                <span class="ad-overline">Main categories</span>
                <h2>Lines of Business</h2>
                <p>{{ count($lineOfBusiness) }} top-level categories</p>
            </div>
        </header>
        <div class="ad-card-body">
            @if(count($lineOfBusiness) > 0)
                <div class="ad-line-of-business-grid">
                    @foreach($lineOfBusiness as $lob)
                        @php
                            $subcatsForLob = $subcategories->where('parent_id', $lob->id);
                            $isActive = (bool) $lob->is_active;
                        @endphp
                        <article class="ad-lob-card {{ $isActive ? '' : 'is-inactive' }}">
                            <h4>{{ $lob->name }}</h4>
                            @if($lob->description)
                                <p>{{ $lob->description }}</p>
                            @endif
                            <small>{{ $subcatsForLob->count() }} subcategories</small>
                            <div class="ad-lob-actions">
                                <button type="button" class="ad-btn ad-btn-secondary ad-btn-xs"
                                    onclick="openAddSubcatModal({{ $lob->id }}, '{{ $lob->name }}')">
                                    + Add Subcategory
                                </button>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <p style="color: var(--cat-muted);">No Lines of Business defined yet. Add one to begin organizing your product catalog.</p>
            @endif
        </div>
    </div>

    <!-- Subcategories Section -->
    <div class="ad-card">
        <header class="ad-card-head">
            <div>
                <span class="ad-overline">Subcategories</span>
                <h2>Subcategories</h2>
                <p>{{ count($subcategories) }} subcategories organized under Lines of Business</p>
            </div>
        </header>
        <div class="ad-card-body">
            @if(count($subcategories) > 0)
                <div class="ad-subcat-list">
                    @foreach($subcategories as $subcat)
                        @php
                            $parent = $lineOfBusiness->firstWhere('id', $subcat->parent_id);
                            $isActive = (bool) $subcat->is_active;
                        @endphp
                        <div class="ad-subcat-item {{ $isActive ? '' : 'is-inactive' }}">
                            <div class="ad-subcat-info">
                                <strong>{{ $subcat->name }}</strong>
                                @if($parent)
                                    <span>Under: {{ $parent->name }}</span>
                                @endif
                            </div>
                            <div class="ad-row-actions">
                                <button type="button" class="ad-btn ad-btn-secondary ad-btn-xs"
                                    onclick="openEditSubcatModal({{ $subcat->id }}, '{{ $subcat->name }}', '{{ $subcat->description ?? '' }}', {{ $subcat->is_active ? 1 : 0 }})">
                                    Edit
                                </button>
                                @if($isActive)
                                    <form method="POST" action="{{ route('admin.subcategories.status', $subcat) }}" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="is_active" value="0">
                                        <button type="submit" class="ad-btn ad-btn-warning-soft ad-btn-xs"
                                            onclick="return confirm('Deactivate this subcategory?');">
                                            Deactivate
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.subcategories.status', $subcat) }}" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="is_active" value="1">
                                        <button type="submit" class="ad-btn ad-btn-primary ad-btn-xs">
                                            Activate
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="color: var(--cat-muted);">No subcategories defined yet. Add subcategories to Lines of Business to help sellers categorize products.</p>
            @endif
        </div>
    </div>
</div>

<!-- Add Line of Business Modal -->
<div class="ad-modal" id="lob-modal">
    <div class="ad-modal-content">
        <h3>Add Line of Business</h3>
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <div class="ad-form-group">
                <label class="ad-form-label">Name</label>
                <input class="ad-form-input" name="name" type="text" required
                    placeholder="e.g., Electronics, Fashion, Furniture">
            </div>
            <div class="ad-form-group">
                <label class="ad-form-label">Description (optional)</label>
                <textarea class="ad-form-input ad-form-textarea" name="description"
                    placeholder="Brief description of this category..."></textarea>
            </div>
            <div class="ad-btn-bar">
                <button type="button" class="ad-btn ad-btn-secondary" onclick="closeModal('lob')">Cancel</button>
                <button type="submit" class="ad-btn ad-btn-primary">Add Line of Business</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Subcategory Modal -->
<div class="ad-modal" id="add-subcat-modal">
    <div class="ad-modal-content">
        <h3>Add Subcategory</h3>
        <p style="margin: 0 0 24px; color: var(--cat-muted);">Under: <strong id="parent-name" style="color: var(--cat-text);"></strong></p>
        <form method="POST" action="{{ route('admin.subcategories.store') }}">
            @csrf
            <input type="hidden" id="parent-id" name="category_id">
            <div class="ad-form-group">
                <label class="ad-form-label">Name</label>
                <input class="ad-form-input" name="name" type="text" required
                    placeholder="e.g., Smartphones, Tablets, Smartwatches">
            </div>
            <div class="ad-form-group">
                <label class="ad-form-label">Description (optional)</label>
                <textarea class="ad-form-input ad-form-textarea" name="description"
                    placeholder="Brief description of this subcategory..."></textarea>
            </div>
            <div class="ad-btn-bar">
                <button type="button" class="ad-btn ad-btn-secondary" onclick="closeModal('add-subcat')">Cancel</button>
                <button type="submit" class="ad-btn ad-btn-primary">Add Subcategory</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Subcategory Modal -->
<div class="ad-modal" id="edit-subcat-modal">
    <div class="ad-modal-content">
        <h3>Edit Subcategory</h3>
        <form method="POST" id="edit-subcat-form">
            @csrf @method('PATCH')
            <div class="ad-form-group">
                <label class="ad-form-label">Name</label>
                <input class="ad-form-input" id="edit-name" name="name" type="text" required>
            </div>
            <div class="ad-form-group">
                <label class="ad-form-label">Description (optional)</label>
                <textarea class="ad-form-input ad-form-textarea" id="edit-description" name="description"></textarea>
            </div>
            <div class="ad-form-group">
                <label class="ad-form-label">Status</label>
                <select class="ad-form-input" id="edit-active" name="is_active">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <div class="ad-btn-bar">
                <button type="button" class="ad-btn ad-btn-secondary" onclick="closeModal('edit-subcat')">Cancel</button>
                <button type="submit" class="ad-btn ad-btn-primary">Update Subcategory</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(modalName) {
    document.getElementById(modalName + '-modal').classList.add('show');
}

function closeModal(modalName) {
    document.getElementById(modalName + '-modal').classList.remove('show');
}

function openAddSubcatModal(parentId, parentName) {
    document.getElementById('parent-id').value = parentId;
    document.getElementById('parent-name').textContent = parentName;
    openModal('add-subcat');
}

function openEditSubcatModal(id, name, description, isActive) {
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-description').value = description;
    document.getElementById('edit-active').value = isActive;
    document.getElementById('edit-subcat-form').action = '/admin/subcategories/' + id;
    openModal('edit-subcat');
}

// Close modal on outside click
document.querySelectorAll('.ad-modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('show');
        }
    });
});
</script>

@endsection