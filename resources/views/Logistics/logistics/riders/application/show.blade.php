@extends('logistics.app')

@section('title','Rider Application Review — LIKHAE Logistics')


@section('content')


<div class="flex flex-col gap-8">



{{-- SUCCESS MESSAGE --}}

@if(session('success'))

<div
class="
rounded-2xl
bg-success-soft
p-5
text-sm
font-semibold
text-success
"
>
{{ session('success') }}
</div>

@endif







{{-- HEADER --}}


<section
class="
flex
flex-col
gap-5
lg:flex-row
lg:items-center
lg:justify-between
"
>


<div>


<span class="text-xs font-bold uppercase tracking-[0.2em] text-primary">
Courier Registration
</span>



<h1 class="mt-3 text-4xl font-bold text-ink">
Rider Application
</h1>



<p class="mt-3 text-muted">
Review submitted rider requirements before approval.
</p>


</div>





@if($rider['status']=="Approved")

<span
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
APPROVED
</span>



@elseif($rider['status']=="Rejected")


<span
class="
rounded-full
bg-danger-soft
px-5
py-3
text-sm
font-semibold
text-danger
"
>
REJECTED
</span>



@else


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
PENDING REVIEW
</span>



@endif



</section>









{{-- APPLICANT PROFILE --}}


<section
class="
rounded-3xl
border
border-line
bg-surface
p-8
"
>


<div class="flex flex-col gap-6 md:flex-row md:items-center">


<div
class="
grid
h-24
w-24
place-items-center
rounded-full
bg-primary-soft
text-4xl
"
>
🚚
</div>



<div>


<h2 class="text-2xl font-bold text-ink">
{{ $rider['name'] }}
</h2>



<p class="mt-2 text-muted">
Courier Applicant
</p>



</div>


</div>


</section>









{{-- PERSONAL DETAILS --}}


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
Personal Information
</h2>



<div class="mt-6 grid gap-6 md:grid-cols-2">



<div>

<p class="text-sm text-muted">
Last Name
</p>

<strong class="text-ink">
Dela Cruz
</strong>

</div>




<div>

<p class="text-sm text-muted">
First Name
</p>

<strong class="text-ink">
Juan
</strong>

</div>




<div>

<p class="text-sm text-muted">
Middle Initial
</p>

<strong class="text-ink">
M.
</strong>

</div>




<div>

<p class="text-sm text-muted">
Sex
</p>

<strong class="text-ink">
Male
</strong>

</div>




<div>

<p class="text-sm text-muted">
Birthday
</p>

<strong class="text-ink">
January 15, 2000
</strong>

</div>




<div>

<p class="text-sm text-muted">
Age
</p>

<strong class="text-ink">
26 Years Old
</strong>

</div>




</div>


</section>









{{-- CONTACT --}}


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
Contact Details
</h2>



<div class="mt-6 grid gap-6 md:grid-cols-2">



<div>

<p class="text-sm text-muted">
Email
</p>


<strong class="text-ink">
{{ $rider['email'] }}
</strong>


</div>





<div>

<p class="text-sm text-muted">
Contact Number
</p>


<strong class="text-ink">
{{ $rider['contact'] }}
</strong>


</div>





<div class="md:col-span-2">

<p class="text-sm text-muted">
Complete Address
</p>


<strong class="text-ink">
Barangay Real, Calamba City, Laguna
</strong>


</div>



</div>


</section>









{{-- VEHICLE --}}


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
Vehicle Information
</h2>



<div class="mt-6 grid gap-6 md:grid-cols-3">



<div>

<p class="text-sm text-muted">
Vehicle Type
</p>

<strong class="text-ink">
{{ $rider['vehicle'] }}
</strong>

</div>




<div>

<p class="text-sm text-muted">
Plate Number
</p>


<strong class="text-ink">
{{ $rider['plate'] }}
</strong>


</div>




<div>

<p class="text-sm text-muted">
Application Type
</p>


<strong class="text-ink">
Courier Rider
</strong>


</div>



</div>


</section>









{{-- DOCUMENTS --}}


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
Submitted Documents
</h2>



<div class="mt-6 grid gap-5 md:grid-cols-2">



<div
class="
rounded-2xl
bg-page-secondary
p-6
"
>


<h3 class="font-bold text-ink">
OR / CR
</h3>


<p class="mt-2 text-sm text-muted">
Vehicle registration proof
</p>


<button
class="
mt-5
rounded-xl
bg-primary
px-5
py-3
text-sm
font-semibold
text-white
"
>
Preview OR/CR
</button>


</div>







<div
class="
rounded-2xl
bg-page-secondary
p-6
"
>


<h3 class="font-bold text-ink">
Driver License / Valid ID
</h3>


<p class="mt-2 text-sm text-muted">
Identity verification
</p>


<button
class="
mt-5
rounded-xl
bg-primary
px-5
py-3
text-sm
font-semibold
text-white
"
>
Preview ID
</button>


</div>



</div>


</section>









{{-- ACTIONS --}}


@if($rider['status']=="Pending Approval")


<section
class="
flex
flex-wrap
gap-4
"
>


<form
method="POST"
action="{{ route('logistics.riders.approve',$rider['id']) }}"
>

@csrf


<button
type="submit"
class="
rounded-2xl
bg-success
px-8
py-4
font-semibold
text-white
"
>
✓ Approve Application
</button>


</form>







<form
method="POST"
action="{{ route('logistics.riders.reject',$rider['id']) }}"
>

@csrf


<button
type="submit"
class="
rounded-2xl
bg-danger
px-8
py-4
font-semibold
text-white
"
>
✕ Reject Application
</button>


</form>



</section>



@endif






</div>


@endsection