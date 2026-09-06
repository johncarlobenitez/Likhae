@extends('rider.app')

@section('title','Scan Parcel — LIKHAE Rider')


@section('content')


<div class="flex flex-col gap-8">



{{-- HEADER --}}

<section>


<span class="text-xs font-bold uppercase tracking-[0.2em] text-primary">
Parcel Verification
</span>


<h1 class="mt-3 text-2xl font-bold tracking-tight text-ink">
Scan Parcel
</h1>


<p class="mt-3 text-sm text-muted">
Confirm that the correct parcel has been collected from the seller.
</p>


</section>







<section class="grid gap-6 xl:grid-cols-[1fr_380px]">





{{-- SCANNER AREA --}}

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
Barcode Scanner
</h2>



<p class="mt-2 text-sm text-muted">
Scan the parcel QR code or barcode.
</p>






<div
class="
mt-8
grid
h-[320px]
place-items-center
rounded-3xl
border-2
border-dashed
border-line
bg-page-secondary
"
>


<div class="text-center">


<div class="mx-auto grid h-20 w-20 place-items-center rounded-3xl bg-primary-soft text-primary">
<svg viewBox="0 0 24 24" class="h-9 w-9 fill-none stroke-current stroke-[1.6]"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M7 7h.01"></path><path d="M7 12h.01"></path><path d="M7 17h.01"></path><path d="M11 7h6"></path><path d="M11 12h6"></path><path d="M11 17h6"></path></svg>
</div>



<h3 class="mt-5 text-lg font-bold text-ink">
Camera Scanner
</h3>


<p class="mt-2 text-sm text-muted">
Point your camera at the parcel barcode.
</p>



</div>


</div>







<button
id="startScannerBtn"
class="
mt-6
w-full
rounded-2xl
bg-primary
py-4
text-sm
font-semibold
text-white
"
>
Start Scanner
</button>




</section>









{{-- PARCEL INFO --}}

<aside
class="
flex
flex-col
gap-6
"
>




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
Parcel Information
</h2>



<div class="mt-6 space-y-4">



<div
class="
rounded-2xl
bg-page-secondary
p-5
"
>

<span class="text-sm text-muted">
Tracking Number
</span>


<strong
class="
mt-2
block
text-lg
font-bold
text-ink
"
>
LH-2026-1001
</strong>


</div>






<div
class="
rounded-2xl
bg-page-secondary
p-5
"
>

<span class="text-sm text-muted">
Seller
</span>


<strong
class="
mt-2
block
text-base
font-semibold
text-ink
"
>
ABC Handmade Store
</strong>


</div>






<div
class="
rounded-2xl
bg-warning-soft
p-5
"
>


<span class="text-sm text-warning">
Current Status
</span>


<strong
class="
mt-2
block
text-base
font-bold
text-warning
"
>
READY_FOR_PICKUP
</strong>


</div>




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


<h2 class="text-xl font-bold">
After Scan
</h2>


<p class="mt-3 text-sm text-white/80">
The parcel status will update automatically.
</p>



<div class="mt-5 space-y-3">


<div class="rounded-xl bg-white/10 p-3 text-sm flex items-center gap-2">
<svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-[2]"><path d="m5 12 4 4 10-10"></path></svg> PICKED_UP
</div>


<div class="rounded-xl bg-white/10 p-3 text-sm flex items-center gap-2">
<svg viewBox="0 0 24 24" class="h-4 w-4 fill-none stroke-current stroke-[2]"><path d="m5 12 4 4 10-10"></path></svg> AT_SORTING_CENTER
</div>


</div>


</section>






</aside>





</section>









{{-- CONFIRMATION --}}

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
Confirm Pickup
</h2>


<p class="mt-2 text-sm text-muted">
Verify that the seller successfully handed over the parcel.
</p>




<div class="mt-6 flex flex-col gap-4 md:flex-row">


<a
href="{{ route('rider.pickups') }}"
class="
flex-1
rounded-2xl
border
border-line
py-4
text-center
text-sm
font-semibold
text-ink
"
>
Cancel
</a>

<button
id="confirmPickupBtn"
class="
flex-1
rounded-2xl
bg-success
py-4
text-sm
font-semibold
text-white
"
>
Confirm Parcel Pickup
</button>



</div>


</section>






</div>



@endsection

@push('scripts')
<script>
document.getElementById('startScannerBtn')?.addEventListener('click', function () {
    this.textContent = 'Scanner Ready';
    this.disabled = true;
    this.classList.remove('bg-primary');
    this.classList.add('bg-success');
});

document.getElementById('confirmPickupBtn')?.addEventListener('click', function () {
    this.textContent = 'Pickup Confirmed ✓';
    this.disabled = true;
    window.setTimeout(function () {
        window.location.href = '{{ route('rider.pickups') }}';
    }, 500);
});
</script>
@endpush