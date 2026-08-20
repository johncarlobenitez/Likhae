<div>
    <label for="{{ $name }}" class="form-label">{{ $label }} @if($required)<span>*</span>@endif</label>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type ?? 'text' }}" value="{{ old($name) }}" @if($required) required @endif @isset($maxlength) maxlength="{{ $maxlength }}" @endisset class="form-input">
</div>
