@extends('rider.app')

@section('title','Delivery Details — LIKHAE Rider')


@section('content')


<div class="flex flex-col gap-8">



{{-- HEADER --}}

<section class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">


<div>


<span class="text-xs font-bold uppercase tracking-[0.2em] text-primary">
Delivery Assignment
</span>



<h1 class="mt-3 text-3xl font-bold tracking-tight text-ink">
Delivery Details
</h1>



<p class="mt-3 text-base text-muted">
Deliver this parcel from the sorting center to the customer.
</p>


</div>




<span
class="
rounded-full
bg-primary-soft
px-5
py-3
text-sm
font-semibold
text-primary
"
>
ASSIGNED_TO_RIDER
</span>



</section>








<section class="grid gap-6 xl:grid-cols-[1fr_380px]">





{{-- MAIN --}}

<div class="flex flex-col gap-6">





{{-- PARCEL DETAILS --}}

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
<svg viewBox="0 0 24 24" class="h-7 w-7 fill-none stroke-current stroke-[1.6]"><path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"></path><rect x="9" y="11" width="14" height="10" rx="2"></rect><circle cx="12" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle></svg>
</div>



<div>


<p class="text-sm text-muted">
Tracking Number
</p>


<h2 class="mt-1 text-xl font-bold text-ink">
LH-2026-1007
</h2>


</div>


</div>






<div class="mt-8 grid gap-5 md:grid-cols-2">



<div class="rounded-2xl bg-page-secondary p-5">

<p class="text-sm text-muted">
Current Status
</p>

<strong class="mt-2 block text-base text-primary">
ASSIGNED_TO_RIDER
</strong>

</div>





<div class="rounded-2xl bg-page-secondary p-5">

<p class="text-sm text-muted">
Payment Method
</p>

<strong class="mt-2 block text-base text-ink">
Cash On Delivery
</strong>

</div>





<div class="rounded-2xl bg-page-secondary p-5">

<p class="text-sm text-muted">
COD Amount
</p>

<strong class="mt-2 block text-base text-ink">
₱1,200
</strong>

</div>




<div class="rounded-2xl bg-page-secondary p-5">

<p class="text-sm text-muted">
Parcel Type
</p>

<strong class="mt-2 block text-base text-ink">
Regular Package
</strong>

</div>



</div>



</section>









{{-- CUSTOMER DETAILS --}}

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
Customer Information
</h2>



<div class="mt-6 grid gap-5">



<div class="rounded-2xl bg-page-secondary p-5">


<p class="text-sm text-muted">
Customer Name
</p>


<strong class="mt-2 block text-base text-ink">
Juan Dela Cruz
</strong>


</div>






<div class="rounded-2xl bg-page-secondary p-5">


<p class="text-sm text-muted">
Delivery Address
</p>


<strong class="mt-2 block text-base text-ink">
123 Main Street,
Los Baños, Laguna
</strong>


</div>







<div class="rounded-2xl bg-page-secondary p-5">


<p class="text-sm text-muted">
Contact Number
</p>


<strong class="mt-2 block text-base text-ink">
0917 555 1234
</strong>


</div>




</div>


</section>









{{-- DELIVERY TIMELINE --}}

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
Delivery Progress
</h2>



<div class="mt-6 space-y-5">


@foreach([

[
'Receive Assignment',
'Completed'
],

[
'Pickup From Sorting Center',
'Current'
],

[
'Out For Delivery',
'Pending'
],

[
'Delivered',
'Pending'
],

[
'Completed',
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
font-bold
{{ 
$step[1]=='Completed'
?
'bg-success text-white'
:
($step[1]=='Current'
?
'bg-primary text-white'
:
'bg-page-secondary text-muted')
}}
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
Delivery Actions
</h2>




<div class="mt-6 flex flex-col gap-4">



<button
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
Accept Delivery
</button>





<button
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
Navigate To Sorting Center
</button>





<button
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
Contact Customer
</button>



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
Next Status
</p>


<h2 class="mt-3 text-xl font-bold">
OUT_FOR_DELIVERY
</h2>


<p class="mt-3 text-sm text-white/80">
After collecting the parcel from the sorting center, start delivery.
</p>



</section>





</aside>





</section>





</div>


@endsection

@push('scripts')
<script>
document.getElementById('acceptDeliveryBtn')?.addEventListener('click', function () {
    this.textContent = 'Delivery Accepted';
    this.disabled = true;
    this.classList.remove('bg-primary');
    this.classList.add('bg-success');

    window.setTimeout(function () {
        window.location.href = '{{ route('rider.deliveries.tracking', $id) }}';
    }, 400);
});

document.getElementById('navigateSortingBtn')?.addEventListener('click', function () {
    window.open('https://www.google.com/maps/search/?api=1&query=Sorting+Center+Laguna', '_blank');
});

document.getElementById('contactCustomerBtn')?.addEventListener('click', function () {
    window.location.href = 'tel:+639175551234';
});
</script>
@endpush