@extends('logistics.app')

@section('title', 'Delivery Areas — LIKHAE Logistics')


@section('content')

<div class="flex flex-col gap-8">


{{-- HEADER --}}

<section class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">


<div>

<span class="text-xs font-bold uppercase tracking-[0.2em] text-primary">
Logistics Management
</span>


<h1 class="mt-3 text-4xl font-bold tracking-tight text-ink">
Delivery Areas
</h1>


<p class="mt-3 text-base text-muted">
Manage delivery coverage locations and rider service areas.
</p>


</div>



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
+ Add Delivery Area
</button>



</section>








{{-- SUMMARY CARDS --}}

<section class="grid gap-5 md:grid-cols-4">


<div class="rounded-3xl border border-line bg-surface p-6">

<p class="text-sm text-muted">
Total Areas
</p>


<h2 class="mt-3 text-4xl font-bold text-ink">
24
</h2>


</div>





<div class="rounded-3xl border border-line bg-surface p-6">

<p class="text-sm text-muted">
Active Areas
</p>


<h2 class="mt-3 text-4xl font-bold text-success">
21
</h2>


</div>





<div class="rounded-3xl border border-line bg-surface p-6">

<p class="text-sm text-muted">
Assigned Riders
</p>


<h2 class="mt-3 text-4xl font-bold text-ink">
35
</h2>


</div>





<div class="rounded-3xl border border-line bg-surface p-6">

<p class="text-sm text-muted">
Pending Requests
</p>


<h2 class="mt-3 text-4xl font-bold text-warning">
3
</h2>


</div>


</section>









{{-- SEARCH FILTER --}}

<section
class="
rounded-3xl
border
border-line
bg-surface
p-7
"
>


<div class="grid gap-4 lg:grid-cols-4">


<input
type="text"
placeholder="Search area..."
class="
rounded-xl
border
border-line
bg-surface
px-4
py-3
text-sm
outline-none
focus:border-primary
"
/>



<select
class="
rounded-xl
border
border-line
bg-surface
px-4
py-3
text-sm
"
>

<option>
Province
</option>

<option>
Laguna
</option>

<option>
Batangas
</option>

</select>





<select
class="
rounded-xl
border
border-line
bg-surface
px-4
py-3
text-sm
"
>

<option>
Status
</option>

<option>
Active
</option>

<option>
Inactive
</option>

</select>





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
Search
</button>



</div>


</section>









{{-- DELIVERY AREA TABLE --}}

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


<h2 class="text-xl font-bold text-ink">
Coverage Areas
</h2>


<p class="mt-2 text-sm text-muted">
Registered delivery locations.
</p>


</div>






<div class="divide-y divide-line">



@foreach([

[
'province'=>'Laguna',
'municipality'=>'Calamba',
'barangay'=>'Real',
'riders'=>'8',
'status'=>'Active'
],


[
'province'=>'Laguna',
'municipality'=>'Los Baños',
'barangay'=>'Batong Malake',
'riders'=>'5',
'status'=>'Active'
],


[
'province'=>'Batangas',
'municipality'=>'Lipa',
'barangay'=>'Mataas Na Lupa',
'riders'=>'4',
'status'=>'Inactive'
]


] as $area)



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


<div>


<h3 class="text-lg font-bold text-ink">

{{ $area['municipality'] }}, {{ $area['province'] }}

</h3>



<p class="mt-2 text-sm text-muted">

Barangay:
{{ $area['barangay'] }}

</p>



<p class="mt-2 text-sm text-muted">

Assigned Riders:
{{ $area['riders'] }}

</p>


</div>







<div class="flex items-center gap-3">


<span
class="
rounded-full
px-4
py-2
text-xs
font-semibold

{{ 
$area['status']=='Active'
?
'bg-success-soft text-success'
:
'bg-danger-soft text-danger'
}}

"
>

{{ $area['status'] }}

</span>




<button
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
Edit
</button>




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
Manage Riders
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
bg-primary
p-8
text-white
"
>


<h2 class="text-xl font-bold">
Delivery Area Management Flow
</h2>



<div class="mt-6 grid gap-5 md:grid-cols-4">


@foreach([

'Add Location',

'Assign Riders',

'Monitor Coverage',

'Update Status'

] as $index=>$step)


<div
class="
rounded-2xl
bg-white/10
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
bg-white
font-bold
text-primary
"
>
{{ $index+1 }}
</div>


<h3 class="mt-4 text-sm font-bold">
{{ $step }}
</h3>


</div>


@endforeach


</div>


</section>






</div>


@endsection