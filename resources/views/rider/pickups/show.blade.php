@extends('rider.app')

@section('title','Pickup Details — LIKHAE Rider')


@section('content')

<div class="flex flex-col gap-8">


{{-- HEADER --}}

<section class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">


<div>

<span class="text-xs font-bold uppercase tracking-[0.2em] text-primary">
Pickup Assignment
</span>


<h1 class="mt-3 text-3xl font-bold tracking-tight text-ink">
Pickup Details
</h1>


<p class="mt-3 text-base text-muted">
Collect this parcel from the seller and bring it to the sorting center.
</p>


</div>



<span
class="
rounded-full
bg-warning-soft
px-5
py-3
text-sm
font-semibold
text-warning
"
>
READY_FOR_PICKUP
</span>



</section>








<section class="grid gap-6 xl:grid-cols-[1fr_360px]">





{{-- MAIN INFORMATION --}}

<div class="flex flex-col gap-6">





{{-- PARCEL CARD --}}

<section
class="
rounded-3xl
border
border-line
bg-surface
p-8
"
>



<div class="flex items-center gap-5">


<div class="grid h-16 w-16 place-items-center rounded-2xl bg-primary-soft text-primary">
<svg viewBox="0 0 24 24" class="h-7 w-7 fill-none stroke-current stroke-[1.6]"><path d="M21 8 12 3 3 8l9 5 9-5Z"></path><path d="M3 8v8l9 5 9-5V8"></path><path d="M12 13v8"></path></svg>
</div>



<div>

<p class="text-sm text-muted">
Tracking Number
</p>


<h2 class="mt-1 text-xl font-bold text-ink">
LH-2026-1001
</h2>


</div>


</div>






<div class="mt-8 grid gap-5 md:grid-cols-2">


<div class="rounded-2xl bg-page-secondary p-5">


<p class="text-sm text-muted">
Parcel Status
</p>


<strong class="mt-2 block text-base text-warning">
READY_FOR_PICKUP
</strong>


</div>





<div class="rounded-2xl bg-page-secondary p-5">


<p class="text-sm text-muted">
Items
</p>


<strong class="mt-2 block text-base text-ink">
3 Items
</strong>


</div>




<div class="rounded-2xl bg-page-secondary p-5">


<p class="text-sm text-muted">
Payment Type
</p>


<strong class="mt-2 block text-base text-ink">
Cash on Delivery
</strong>


</div>




<div class="rounded-2xl bg-page-secondary p-5">


<p class="text-sm text-muted">
COD Amount
</p>


<strong class="mt-2 block text-base text-ink">
₱850
</strong>


</div>



</div>



</section>








{{-- SELLER INFORMATION --}}

<section
class="
rounded-3xl
border
border-line
bg-surface
p-8
"
>


<h2 class="text-xl font-bold text-ink">
Seller Information
</h2>



<div class="mt-6 space-y-4">


<div class="rounded-2xl bg-page-secondary p-5">


<p class="text-sm text-muted">
Store Name
</p>


<strong class="mt-2 block text-base text-ink">
ABC Handmade Store
</strong>


</div>





<div class="rounded-2xl bg-page-secondary p-5">


<p class="text-sm text-muted">
Pickup Address
</p>


<strong class="mt-2 block text-base text-ink">
123 Rizal Street,
Calamba Laguna
</strong>


</div>





<div class="rounded-2xl bg-page-secondary p-5">


<p class="text-sm text-muted">
Seller Contact
</p>


<strong class="mt-2 block text-base text-ink">
0917 555 8888
</strong>


</div>



</div>


</section>









{{-- PICKUP TIMELINE --}}

<section
class="
rounded-3xl
border
border-line
bg-surface
p-8
"
>


<h2 class="text-xl font-bold text-ink">
Pickup Process
</h2>



<div class="mt-6 space-y-5">


@foreach([

[
'Accept Pickup',
'Waiting'
],

[
'Travel To Seller',
'Pending'
],

[
'Confirm Parcel',
'Pending'
],

[
'Bring To Sorting Center',
'Pending'
]


] as $index=>$step)



<div class="flex gap-5">


<div
class="
grid
h-12
w-12
place-items-center
rounded-full
{{ $index == 0 ? 'bg-primary text-white':'bg-page-secondary text-muted' }}
font-bold
"
>
{{ $index+1 }}
</div>



<div>


<h3 class="text-base font-semibold text-ink">
{{ $step[0] }}
</h3>


<p class="mt-1 text-sm text-muted">
{{ $step[1] }}
</p>


</div>


</div>



@endforeach



</div>


</section>





</div>









{{-- ACTION PANEL --}}

<aside class="flex flex-col gap-6">





<section
class="
rounded-3xl
border
border-line
bg-surface
p-7
"
>


<h2 class="text-xl font-bold text-ink">
Pickup Actions
</h2>



<div class="mt-6 flex flex-col gap-4">

<button
id="acceptPickupBtn"
class="
rounded-2xl
bg-primary
px-6
py-4
text-sm
font-semibold
text-white
"
>
Accept Pickup
</button>

<button
id="openNavBtn"
class="
rounded-2xl
border
border-line
px-6
py-4
text-sm
font-semibold
text-ink
"
>
Open Navigation
</button>

<a
href="{{ route('rider.pickups.scan') }}"
class="
rounded-2xl
border
border-line
px-6
py-4
text-center
text-sm
font-semibold
text-ink
"
>
Scan Parcel
</a>

</div>


</section>






<section
class="
rounded-3xl
bg-primary
p-7
text-white
"
>


<p class="text-xs uppercase tracking-widest text-white/70">
Next Step
</p>


<h2 class="mt-3 text-2xl font-bold">
Bring Parcel To Sorting Center
</h2>


<p class="mt-3 text-sm text-white/80">
After successful pickup, parcel status changes to PICKED_UP.
</p>


</section>




</aside>




</section>





</div>


@endsection

@push('scripts')
<script>
document.getElementById('acceptPickupBtn')?.addEventListener('click', function () {
    this.textContent = 'Pickup Accepted!';
    this.classList.replace('bg-primary', 'bg-success');
    this.disabled = true;
});

document.getElementById('openNavBtn')?.addEventListener('click', function () {
    window.open('https://www.google.com/maps/search/?api=1&query=123+Rizal+Street+Calamba+Laguna', '_blank');
});
</script>
@endpush
