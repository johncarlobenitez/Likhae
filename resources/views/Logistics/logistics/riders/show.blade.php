@extends('logistics.app')

@section('title', 'Rider Profile — LIKHAE Logistics')

@section('content')

<div class="flex w-full flex-col gap-6">


{{-- HEADER --}}
<section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">

    <div>
        <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">
            Rider Management
        </span>

        <h1 class="mt-2 font-display text-[32px] font-semibold tracking-[-0.04em] text-ink">
            Rider Profile
        </h1>

        <p class="mt-2 text-[10px] text-muted">
            View rider information, documents, vehicle details, and delivery performance.
        </p>
    </div>


    <div class="flex gap-2">

        <button
            class="rounded-lg border border-line bg-surface px-4 py-2 text-[9px] font-semibold text-ink">
            Suspend
        </button>

        <button
            class="rounded-lg bg-primary px-4 py-2 text-[9px] font-semibold text-white">
            Activate
        </button>

    </div>

</section>



{{-- PROFILE CARD --}}
<section class="rounded-xl border border-line bg-surface p-5">

<div class="flex flex-col gap-5 md:flex-row md:items-center">


<div class="grid h-20 w-20 place-items-center rounded-full bg-primary-soft text-primary">

<span class="text-[20px] font-bold">
JD
</span>

</div>



<div>

<h2 class="text-[20px] font-semibold text-ink">
Juan Dela Cruz
</h2>

<p class="mt-1 text-[10px] text-muted">
Rider ID: RID-0001
</p>


<div class="mt-3 flex flex-wrap gap-2">

<span class="rounded-full bg-success-soft px-3 py-1 text-[8px] font-semibold text-success">
Active
</span>

<span class="rounded-full bg-primary-soft px-3 py-1 text-[8px] font-semibold text-primary">
Area A
</span>

</div>


</div>


</div>

</section>




{{-- INFORMATION --}}
<section class="grid gap-5 lg:grid-cols-2">


<div class="rounded-xl border border-line bg-surface p-5">

<h2 class="text-[13px] font-semibold text-ink">
Personal Information
</h2>


<div class="mt-5 grid gap-4 sm:grid-cols-2">


@foreach([
['Full Name','Juan Dela Cruz'],
['Email','juan@email.com'],
['Contact','0917 555 1234'],
['Address','Santa Cruz, Laguna'],
['Birthday','January 10, 1998'],
['Sex','Male']
] as $item)


<div class="rounded-lg bg-page-secondary p-4">

<span class="text-[8px] uppercase text-muted">
{{ $item[0] }}
</span>

<strong class="mt-2 block text-[10px] font-semibold text-ink">
{{ $item[1] }}
</strong>

</div>


@endforeach


</div>

</div>



<div class="rounded-xl border border-line bg-surface p-5">

<h2 class="text-[13px] font-semibold text-ink">
Vehicle Information
</h2>


<div class="mt-5 grid gap-4">


@foreach([
['Vehicle Type','Motorcycle'],
['Plate Number','ABC-1234'],
['OR/CR Status','Verified'],
['Driver License','Verified']
] as $item)


<div class="rounded-lg bg-page-secondary p-4">

<span class="text-[8px] uppercase text-muted">
{{ $item[0] }}
</span>

<strong class="mt-2 block text-[10px] font-semibold text-ink">
{{ $item[1] }}
</strong>

</div>


@endforeach


</div>

</div>


</section>





{{-- DOCUMENTS --}}
<section class="rounded-xl border border-line bg-surface p-5">

<h2 class="text-[13px] font-semibold text-ink">
Verification Documents
</h2>


<div class="mt-5 grid gap-4 md:grid-cols-2">


<div class="rounded-xl border border-line bg-page-secondary p-5">

<h3 class="text-[10px] font-semibold text-ink">
OR / CR
</h3>

<p class="mt-2 text-[9px] text-success">
Verified Document
</p>


<button class="mt-4 rounded-lg border border-line px-4 py-2 text-[8px] font-semibold text-ink">
View File
</button>

</div>



<div class="rounded-xl border border-line bg-page-secondary p-5">

<h3 class="text-[10px] font-semibold text-ink">
Driver License / ID
</h3>

<p class="mt-2 text-[9px] text-success">
Verified Document
</p>


<button class="mt-4 rounded-lg border border-line px-4 py-2 text-[8px] font-semibold text-ink">
View File
</button>

</div>


</div>

</section>





{{-- PERFORMANCE --}}
<section class="grid gap-3 sm:grid-cols-3">


@foreach([
['Completed Deliveries','342'],
['Rating','4.9'],
['Success Rate','98%']
] as $stat)


<div class="rounded-xl border border-line bg-surface p-5">

<span class="text-[9px] text-muted">
{{ $stat[0] }}
</span>

<strong class="mt-2 block text-[25px] font-bold text-ink">
{{ $stat[1] }}
</strong>

</div>


@endforeach


</section>





{{-- ASSIGNED PARCELS --}}
<section class="rounded-xl border border-line bg-surface overflow-hidden">


<div class="border-b border-line px-5 py-4">

<h2 class="text-[13px] font-semibold text-ink">
Assigned Parcels
</h2>

</div>



@foreach([
['LH-2026-1003','Out for Delivery'],
['LH-2026-1015','Delivered'],
['LH-2026-1018','Pending Pickup']
] as $parcel)


<div class="flex items-center justify-between border-b border-line p-5">


<div>

<strong class="block text-[10px] font-semibold text-ink">
{{ $parcel[0] }}
</strong>

<span class="mt-1 block text-[8px] text-muted">
Delivery Status
</span>

</div>



<span class="rounded-full bg-primary-soft px-3 py-1 text-[8px] font-semibold text-primary">
{{ $parcel[1] }}
</span>


</div>


@endforeach


</section>



</div>

@endsection
