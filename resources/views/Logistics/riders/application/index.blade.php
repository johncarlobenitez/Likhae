@extends('logistics.app')

@section('title','Rider Applications — LIKHAE Logistics')


@section('content')


<div class="flex flex-col gap-8">



{{-- HEADER --}}

<section class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">


<div>

<span class="text-xs font-bold uppercase tracking-[0.2em] text-primary">
Rider Management
</span>


<h1 class="mt-3 text-4xl font-bold tracking-tight text-ink">
Rider Applications
</h1>


<p class="mt-3 text-base text-muted">
Review and verify courier registration applications.
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

{{ count([
1,2,3
]) }} Pending Reviews

</div>


</section>









{{-- SUMMARY CARDS --}}


<section class="grid gap-5 md:grid-cols-4">



<div class="rounded-3xl border border-line bg-surface p-6">

<p class="text-sm text-muted">
Total Applications
</p>


<h2 class="mt-3 text-4xl font-bold text-ink">
35
</h2>


</div>





<div class="rounded-3xl border border-line bg-surface p-6">

<p class="text-sm text-muted">
Pending
</p>


<h2 class="mt-3 text-4xl font-bold text-warning">
12
</h2>


</div>





<div class="rounded-3xl border border-line bg-surface p-6">

<p class="text-sm text-muted">
Approved
</p>


<h2 class="mt-3 text-4xl font-bold text-success">
20
</h2>


</div>





<div class="rounded-3xl border border-line bg-surface p-6">

<p class="text-sm text-muted">
Rejected
</p>


<h2 class="mt-3 text-4xl font-bold text-danger">
3
</h2>


</div>



</section>









{{-- FILTER --}}


<section
class="
rounded-3xl
border
border-line
bg-surface
p-6
"
>


<div class="flex flex-wrap gap-4">


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
All
</button>



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
Pending
</button>




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
Approved
</button>




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
Rejected
</button>


</div>


</section>









{{-- APPLICATION LIST --}}



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
Courier Applicants
</h2>


<p class="mt-2 text-sm text-muted">
Verify applicant information and requirements.
</p>


</div>









<div class="divide-y divide-line">



@php

$riders=[


[
'id'=>1,
'name'=>'Juan Dela Cruz',
'email'=>'juan@email.com',
'vehicle'=>'Motorcycle',
'plate'=>'ABC-1234',
'status'=>'Pending Approval'
],



[
'id'=>2,
'name'=>'Mark Santos',
'email'=>'mark@email.com',
'vehicle'=>'Motorcycle',
'plate'=>'XYZ-5678',
'status'=>'Approved'
],




[
'id'=>3,
'name'=>'Carlo Reyes',
'email'=>'carlo@email.com',
'vehicle'=>'Van',
'plate'=>'VAN-9090',
'status'=>'Rejected'
]


];

@endphp








@foreach($riders as $rider)


<div
class="
flex
flex-col
gap-6
p-8
lg:flex-row
lg:items-center
lg:justify-between
"
>




<div class="flex gap-5">



<div class="grid h-16 w-16 place-items-center rounded-2xl bg-primary-soft text-primary">
<svg viewBox="0 0 24 24" class="h-6 w-6 fill-none stroke-current stroke-[1.6]"><path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"></path><rect x="9" y="11" width="14" height="10" rx="2"></rect><circle cx="12" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle></svg>
</div>





<div>


<h3
class="
text-lg
font-bold
text-ink
"
>
{{ $rider['name'] }}
</h3>



<p class="mt-2 text-sm text-muted">
{{ $rider['email'] }}
</p>



<p class="mt-1 text-sm text-muted">
{{ $rider['vehicle'] }} • {{ $rider['plate'] }}
</p>



</div>



</div>









<div class="flex flex-wrap items-center gap-3">



@if($rider['status']=="Approved")


<span
class="
rounded-full
bg-success-soft
px-4
py-2
text-xs
font-semibold
text-success
"
>
Approved
</span>


@elseif($rider['status']=="Rejected")


<span
class="
rounded-full
bg-danger-soft
px-4
py-2
text-xs
font-semibold
text-danger
"
>
Rejected
</span>


@else


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
Pending Approval
</span>


@endif








<a
href="{{ route('logistics.riders.show',$rider['id']) }}"
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






@if($rider['status']=="Pending Approval")



<form
method="POST"
action="{{ route('logistics.riders.approve',$rider['id']) }}"
>

@csrf

<button
class="
rounded-xl
bg-success
px-5
py-3
text-sm
font-semibold
text-white
"
>
Approve
</button>

</form>







<form
method="POST"
action="{{ route('logistics.riders.reject',$rider['id']) }}"
>

@csrf

<button
class="
rounded-xl
bg-danger
px-5
py-3
text-sm
font-semibold
text-white
"
>
Reject
</button>


</form>



@endif






</div>





</div>



@endforeach




</div>






</section>








</div>


@endsection
