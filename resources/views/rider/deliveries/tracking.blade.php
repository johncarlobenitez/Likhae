@extends('rider.app')

@section('title','Delivery Tracking — LIKHAE Rider')


@section('content')

<div class="flex flex-col gap-8">


{{-- HEADER --}}

<section class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">


<div>

<span class="text-xs font-bold uppercase tracking-[0.2em] text-primary">
Live Delivery
</span>


<h1 class="mt-3 text-3xl font-bold tracking-tight text-ink">
Delivery Tracking
</h1>


<p class="mt-3 text-base text-muted">
Track your delivery progress until the customer receives the parcel.
</p>


</div>



<div
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
<svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-[1.6]"><path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"></path><rect x="9" y="11" width="14" height="10" rx="2"></rect><circle cx="12" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle></svg>
OUT_FOR_DELIVERY
</div>


</section>









<section class="grid gap-6 xl:grid-cols-[1fr_380px]">





{{-- LEFT CONTENT --}}

<div class="flex flex-col gap-6">






{{-- MAP AREA --}}

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
Delivery Route
</h2>



<div
class="
mt-6
grid
h-[350px]
place-items-center
rounded-3xl
bg-page-secondary
"
>


<div class="text-center">


<div class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-primary-soft text-primary">
<svg viewBox="0 0 24 24" class="h-9 w-9 fill-none stroke-current stroke-[1.6]"><path d="M12 21s7-5 7-11a7 7 0 1 0-14 0c0 6 7 11 7 11Z"></path><circle cx="12" cy="10" r="2"></circle></svg>
</div>


<h3 class="mt-5 text-lg font-bold text-ink">
Customer Location
</h3>


<p class="mt-2 text-sm text-muted">
123 Main Street,
Los Baños, Laguna
</p>


</div>


</div>





<button
id="openRouteBtn"
class="mt-6 w-full rounded-2xl bg-primary py-4 text-sm font-semibold text-white"
>
Open Navigation
</button>


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
Delivery Status
</h2>



<div class="mt-8 space-y-6">



@foreach([

[
'title'=>'Assigned To Rider',
'status'=>'Completed'
],

[
'title'=>'Picked Up From Sorting Center',
'status'=>'Completed'
],

[
'title'=>'Out For Delivery',
'status'=>'Current'
],

[
'title'=>'Customer Received Parcel',
'status'=>'Pending'
],

[
'title'=>'Completed',
'status'=>'Pending'
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

$step['status']=='Completed'
?
'bg-success text-white'

:

($step['status']=='Current'
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


<h3 class="text-base font-bold text-ink">
{{ $step['title'] }}
</h3>


<p class="mt-1 text-sm text-muted">
{{ $step['status'] }}
</p>


</div>



</div>



@endforeach



</div>



</section>







</div>








{{-- RIGHT PANEL --}}

<aside class="flex flex-col gap-6">





{{-- CUSTOMER --}}

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
Customer
</h2>



<div class="mt-5 space-y-4">


<div class="rounded-2xl bg-page-secondary p-5">

<p class="text-sm text-muted">
Name
</p>


<strong class="mt-2 block text-base text-ink">
Juan Dela Cruz
</strong>


</div>




<div class="rounded-2xl bg-page-secondary p-5">

<p class="text-sm text-muted">
Contact
</p>


<strong class="mt-2 block text-base text-ink">
0917 555 1234
</strong>


</div>



</div>


</section>









{{-- ACTIONS --}}

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
Actions
</h2>



<div class="mt-6 flex flex-col gap-4">



<button
id="arrivedCustomerBtn"
class="rounded-2xl border border-line py-4 text-sm font-semibold text-ink"
>
Arrived At Customer
</button>





<button
id="markDeliveredBtn"
type="button"
class="rounded-2xl bg-success py-4 text-center text-sm font-semibold text-white"
>
Mark As Delivered
</button>



<button
id="failedDeliveryBtn"
type="button"
class="rounded-2xl bg-danger py-4 text-center text-sm font-semibold text-white"
>
Delivery Failed
</button>




</div>


</section>








{{-- PARCEL SUMMARY --}}

<section
class="
rounded-3xl
bg-primary
p-7
text-white
"
>


<p class="text-xs uppercase tracking-widest text-white/70">
Tracking Number
</p>


<h2 class="mt-3 text-2xl font-bold">
LH-2026-1007
</h2>


<p class="mt-3 text-sm text-white/80">
COD Amount: ₱1,200
</p>


</section>





</aside>





</section>






</div>

@endsection

@push('scripts')
<script>
document.getElementById('openRouteBtn')?.addEventListener('click', function () {
    window.open('https://www.google.com/maps/search/?api=1&query=123+Main+Street+Los+Ba%C3%B1os+Laguna', '_blank');
});

document.getElementById('arrivedCustomerBtn')?.addEventListener('click', function () {
    this.textContent = 'Customer Reached';
    this.disabled = true;
    this.classList.add('bg-success-soft', 'text-success');
});

document.getElementById('markDeliveredBtn')?.addEventListener('click', function () {
    this.textContent = 'Delivered ✓';
    this.disabled = true;
    this.classList.remove('bg-success');
    this.classList.add('bg-success-soft', 'text-success');
});

document.getElementById('failedDeliveryBtn')?.addEventListener('click', function () {
    this.textContent = 'Marked Failed';
    this.disabled = true;
    this.classList.remove('bg-danger');
    this.classList.add('bg-warning-soft', 'text-warning');
});
</script>
@endpush