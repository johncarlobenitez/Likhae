@extends('layouts.buyer')
@section('content')
<div class="mx-auto max-w-3xl px-5 py-10"><div class="mb-8"><p class="text-xs font-bold uppercase text-red-800">Seller application</p><h1 class="mt-2 text-3xl font-bold">Open a shop</h1><p class="mt-2 text-stone-500">Your shop stays private until an administrator approves it.</p></div>
@if($errors->any())<div class="mb-5 border border-red-200 bg-red-50 p-4 text-sm text-red-800">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('sell.store') }}" enctype="multipart/form-data" class="space-y-5 border border-stone-200 bg-white p-6 shadow-sm">@csrf
<label class="block"><span class="text-sm font-semibold">Shop name</span><input class="mt-2 w-full border p-3" name="name" maxlength="80" value="{{ old('name',$seller?->name) }}" required></label>
<label class="block"><span class="text-sm font-semibold">Description</span><textarea class="mt-2 w-full border p-3" name="description" rows="5" maxlength="1000" required>{{ old('description',$seller?->description) }}</textarea></label>
<label class="block"><span class="text-sm font-semibold">Pickup address</span><select class="mt-2 w-full border p-3" name="address_id" required><option value="">Select an address</option>@foreach($addresses as $address)<option value="{{ $address->id }}" @selected(old('address_id')==$address->id)>{{ $address->label }} - {{ $address->line1 }}, {{ $address->city }}, {{ $address->province }}</option>@endforeach</select></label>
@if($addresses->isEmpty())<p class="text-sm text-amber-700">Add an address in Buyer Account before applying.</p>@endif
<div class="grid gap-5 md:grid-cols-2"><label><span class="text-sm font-semibold">Shop logo</span><input class="mt-2 block w-full border p-3" type="file" name="logo" accept="image/jpeg,image/png,image/webp" required></label><label><span class="text-sm font-semibold">Permit or valid ID</span><input class="mt-2 block w-full border p-3" type="file" name="permit" accept="image/jpeg,image/png,application/pdf" required></label></div>
<button class="bg-red-900 px-5 py-3 font-semibold text-white" @disabled($addresses->isEmpty())>Submit for review</button></form></div>
@endsection
