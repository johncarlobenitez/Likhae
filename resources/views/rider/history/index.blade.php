@extends('rider.app')

@section('title', 'Delivery History — LIKHAE Rider')

@section('content')

<div class="flex w-full flex-col gap-6">


{{-- HEADER --}}
<section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">

    <div>

        <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">
            Delivery Records
        </span>


        <h1 class="mt-2 font-display text-[32px] font-semibold tracking-[-0.04em] text-ink">
            Delivery History
        </h1>


        <p class="mt-2 text-[10px] text-muted">
            View your completed deliveries and performance records.
        </p>

    </div>


    <button
        class="
        rounded-lg
        bg-primary
        px-5
        py-3
        text-[9px]
        font-semibold
        text-white
        "
    >
        Export History
    </button>


</section>





{{-- SUMMARY --}}
<section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">


@foreach([
['Completed Deliveries','342'],
['Successful Rate','98%'],
['Customer Rating','4.9 ★'],
['Total Earnings','₱28,450']
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






{{-- FILTER --}}
<section class="rounded-xl border border-line bg-surface p-5">


<div class="grid gap-3 lg:grid-cols-[1fr_200px_auto]">


<div>

<label
class="
mb-1.5
block
text-[8px]
font-semibold
uppercase
text-muted
"
>
Search
</label>


<input
type="text"
placeholder="Search tracking number..."
class="
h-10
w-full
rounded-lg
border
border-line
bg-surface
px-3
text-[9px]
outline-none
focus:border-primary
"
/>

</div>




<div>

<label
class="
mb-1.5
block
text-[8px]
font-semibold
uppercase
text-muted
"
>
Date
</label>


<input
type="date"
class="
h-10
w-full
rounded-lg
border
border-line
bg-surface
px-3
text-[9px]
outline-none
focus:border-primary
"
/>

</div>




<button
class="
h-10
self-end
rounded-lg
bg-primary
px-5
text-[9px]
font-semibold
text-white
"
>
Filter
</button>


</div>


</section>







{{-- HISTORY TABLE --}}
<section
class="
overflow-hidden
rounded-xl
border
border-line
bg-surface
"
>


<div class="border-b border-line px-5 py-4">


<h2 class="text-[13px] font-semibold text-ink">
Completed Deliveries
</h2>


<p class="mt-1 text-[9px] text-muted">
Your previous successful deliveries.
</p>


</div>




<div class="divide-y divide-line">



@foreach([
[
'tracking'=>'LH-2026-0901',
'buyer'=>'Ana Reyes',
'location'=>'Los Baños, Laguna',
'date'=>'September 01, 2026',
'payment'=>'₱850',
'rating'=>'5.0'
],

[
'tracking'=>'LH-2026-0830',
'buyer'=>'Carlo Mendoza',
'location'=>'Calamba, Laguna',
'date'=>'August 30, 2026',
'payment'=>'₱1,200',
'rating'=>'4.9'
],

[
'tracking'=>'LH-2026-0828',
'buyer'=>'Maria Cruz',
'location'=>'Santa Cruz, Laguna',
'date'=>'August 28, 2026',
'payment'=>'₱560',
'rating'=>'5.0'
]

] as $delivery)




<article
class="
flex
flex-col
gap-4
p-5
lg:flex-row
lg:items-center
lg:justify-between
"
>


<div>


<strong
class="
block
text-[11px]
font-semibold
text-ink
"
>
{{ $delivery['tracking'] }}
</strong>


<p class="mt-1 text-[9px] text-muted">
Buyer: {{ $delivery['buyer'] }}
</p>


<p class="mt-1 text-[9px] text-muted">
{{ $delivery['location'] }}
</p>


<span class="mt-2 block text-[8px] text-muted">
{{ $delivery['date'] }}
</span>


</div>




<div
class="
flex
flex-wrap
items-center
gap-3
"
>


<span
class="
rounded-full
bg-success-soft
px-3
py-1
text-[8px]
font-semibold
text-success
"
>
Delivered
</span>



<span
class="
rounded-full
bg-primary-soft
px-3
py-1
text-[8px]
font-semibold
text-primary
"
>
{{ $delivery['rating'] }} ★
</span>



<strong
class="
text-[10px]
font-semibold
text-ink
"
>
{{ $delivery['payment'] }}
</strong>


<button
class="
rounded-lg
border
border-line
px-4
py-2
text-[8px]
font-semibold
text-ink
"
>
View
</button>


</div>



</article>


@endforeach



</div>


</section>






{{-- PERFORMANCE --}}
<section class="rounded-xl border border-line bg-surface p-5">


<h2 class="text-[13px] font-semibold text-ink">
Monthly Performance
</h2>


<div class="mt-5 grid gap-4 sm:grid-cols-3">


<div class="rounded-lg bg-page-secondary p-4">

<span class="text-[8px] text-muted">
Deliveries
</span>


<strong class="mt-2 block text-[18px] font-bold text-ink">
86
</strong>

</div>



<div class="rounded-lg bg-page-secondary p-4">

<span class="text-[8px] text-muted">
Average Rating
</span>


<strong class="mt-2 block text-[18px] font-bold text-ink">
4.9
</strong>

</div>




<div class="rounded-lg bg-page-secondary p-4">

<span class="text-[8px] text-muted">
Income
</span>


<strong class="mt-2 block text-[18px] font-bold text-primary">
₱7,850
</strong>

</div>


</div>


</section>




</div>


@endsection