@extends('logistics.app')

@section('title', 'Parcel Tracking — LIKHAE Logistics')

@section('content')

<div class="flex w-full flex-col gap-6">

<section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">

<div>
<span class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">
Delivery Monitoring
</span>

<h1 class="mt-2 font-display text-[32px] font-semibold tracking-[-0.04em] text-ink">
Parcel Tracking
</h1>

<p class="mt-2 text-[10px] text-muted">
Monitor parcel movement until successful delivery.
</p>
</div>


<div class="flex h-10 rounded-lg border border-line bg-surface">

<input
class="bg-transparent px-4 text-[9px] outline-none"
placeholder="Search tracking number..."
>

<button class="bg-primary px-4 text-[8px] font-semibold text-white">
Search
</button>

</div>

</section>


<section class="grid gap-5 xl:grid-cols-[1fr_360px]">


<div class="flex flex-col gap-5">


<section class="rounded-xl border border-line bg-surface p-5">

<div class="flex justify-between">

<div>
<span class="text-[8px] uppercase text-muted">
Tracking Number
</span>

<h2 class="mt-2 text-[18px] font-semibold text-ink">
LH-2026-1003
</h2>
</div>


<span class="rounded-full bg-primary-soft px-3 py-1 text-[8px] font-semibold text-primary">
Out for Delivery
</span>

</div>


<div class="mt-5 grid gap-3 sm:grid-cols-3">

<div class="rounded-lg bg-page-secondary p-4">
<span class="text-[8px] text-muted">Buyer</span>
<strong class="mt-2 block text-[10px] text-ink">Ana Reyes</strong>
</div>


<div class="rounded-lg bg-page-secondary p-4">
<span class="text-[8px] text-muted">Rider</span>
<strong class="mt-2 block text-[10px] text-ink">Rider 03</strong>
</div>


<div class="rounded-lg bg-page-secondary p-4">
<span class="text-[8px] text-muted">Destination</span>
<strong class="mt-2 block text-[10px] text-ink">Los Baños</strong>
</div>

</div>

</section>



<section class="rounded-xl border border-line bg-surface p-5">

<h2 class="text-[13px] font-semibold text-ink">
Delivery Timeline
</h2>


<div class="mt-5 space-y-5">

@foreach([
'Parcel Received',
'Sorted by Destination',
'Rider Assigned',
'Out for Delivery',
'Delivered'
] as $status)

<div class="flex gap-3">

<span class="grid h-7 w-7 place-items-center rounded-full bg-primary text-[8px] font-bold text-white">
{{ $loop->iteration }}
</span>

<div>
<strong class="block text-[10px] text-ink">
{{ $status }}
</strong>

<span class="text-[8px] text-muted">
Updated status
</span>
</div>

</div>

@endforeach

</div>

</section>



<section class="rounded-xl border border-line bg-surface p-5">

<h2 class="text-[13px] font-semibold text-ink">
Delivery Route
</h2>

<div class="mt-4 flex h-[220px] items-center justify-center rounded-xl bg-page-secondary">

<span class="text-[9px] text-muted">
Map Integration Placeholder
</span>

</div>

</section>


</div>



<aside class="flex flex-col gap-5">


<section class="rounded-xl bg-primary p-5 text-white">

<span class="text-[8px] uppercase text-white/60">
Current Location
</span>

<h2 class="mt-3 text-[22px] font-semibold">
Los Baños, Laguna
</h2>

<p class="mt-2 text-[9px] text-white/70">
Rider is currently delivering the parcel.
</p>

</section>



<section class="rounded-xl border border-line bg-surface p-5">

<h2 class="text-[13px] font-semibold text-ink">
Rider Information
</h2>

<div class="mt-4 rounded-lg bg-page-secondary p-4">

<strong class="block text-[10px] text-ink">
Rider 03
</strong>

<span class="text-[8px] text-muted">
0917 555 1234
</span>

</div>

</section>



<section class="rounded-xl border border-line bg-surface p-5">

<h2 class="text-[13px] font-semibold text-ink">
Delivery Proof
</h2>

<div class="mt-4 flex h-32 items-center justify-center rounded-lg border border-dashed border-line bg-page-secondary">

<span class="text-[9px] text-muted">
Proof of delivery image
</span>

</div>

</section>


</aside>


</section>

</div>

@endsection
