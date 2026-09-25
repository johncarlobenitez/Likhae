@extends('layouts.buyer')
@section('content')
<div class="mx-auto max-w-3xl px-5 py-10"><p class="text-xs font-bold uppercase text-red-800">Courier partnership</p><h1 class="mt-2 text-3xl font-bold">Deliver with us</h1><p class="mt-2 text-stone-500">Company access is enabled only after administrator approval.</p>
@if($errors->any())<div class="my-5 border border-red-200 bg-red-50 p-4 text-sm text-red-800">{{ $errors->first() }}</div>@endif
<form class="mt-8 space-y-5 border border-stone-200 bg-white p-6 shadow-sm" method="POST" action="{{ route('partner.store') }}" enctype="multipart/form-data">@csrf
<label class="block"><span class="text-sm font-semibold">Company name</span><input class="mt-2 w-full border p-3" name="name" value="{{ old('name',$provider?->name) }}" required></label>
<label class="block"><span class="text-sm font-semibold">Contact phone</span><input class="mt-2 w-full border p-3" name="contact_phone" value="{{ old('contact_phone',$provider?->contact_phone) }}" required></label>
<label class="block"><span class="text-sm font-semibold">Business ID or permit</span><input class="mt-2 block w-full border p-3" type="file" name="document" accept="image/jpeg,image/png,application/pdf" required></label>
<button class="bg-red-900 px-5 py-3 font-semibold text-white">Submit for review</button></form></div>
@endsection
