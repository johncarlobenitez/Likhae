@extends('rider.app')

@section('title','Rider Dashboard — LIKHAE')


@section('content')

<div class="flex flex-col gap-8">


{{-- HEADER --}}
<section class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">


<div>

<h1 class="mt-3 text-lg font-bold tracking-tight text-ink">
Good morning, Juan
</h1>


<p class="mt-3 text-sm text-muted">
Manage your pickup and delivery assignments for today.
</p>


</div>




<div
class="
rounded-full
bg-success-soft
px-5
py-3
text-sm
font-semibold
text-success
"
>

Rider Online

</div>


</section>







{{-- SUMMARY CARDS --}}

<section class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">


@foreach([

[
'title'=>'Pickup Assignments',
'value'=>'12',
'desc'=>'Parcels waiting for pickup',
'icon'=>'pickup'
],

[
'title'=>'Delivery Assignments',
'value'=>'8',
'desc'=>'Parcels to deliver',
'icon'=>'delivery'
],

[
'title'=>'Completed Today',
'value'=>'6',
'desc'=>'Successful deliveries',
'icon'=>'completed'
],

[
'title'=>'Today Earnings',
'value'=>'₱850',
'desc'=>'Delivery income',
'icon'=>'earnings'
]

] as $card)


<div
class="
rounded-3xl
border
border-line
bg-surface
p-6
shadow-sm
transition
hover:-translate-y-1
"
>


<div class="flex items-center justify-between">


<div class="grid h-14 w-14 place-items-center rounded-2xl bg-primary-soft text-primary">

@if($card['icon'] === 'pickup')
<svg viewBox="0 0 24 24" class="h-6 w-6 fill-none stroke-current stroke-[1.6]"><path d="M21 8 12 3 3 8l9 5 9-5Z"></path><path d="M3 8v8l9 5 9-5V8"></path><path d="M12 13v8"></path></svg>
@elseif($card['icon'] === 'delivery')
<svg viewBox="0 0 24 24" class="h-6 w-6 fill-none stroke-current stroke-[1.6]"><path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"></path><rect x="9" y="11" width="14" height="10" rx="2"></rect><circle cx="12" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle></svg>
@elseif($card['icon'] === 'completed')
<svg viewBox="0 0 24 24" class="h-6 w-6 fill-none stroke-current stroke-[1.6]"><circle cx="12" cy="12" r="9"></circle><path d="m9 12 2 2 4-4"></path></svg>
@elseif($card['icon'] === 'earnings')
<svg viewBox="0 0 24 24" class="h-6 w-6 fill-none stroke-current stroke-[1.6]"><path d="M6 3h8a4 4 0 0 1 0 8H6z"></path><path d="M6 11h8a4 4 0 0 1 0 8H6z"></path><path d="M6 3v18"></path><path d="M4 7h4"></path><path d="M4 15h4"></path></svg>
@endif

</div>


</div>




<p class="mt-6 text-xs text-muted">
{{ $card['title'] }}
</p>


<h2
class="
mt-2
text-[1.6rem]
font-bold
text-ink
"
>
{{ $card['value'] }}
</h2>



<p class="mt-2 text-xs text-muted">
{{ $card['desc'] }}
</p>



</div>


@endforeach


</section>









{{-- MAIN CONTENT --}}

<section
class="
grid
gap-6
xl:grid-cols-[1fr_360px]
"
>



{{-- ASSIGNMENTS --}}

<div
class="
rounded-3xl
border
border-line
bg-surface
overflow-hidden
"
>



<div
class="
flex
items-center
justify-between
border-b
border-line
px-7
py-6
"
>


<div>


<h2 class="text-base font-bold text-ink">
Today's Assignments
</h2>


<p class="mt-2 text-xs text-muted">
Your active courier tasks.
</p>


</div>



<button
class="
rounded-xl
bg-primary
px-4
py-2.5
text-sm
font-semibold
text-white
"
>
View All
</button>


</div>







