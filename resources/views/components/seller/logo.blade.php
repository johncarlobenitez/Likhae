<a {{ $attributes->merge(['class' => 'inline-flex items-center gap-2']) }} href="{{ url('/') }}">
    <x-likhae-logo :context="($seller ?? false) === true ? 'Seller Center' : null" class="likhae-logo--seller-component" />
</a>
