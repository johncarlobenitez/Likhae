@extends('rider.app')

@section('title','Pickup Assignments — LIKHAE Rider')


@section('content')


<div class="flex flex-col gap-8">



{{-- HEADER --}}

<section class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">


<div>


<span class="text-xs font-bold uppercase tracking-[0.2em] text-primary">
Pickup Management
</span>



<h1 class="mt-3 text-2xl font-bold tracking-tight text-ink">
Pickup Assignments
</h1>



<p class="mt-3 text-sm text-muted">
Collect parcels from sellers and bring them to the sorting center.
</p>


</div>



<div
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
<svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-[1.6]"><path d="M21 8 12 3 3 8l9 5 9-5Z"></path><path d="M3 8v8l9 5 9-5V8"></path><path d="M12 13v8"></path></svg>
5 Pending Pickup
</div>



</section>

@push('scripts')
<script>
document.querySelectorAll('.accept-pickup-btn').forEach(function (button) {
    button.addEventListener('click', function () {
        const originalText = this.textContent;

        this.textContent = 'Pickup Accepted';
        this.disabled = true;
        this.classList.remove('bg-primary');
        this.classList.add('bg-success');

        const badge = this.closest('.flex.flex-wrap.items-center.gap-3')?.querySelector('span');
        if (badge) {
            badge.textContent = 'ACCEPTED';
            badge.classList.remove('bg-warning-soft', 'text-warning');
            badge.classList.add('bg-success-soft', 'text-success');
        }

        window.setTimeout(function () {
            window.location.href = '{{ route('rider.pickups.show', ':tracking') }}'.replace(':tracking', button.dataset.tracking);
        }, 350);
    });
});
</script>
@endpush





{{-- SUMMARY --}}

<section class="grid gap-5 md:grid-cols-3">


<div class="rounded-3xl border border-line bg-surface p-6">


<p class="text-sm text-muted">
Ready For Pickup
</p>


<h2 class="mt-3 text-2xl font-bold text-ink">
5
</h2>


</div>





<div class="rounded-3xl border border-line bg-surface p-6">


<p class="text-sm text-muted">
Picked Up Today
</p>


<h2 class="mt-3 text-4xl font-bold text-ink">
8
</h2>


</div>





<div class="rounded-3xl border border-line bg-surface p-6">


<p class="text-sm text-muted">
At Sorting Center
</p>


<h2 class="mt-3 text-4xl font-bold text-ink">
6
</h2>


</div>



</section>









{{-- PICKUP LIST --}}

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
Pickup Tasks
</h2>


<p class="mt-2 text-xs text-muted">
Assigned parcels waiting for seller pickup.
</p>



</div>







<div class="divide-y divide-line">





@foreach([


[
'tracking'=>'LH-2026-1001',
'seller'=>'ABC Handmade Store',
'location'=>'Calamba, Laguna',
'status'=>'READY_FOR_PICKUP',
'items'=>'3 Items'
],



[
'tracking'=>'LH-2026-1005',
'seller'=>'Creative Crafts PH',
'location'=>'Santa Rosa, Laguna',
'status'=>'READY_FOR_PICKUP',
'items'=>'2 Items'
],



[
'tracking'=>'LH-2026-1009',
'seller'=>'Local Artisan Shop',
'location'=>'Biñan, Laguna',
'status'=>'READY_FOR_PICKUP',
'items'=>'5 Items'
]



] as $pickup)



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
<svg viewBox="0 0 24 24" class="h-7 w-7 fill-none stroke-current stroke-[1.6]"><path d="M21 8 12 3 3 8l9 5 9-5Z"></path><path d="M3 8v8l9 5 9-5V8"></path><path d="M12 13v8"></path></svg>
</div>





<div>


<h3
class="
text-lg
font-bold
text-ink
"
>
{{ $pickup['tracking'] }}
</h3>



<p class="mt-2 text-sm text-muted">
Seller:
{{ $pickup['seller'] }}
</p>



<p class="mt-1 text-sm text-muted">
<span class="flex items-center gap-1">
<svg viewBox="0 0 24 24" class="h-3 w-3 fill-none stroke-current stroke-[1.6]"><path d="M12 21s7-5 7-11a7 7 0 1 0-14 0c0 6 7 11 7 11Z"></path><circle cx="12" cy="10" r="2"></circle></svg>
{{ $pickup['location'] }}
</span>
</p>



<p class="mt-2 text-sm text-muted">
{{ $pickup['items'] }}
</p>



</div>



</div>








<div class="flex flex-wrap items-center gap-3">



<span
class="
rounded-full
bg-warning-soft
px-4
py-2
text-xs
font-semibold
text-warning
"
>
{{ $pickup['status'] }}
</span>





<a
href="{{ route('rider.pickups.show', $pickup['tracking']) }}"
class="
rounded-xl
border
border-line
px-5
py-3
text-sm
font-semibold
text-ink
hover:bg-page-secondary
"
>
View
</a>

<button
    type="button"
    class="accept-pickup-btn rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white"
    data-tracking="{{ $pickup['tracking'] }}"
>
    Accept Pickup
</button>



</div>




</div>



@endforeach





</div>



</section>









{{-- PROCESS GUIDE --}}

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



<div class="mt-6 grid gap-5 md:grid-cols-4">


@foreach([

['1','Accept Pickup'],
['2','Go To Seller'],
['3','Scan Parcel'],
['4','Send To Sorting']

] as $step)



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
{{ $step[0] }}
</div>



<h3 class="mt-4 text-sm font-bold text-ink">
{{ $step[1] }}
</h3>



</div>



@endforeach



</div>



</section>






</div>


@endsection