<div class="divide-y divide-line">


@foreach([

[
'id'=>'LH-2026-1001',
'type'=>'Pickup',
'location'=>'ABC Crafts Store - Calamba',
'status'=>'READY_FOR_PICKUP'
],


[
'id'=>'LH-2026-1007',
'type'=>'Delivery',
'location'=>'Juan Dela Cruz - Los Baños',
'status'=>'OUT_FOR_DELIVERY'
],


[
'id'=>'LH-2026-1011',
'type'=>'Pickup',
'location'=>'Maria Shop - Santa Cruz',
'status'=>'READY_FOR_PICKUP'
]


] as $task)



<div
class="
flex
flex-col
gap-5
p-7
lg:flex-row
lg:items-center
lg:justify-between
"
>


<div>


<h3
class="
text-lg
font-bold
text-ink
"
>
{{ $task['id'] }}
</h3>


<p
class="
mt-2
text-sm
text-muted
"
>
{{ $task['type'] }}
</p>


<p
class="
mt-1
text-sm
text-muted
"
>
{{ $task['location'] }}
</p>


</div>





<div class="flex items-center gap-3">


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

{{ $task['status'] }}

</span>



<button
class="
rounded-xl
bg-primary
px-5
py-3
text-sm
font-semibold
text-white
"
>
View
</button>


</div>


</div>


@endforeach


</div>



</div>








{{-- SIDE PANEL --}}

<aside class="flex flex-col gap-6">





<div
class="
rounded-3xl
bg-primary
p-7
text-white
"
>


<p class="text-xs uppercase tracking-widest text-white/70">
Today's Progress
</p>


<h2
class="
mt-4
text-2xl
font-bold
"
>
6/12
</h2>


<p class="mt-3 text-sm text-white/80">
Completed tasks today
</p>



<div
class="
mt-6
h-3
overflow-hidden
rounded-full
bg-white/20
"
>

<div
class="
h-full
w-1/2
rounded-full
bg-white
"
>
</div>


</div>


</div>








<div
class="
rounded-3xl
border
border-line
bg-surface
p-7
"
>


<h2 class="text-xl font-bold text-ink">
Quick Actions
</h2>



<div class="mt-5 space-y-4">


<a
href="#"
class="
block
rounded-2xl
bg-page-secondary
p-5
"
>


<h3 class="flex items-center gap-2 font-semibold text-ink">
<svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-[1.6] text-primary"><path d="M21 8 12 3 3 8l9 5 9-5Z"></path><path d="M3 8v8l9 5 9-5V8"></path><path d="M12 13v8"></path></svg>
Pickup Assignments
</h3>


<p class="mt-2 text-sm text-muted">
Collect parcels from sellers
</p>


</a>





<a
href="#"
class="
block
rounded-2xl
bg-page-secondary
p-5
"
>


<h3 class="flex items-center gap-2 font-semibold text-ink">
<svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-[1.6] text-primary"><path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"></path><rect x="9" y="11" width="14" height="10" rx="2"></rect><circle cx="12" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle></svg>
Delivery Assignments
</h3>


<p class="mt-2 text-sm text-muted">
Deliver parcels to buyers
</p>


</a>



</div>


</div>






<div
class="
rounded-3xl
border
border-line
bg-surface
p-7
"
>


<h2 class="text-xl font-bold text-ink">
Performance
</h2>


<div class="mt-5 space-y-4">


<div class="flex justify-between">

<span class="text-sm text-muted">
Rating
</span>

<strong class="text-sm text-ink">
4.9 / 5.0
</strong>

</div>


<div class="flex justify-between">

<span class="text-sm text-muted">
Success Rate
</span>

<strong class="text-sm text-success">
98%
</strong>

</div>


<div class="flex justify-between">

<span class="text-sm text-muted">
Completed Deliveries
</span>

<strong class="text-sm text-ink">
342
</strong>

</div>


</div>


</div>



</aside>




</section>





</div>


@endsection