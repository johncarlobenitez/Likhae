@extends('rider.app')

@section('title', 'Earnings — LIKHAE Rider')

@section('content')

<div class="flex w-full flex-col gap-6">


{{-- HEADER --}}
<section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">

    <div>

        <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">
            Income Management
        </span>


        <h1 class="mt-2 font-display text-[32px] font-semibold tracking-[-0.04em] text-ink">
            My Earnings
        </h1>


        <p class="mt-2 text-[10px] text-muted">
            Track your delivery income, incentives, and payout history.
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
        Request Payout
    </button>


</section>





{{-- SUMMARY CARDS --}}
<section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">


@foreach([
['Today','₱850'],
['This Week','₱5,420'],
['This Month','₱28,450'],
['Total Earnings','₱142,800']
] as $stat)


<div class="rounded-xl border border-line bg-surface p-5">

<span class="text-[9px] text-muted">
{{ $stat[0] }}
</span>


<strong class="mt-2 block text-[26px] font-bold tracking-[-0.04em] text-ink">
{{ $stat[1] }}
</strong>


</div>


@endforeach


</section>






{{-- EARNINGS OVERVIEW --}}
<section class="grid gap-5 xl:grid-cols-[1fr_340px]">


<section class="rounded-xl border border-line bg-surface p-5">


<div class="flex items-start justify-between">


<div>

<h2 class="text-[13px] font-semibold text-ink">
Weekly Earnings
</h2>


<p class="mt-1 text-[9px] text-muted">
Your delivery income this week.
</p>


</div>


<span class="rounded-full bg-success-soft px-3 py-1 text-[8px] font-semibold text-success">
+12%
</span>


</div>




<div class="mt-6 flex h-[220px] items-end gap-3 border-b border-line">


@foreach([
['Mon','650'],
['Tue','900'],
['Wed','750'],
['Thu','1200'],
['Fri','980'],
['Sat','620'],
['Sun','850']
] as $day)



<div class="flex flex-1 flex-col items-center justify-end gap-2">


<span class="text-[7px] text-muted">
₱{{ $day[1] }}
</span>



<div
class="w-full max-w-[35px] rounded-t-lg bg-primary"
style="height: {{ ($day[1] / 1200) * 150 }}px"
>
</div>



<span class="pb-2 text-[7px] text-muted">
{{ $day[0] }}
</span>


</div>


@endforeach


</div>


</section>






{{-- PAYOUT --}}
<aside class="rounded-xl border border-line bg-surface p-5">


<h2 class="text-[13px] font-semibold text-ink">
Payout Status
</h2>



<div class="mt-5 rounded-xl bg-success-soft p-5">


<span class="text-[8px] uppercase text-success">
Available Balance
</span>


<strong class="mt-2 block text-[25px] font-bold text-ink">
₱8,450
</strong>



</div>




<div class="mt-5 space-y-3">


<div class="flex justify-between">

<span class="text-[9px] text-muted">
Last Payout
</span>


<strong class="text-[9px] text-ink">
₱7,800
</strong>


</div>



<div class="flex justify-between">

<span class="text-[9px] text-muted">
Status
</span>


<strong class="text-[9px] text-success">
Completed
</strong>


</div>



<div class="flex justify-between">

<span class="text-[9px] text-muted">
Schedule
</span>


<strong class="text-[9px] text-ink">
Every Friday
</strong>


</div>


</div>



</aside>


</section>







{{-- DELIVERY INCENTIVES --}}
<section class="rounded-xl border border-line bg-surface p-5">


<h2 class="text-[13px] font-semibold text-ink">
Delivery Incentives
</h2>


<p class="mt-1 text-[9px] text-muted">
Additional rewards based on performance.
</p>



<div class="mt-5 grid gap-3 sm:grid-cols-3">


@foreach([
['Daily Target','₱300 Bonus'],
['Perfect Rating','₱500 Bonus'],
['100 Deliveries','₱1,000 Bonus']
] as $bonus)


<div class="rounded-lg bg-page-secondary p-4">


<strong class="block text-[10px] font-semibold text-ink">
{{ $bonus[0] }}
</strong>


<span class="mt-2 block text-[9px] text-primary">
{{ $bonus[1] }}
</span>


</div>


@endforeach


</div>


</section>







{{-- TRANSACTION HISTORY --}}
<section class="overflow-hidden rounded-xl border border-line bg-surface">


<div class="border-b border-line px-5 py-4">


<h2 class="text-[13px] font-semibold text-ink">
Earnings History
</h2>


<p class="mt-1 text-[9px] text-muted">
Recent delivery earnings.
</p>


</div>





<div class="divide-y divide-line">


@foreach([
['Sep 03, 2026','8 Deliveries','₱850','Paid'],
['Sep 02, 2026','10 Deliveries','₱1,200','Paid'],
['Sep 01, 2026','6 Deliveries','₱700','Pending']
] as $earning)



<div class="flex flex-col gap-3 p-5 md:flex-row md:items-center md:justify-between">


<div>


<strong class="block text-[10px] font-semibold text-ink">
{{ $earning[0] }}
</strong>


<p class="mt-1 text-[8px] text-muted">
{{ $earning[1] }}
</p>


</div>




<div class="flex items-center gap-3">


<strong class="text-[10px] font-semibold text-ink">
{{ $earning[2] }}
</strong>



<span
class="
rounded-full
px-3
py-1
text-[8px]
font-semibold
{{ $earning[3] === 'Paid'
? 'bg-success-soft text-success'
: 'bg-warning-soft text-warning'
}}
"
>
{{ $earning[3] }}
</span>


</div>


</div>


@endforeach


</div>


</section>





</div>


@endsection
