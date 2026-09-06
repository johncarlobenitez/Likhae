@extends('rider.app')

@section('title','Delivery Assignments — LIKHAE Rider')


@section('content')


<div class="flex flex-col gap-8">



{{-- HEADER --}}

<section class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">


<div>

<span class="text-xs font-bold uppercase tracking-[0.2em] text-primary">
Delivery Management
</span>


<h1 class="mt-3 text-2xl font-bold tracking-tight text-ink">
Delivery Assignments
</h1>


<p class="mt-3 text-sm text-muted">
Deliver sorted parcels from the sorting center to customers.
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
8 Active Deliveries
</div>


</section>

@push('scripts')
<script>
document.querySelectorAll('.accept-delivery-btn').forEach(function (button) {
    button.addEventListener('click', function () {
        this.textContent = 'Accepted';
        this.disabled = true;
        this.classList.remove('bg-primary');
        this.classList.add('bg-success');

        const badge = this.closest('.flex.flex-wrap.items-center.gap-3')?.querySelector('span');
        if (badge) {
            badge.textContent = 'OUT_FOR_DELIVERY';
            badge.classList.remove('bg-primary-soft', 'text-primary');
            badge.classList.add('bg-success-soft', 'text-success');
        }

        window.setTimeout(function () {
            window.location.href = '{{ route('rider.deliveries.show', ':tracking') }}'.replace(':tracking', button.dataset.tracking);
        }, 350);
    });
});
</script>
@endpush





{{-- SUMMARY --}}

<section class="grid gap-5 md:grid-cols-4">


<div class="rounded-3xl border border-line bg-surface p-6">

<p class="text-sm text-muted">
Assigned
</p>

<h2 class="mt-3 text-2xl font-bold text-ink">
8
</h2>

</div>





<div class="rounded-3xl border border-line bg-surface p-6">

<p class="text-sm text-muted">
Ready Delivery
</p>

<h2 class="mt-3 text-4xl font-bold text-ink">
5
</h2>

</div>





<div class="rounded-3xl border border-line bg-surface p-6">

<p class="text-sm text-muted">
Delivered
</p>

<h2 class="mt-3 text-4xl font-bold text-ink">
12
</h2>

</div>





<div class="rounded-3xl border border-line bg-surface p-6">

<p class="text-sm text-muted">
Failed
</p>

<h2 class="mt-3 text-4xl font-bold text-danger">
1
</h2>

</div>


</section>









{{-- DELIVERY LIST --}}

<section
class="
overflow-hidden
rounded-3xl
border
border-line
bg-surface
"
>



<div
class="
border-b
border-line
px-8
py-6
"
>


<h2 class="text-lg font-bold text-ink">
Assigned Deliveries
</h2>


<p class="mt-2 text-xs text-muted">
Parcels assigned from the sorting center.
</p>


</div>







<div class="divide-y divide-line">



@foreach([


[
'tracking'=>'LH-2026-1007',
'buyer'=>'Juan Dela Cruz',
'address'=>'Los Baños, Laguna',
'status'=>'ASSIGNED_TO_RIDER',
'payment'=>'₱1,200'
],



[
'tracking'=>'LH-2026-1015',
'buyer'=>'Maria Santos',
'address'=>'Calamba, Laguna',
'status'=>'SORTED',
'payment'=>'₱650'
],



[
'tracking'=>'LH-2026-1020',
'buyer'=>'Carlo Reyes',
'address'=>'Santa Rosa, Laguna',
'status'=>'OUT_FOR_DELIVERY',
'payment'=>'₱900'
]


] as $delivery)



<div
class="
flex
flex-col
gap-5
p-8
lg:flex-row
lg:items-center
lg:justify-between
"
>



<div class="flex gap-5">


<div class="grid h-16 w-16 place-items-center rounded-2xl bg-primary-soft text-primary">
<svg viewBox="0 0 24 24" class="h-7 w-7 fill-none stroke-current stroke-[1.6]"><path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"></path><rect x="9" y="11" width="14" height="10" rx="2"></rect><circle cx="12" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle></svg>
</div>




<div>


<h3 class="text-lg font-bold text-ink">

{{ $delivery['tracking'] }}

</h3>



<p class="mt-2 text-sm text-muted">
Buyer:
{{ $delivery['buyer'] }}
</p>



<p class="mt-1 text-sm text-muted">
<span class="flex items-center gap-1">
<svg viewBox="0 0 24 24" class="h-3 w-3 fill-none stroke-current stroke-[1.6]"><path d="M12 21s7-5 7-11a7 7 0 1 0-14 0c0 6 7 11 7 11Z"></path><circle cx="12" cy="10" r="2"></circle></svg>
{{ $delivery['address'] }}
</span>
</p>



<p class="mt-2 text-sm font-semibold text-ink">
COD:
{{ $delivery['payment'] }}
</p>



</div>


</div>







<div class="flex flex-wrap items-center gap-3">



<span
class="
rounded-full
bg-primary-soft
px-4
py-2
text-xs
font-semibold
text-primary
"
>

{{ $delivery['status'] }}

</span>





<a
href="{{ route('rider.deliveries.show', $delivery['tracking']) }}"
class="
rounded-xl
border
border-line
px-5
py-3
text-sm
font-semibold
text-ink
"
>
View
</a>





<button
type="button"
class="accept-delivery-btn rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white"
data-tracking="{{ $delivery['tracking'] }}"
>
Accept Delivery
</button>



</div>



</div>


@endforeach



</div>


</section>









{{-- DELIVERY PROCESS --}}

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
Delivery Process
</h2>



<div class="mt-6 grid gap-5 md:grid-cols-5">



@foreach([

'Receive Assignment',
'Pickup From Sorting',
'Out For Delivery',
'Deliver Parcel',
'Buyer Confirms'

] as $index=>$step)



<div
class="
rounded-2xl
bg-page-secondary
p-5
"
>


<div
class="
grid
h-10
w-10
place-items-center
rounded-xl
bg-primary
font-bold
text-white
"
>
{{ $index+1 }}
</div>



<h3 class="mt-4 text-sm font-bold text-ink">
{{ $step }}
</h3>


</div>



@endforeach



</div>


</section>






</div>


@endsection