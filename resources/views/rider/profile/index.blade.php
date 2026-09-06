@extends('rider.app')

@section('title', 'Rider Profile — LIKHAE')

@section('content')

<div class="flex w-full flex-col gap-6">


{{-- HEADER --}}
<section>

    <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">
        Account Settings
    </span>


    <h1 class="mt-2 font-display text-[32px] font-semibold tracking-[-0.04em] text-ink">
        My Profile
    </h1>


    <p class="mt-2 text-[10px] text-muted">
        Manage your rider information, vehicle details, and account settings.
    </p>

</section>





{{-- PROFILE CARD --}}
<section class="rounded-xl border border-line bg-surface p-5">


<div class="flex flex-col gap-5 md:flex-row md:items-center">


<div
class="
grid
h-24
w-24
place-items-center
rounded-full
bg-primary-soft
text-primary
"
>

<span class="text-[24px] font-bold">
JD
</span>


</div>



<div>

<h2 class="text-[20px] font-semibold text-ink">
Juan Dela Cruz
</h2>


<p class="mt-1 text-[9px] text-muted">
Rider ID: RID-0001
</p>


<div class="mt-3 flex gap-2">


<span class="rounded-full bg-success-soft px-3 py-1 text-[8px] font-semibold text-success">
Verified Rider
</span>


<span class="rounded-full bg-primary-soft px-3 py-1 text-[8px] font-semibold text-primary">
Online
</span>


</div>


</div>


</div>


</section>







{{-- PERSONAL INFORMATION --}}
<section class="rounded-xl border border-line bg-surface p-5">


<h2 class="text-[13px] font-semibold text-ink">
Personal Information
</h2>


<div class="mt-5 grid gap-4 md:grid-cols-2">


@foreach([
['Full Name','Juan Dela Cruz'],
['Email','juan@email.com'],
['Contact Number','0917 555 1234'],
['Birthday','January 10, 1998'],
['Sex','Male'],
['Address','Calamba, Laguna']
] as $info)


<div class="rounded-lg bg-page-secondary p-4">


<span class="text-[8px] uppercase text-muted">
{{ $info[0] }}
</span>


<strong class="mt-2 block text-[10px] font-semibold text-ink">
{{ $info[1] }}
</strong>


</div>


@endforeach


</div>


</section>








{{-- VEHICLE INFORMATION --}}
<section class="rounded-xl border border-line bg-surface p-5">


<h2 class="text-[13px] font-semibold text-ink">
Vehicle Information
</h2>


<div class="mt-5 grid gap-4 md:grid-cols-2">



@foreach([
['Vehicle Type','Motorcycle'],
['Plate Number','ABC-1234'],
['OR/CR Status','Verified'],
['Driver License','Verified']
] as $vehicle)


<div class="rounded-lg bg-page-secondary p-4">


<span class="text-[8px] uppercase text-muted">
{{ $vehicle[0] }}
</span>


<strong class="mt-2 block text-[10px] font-semibold text-ink">
{{ $vehicle[1] }}
</strong>


</div>


@endforeach



</div>


</section>







{{-- DOCUMENTS --}}
<section class="rounded-xl border border-line bg-surface p-5">


<h2 class="text-[13px] font-semibold text-ink">
Verification Documents
</h2>


<div class="mt-5 grid gap-4 md:grid-cols-2">


<div class="rounded-xl border border-line bg-page-secondary p-5">


<h3 class="text-[10px] font-semibold text-ink">
OR / CR Document
</h3>


<p class="mt-2 text-[8px] text-success">
Verified
</p>


<button
class="
mt-4
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
View Document
</button>


</div>




<div class="rounded-xl border border-line bg-page-secondary p-5">


<h3 class="text-[10px] font-semibold text-ink">
Driver License / ID
</h3>


<p class="mt-2 text-[8px] text-success">
Verified
</p>


<button
class="
mt-4
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
View Document
</button>


</div>



</div>


</section>







{{-- CHANGE PASSWORD --}}
<section class="rounded-xl border border-line bg-surface p-5">


<h2 class="text-[13px] font-semibold text-ink">
Change Password
</h2>


<div class="mt-5 grid gap-4 md:grid-cols-2">


<input
type="password"
placeholder="Current password"
class="
h-10
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



<input
type="password"
placeholder="New password"
class="
h-10
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
mt-4
rounded-lg
bg-primary
px-5
py-3
text-[9px]
font-semibold
text-white
"
>
Update Password
</button>


</section>








{{-- SETTINGS --}}
<section class="grid gap-5 lg:grid-cols-2">



<div class="rounded-xl border border-line bg-surface p-5">


<h2 class="text-[13px] font-semibold text-ink">
Notifications
</h2>


<div class="mt-5 space-y-3">


@foreach([
'New parcel assignment',
'Delivery reminders',
'Customer messages'
] as $setting)


<label class="flex items-center justify-between rounded-lg bg-page-secondary p-4">


<span class="text-[9px] font-semibold text-ink">
{{ $setting }}
</span>


<input
type="checkbox"
checked
class="accent-primary"
/>


</label>


@endforeach


</div>


</div>






<div class="rounded-xl border border-line bg-surface p-5">


<h2 class="text-[13px] font-semibold text-ink">
Appearance
</h2>


<p class="mt-2 text-[9px] text-muted">
Choose your preferred interface theme.
</p>



<div class="mt-5 grid gap-3">


<button
id="lightModeButton"
class="
rounded-lg
border
border-line
bg-page-secondary
p-4
text-left
"
>

<strong class="block text-[10px] text-ink">
Light Theme
</strong>

<span class="text-[8px] text-muted">
Default appearance
</span>

</button>




<button
id="darkModeButton"
class="
rounded-lg
border
border-line
bg-page-secondary
p-4
text-left
"
>

<strong class="block text-[10px] text-ink">
Dark Theme
</strong>

<span class="text-[8px] text-muted">
Low light mode
</span>

</button>


</div>


</div>


</section>







{{-- LOGOUT --}}
<section class="rounded-xl border border-danger/20 bg-danger-soft p-5">


<h2 class="text-[13px] font-semibold text-danger">
Account Actions
</h2>


<p class="mt-2 text-[9px] text-danger/70">
Logout from your rider account.
</p>


<button
class="
mt-4
rounded-lg
border
border-danger/30
bg-surface
px-5
py-3
text-[9px]
font-semibold
text-danger
"
>
Logout
</button>


</section>





</div>


<script>

const lightModeButton = document.getElementById('lightModeButton');
const darkModeButton = document.getElementById('darkModeButton');

function applyTheme(theme) {
    const dark = theme === 'dark';

    document.documentElement.classList.toggle('dark', dark);
    localStorage.setItem('likhae-theme', theme);

    lightModeButton.classList.toggle('border-primary', !dark);
    lightModeButton.classList.toggle('bg-primary-soft', !dark);
    lightModeButton.classList.toggle('border-line', dark);
    lightModeButton.classList.toggle('bg-page-secondary', dark);

    darkModeButton.classList.toggle('border-primary', dark);
    darkModeButton.classList.toggle('bg-primary-soft', dark);
    darkModeButton.classList.toggle('border-line', !dark);
    darkModeButton.classList.toggle('bg-page-secondary', !dark);
}

lightModeButton?.addEventListener('click', function() {
    applyTheme('light');
});

darkModeButton?.addEventListener('click', function() {
    applyTheme('dark');
});

applyTheme(localStorage.getItem('likhae-theme') === 'dark' ? 'dark' : 'light');

</script>


@endsection s that's much better