@extends('layouts.seller')

@php($mode = $mode ?? 'logistics')
@section('title', match($mode) {
    'logistics'          => 'Logistics Center',
    'rider-applications' => 'Rider Applications',
    'hubs'               => 'Hub Management',
    default              => ucwords(str_replace('-', ' ', $mode)),
})
@section('active', $mode)
@section('subtitle', match($mode) {
    'logistics'          => 'Receive, sort, assign, and monitor marketplace parcels.',
    'rider-applications' => 'Review and approve rider applications for your sorting center.',
    'hubs'               => 'Manage sorting hubs and their coverage areas.',
    default              => 'Logistics Center operations.',
})

@section('content')
<div class="sl-page">

    {{-- ── DASHBOARD ─────────────────────────────────────────────── --}}
    @if($mode === 'logistics')
    <div class="sl-page-toolbar">
        <div><span class="sl-eyebrow">Sorting center</span><h2>Operations overview</h2><p>Friday, September 4 · Laguna Hub</p></div>
        <div class="sl-toolbar-group"><a class="sl-btn sl-btn-primary" href="{{ route('logistics.assignments') }}">Assign parcels</a></div>
    </div>

    <section class="sl-stat-grid" aria-label="Logistics summary">
        <x-seller.stat-card label="Incoming Parcels" value="42" change="Today" icon="orders" />
        <x-seller.stat-card label="Awaiting Sort" value="18" change="Needs attention" direction="down" icon="inventory" />
        <x-seller.stat-card label="Ready for Assignment" value="11" change="Sorted today" icon="shipping" />
        <x-seller.stat-card label="Active Riders" value="24" change="On duty today" icon="users" />
    </section>

    <div class="sl-dashboard-grid">
        <section class="sl-card">
            <header class="sl-card-head">
                <div><span class="sl-eyebrow">Parcel operations</span><h2>Sorting queue</h2><p>Move every parcel from scan to rider assignment.</p></div>
                <a href="{{ route('logistics.parcels') }}" class="sl-text-link">View all parcels</a>
            </header>
            <div class="sl-process-flow">
                @foreach ([['1','Receive','42 parcels'],['2','Scan','18 waiting'],['3','Sort by area','11 sorted'],['4','Assign rider','7 assigned']] as $step)
                    <div class="sl-process-step"><span>{{ $step[0] }}</span><div><strong>{{ $step[1] }}</strong><small>{{ $step[2] }}</small></div></div>
                @endforeach
            </div>
            <div class="sl-table-wrap">
                <table class="sl-table"><thead><tr><th>Parcel</th><th>Delivery address</th><th>Area</th><th>Assigned rider</th><th>Status</th></tr></thead><tbody>
                    @foreach ([
                        ['#1001','Santa Cruz, Laguna','Area A','Juan D.','Assigned','is-info'],
                        ['#1002','Pagsanjan, Laguna','Area B','Maria S.','Sorted','is-warning'],
                        ['#1003','Los Baños, Laguna','Area C','—','At center','is-neutral'],
                    ] as $parcel)
                        <tr><td><strong>{{ $parcel[0] }}</strong></td><td>{{ $parcel[1] }}</td><td>{{ $parcel[2] }}</td><td>{{ $parcel[3] }}</td><td><span class="sl-status {{ $parcel[5] }}">{{ $parcel[4] }}</span></td></tr>
                    @endforeach
                </tbody></table>
            </div>
        </section>

        <section class="sl-card">
            <header class="sl-card-head">
                <div><span class="sl-eyebrow">Rider management</span><h2>Pending applications</h2><p>Applications waiting for your review.</p></div>
                <a href="{{ route('logistics.rider-applications') }}" class="sl-text-link">All applications</a>
            </header>
            <div class="sl-alert-list">
                @foreach ([
                    ['Carlo R.','Area A','Applied today','is-warning'],
                    ['Bea M.','Area B','Applied yesterday','is-warning'],
                    ['Rico T.','Area C','Active · Available','is-success'],
                ] as $rider)
                    <article class="sl-stock-alert {{ $rider[3] }}">
                        <span class="sl-stock-icon">{{ mb_strtoupper(mb_substr($rider[0], 0, 1)) }}</span>
                        <div><small>{{ $rider[1] }}</small><strong>{{ $rider[0] }}</strong><span>{{ $rider[2] }}</span></div>
                        <a class="sl-btn sl-btn-ghost sl-btn-sm" href="{{ route('logistics.rider-applications') }}">Review</a>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
    @endif

    {{-- ── RIDER APPLICATIONS ────────────────────────────────────── --}}
    @if($mode === 'rider-applications')
    <div class="sl-page-toolbar">
        <div><span class="sl-eyebrow">Rider management</span><h2>Rider applications</h2><p>Accept or reject applicants for your sorting center's delivery fleet.</p></div>
    </div>

    <section class="sl-stat-grid" aria-label="Application summary">
        <x-seller.stat-card label="Pending Review" value="4" change="2 new today" direction="down" icon="users" />
        <x-seller.stat-card label="Approved This Month" value="11" change="Active riders" icon="orders" />
        <x-seller.stat-card label="Rejected" value="3" change="This month" icon="inventory" />
        <x-seller.stat-card label="Total Active Riders" value="24" change="Across all areas" icon="shipping" />
    </section>

    <section class="sl-card">
        <header class="sl-card-head">
            <div><span class="sl-eyebrow">Applications</span><h2>Pending review</h2><p>Applicants who submitted their documents and are awaiting approval.</p></div>
        </header>
        <div class="sl-table-wrap">
            <table class="sl-table">
                <thead><tr><th>Applicant</th><th>Contact</th><th>Vehicle</th><th>Coverage area</th><th>Submitted</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @foreach ([
                        ['Carlo Reyes','09171234567','Motorcycle','Area A – Santa Cruz','Today','Pending','is-warning'],
                        ['Bea Mendoza','09281234567','Bicycle','Area B – Pagsanjan','Yesterday','Pending','is-warning'],
                        ['Lito Cruz','09391234567','Motorcycle','Area C – Los Baños','3 days ago','Under review','is-info'],
                        ['Ana Santos','09451234567','Motorcycle','Area A – Santa Cruz','1 week ago','Pending','is-warning'],
                    ] as $app)
                        <tr>
                            <td><strong>{{ $app[0] }}</strong></td>
                            <td>{{ $app[1] }}</td>
                            <td>{{ $app[2] }}</td>
                            <td>{{ $app[3] }}</td>
                            <td>{{ $app[4] }}</td>
                            <td><span class="sl-status {{ $app[6] }}">{{ $app[5] }}</span></td>
                            <td>
                                <div style="display:flex;gap:.5rem;">
                                    <button type="button" class="sl-btn sl-btn-primary sl-btn-sm">Approve</button>
                                    <button type="button" class="sl-btn sl-btn-ghost sl-btn-sm">Reject</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="sl-card" style="margin-top:1.5rem;">
        <header class="sl-card-head">
            <div><span class="sl-eyebrow">Active fleet</span><h2>Approved riders</h2><p>Currently active riders under this sorting center.</p></div>
            <a href="{{ route('logistics.riders') }}" class="sl-text-link">Manage riders</a>
        </header>
        <div class="sl-table-wrap">
            <table class="sl-table">
                <thead><tr><th>Rider</th><th>Vehicle</th><th>Coverage area</th><th>Status</th><th>Deliveries today</th></tr></thead>
                <tbody>
                    @foreach ([
                        ['Juan Dela Cruz','Motorcycle','Area A – Santa Cruz','Available','is-success',4],
                        ['Maria Santos','Bicycle','Area B – Pagsanjan','On delivery','is-info',6],
                        ['Rico Torres','Motorcycle','Area C – Los Baños','On delivery','is-info',3],
                    ] as $rider)
                        <tr>
                            <td><strong>{{ $rider[0] }}</strong></td>
                            <td>{{ $rider[1] }}</td>
                            <td>{{ $rider[2] }}</td>
                            <td><span class="sl-status {{ $rider[4] }}">{{ $rider[3] }}</span></td>
                            <td>{{ $rider[5] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif

    {{-- ── HUB MANAGEMENT ───────────────────────────────────────── --}}
    @if($mode === 'hubs')
    <div class="sl-page-toolbar">
        <div><span class="sl-eyebrow">Center management</span><h2>Hub management</h2><p>Sorting hubs and their assigned coverage areas.</p></div>
        <div class="sl-toolbar-group"><button type="button" class="sl-btn sl-btn-primary">Add hub</button></div>
    </div>

    <section class="sl-stat-grid" aria-label="Hub summary">
        <x-seller.stat-card label="Active Hubs" value="3" change="Operational" icon="orders" />
        <x-seller.stat-card label="Coverage Areas" value="9" change="Across all hubs" icon="shipping" />
        <x-seller.stat-card label="Parcels Today" value="42" change="Across all hubs" icon="inventory" />
        <x-seller.stat-card label="Riders Deployed" value="24" change="Across all hubs" icon="users" />
    </section>

    <section class="sl-card">
        <header class="sl-card-head">
            <div><span class="sl-eyebrow">Hubs</span><h2>Sorting centers</h2><p>Each hub covers specific barangays and municipalities.</p></div>
        </header>
        <div class="sl-table-wrap">
            <table class="sl-table">
                <thead><tr><th>Hub name</th><th>Location</th><th>Coverage areas</th><th>Active riders</th><th>Parcels today</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach ([
                        ['Laguna Hub – Main','Santa Cruz, Laguna','Santa Cruz, Pagsanjan, Lumban',10,18,'Operational','is-success'],
                        ['Laguna Hub – South','Los Baños, Laguna','Los Baños, Calamba, Bay',8,14,'Operational','is-success'],
                        ['Laguna Hub – North','Calamba, Laguna','Calamba, Cabuyao, Biñan',6,10,'Operational','is-success'],
                    ] as $hub)
                        <tr>
                            <td><strong>{{ $hub[0] }}</strong></td>
                            <td>{{ $hub[1] }}</td>
                            <td>{{ $hub[2] }}</td>
                            <td>{{ $hub[3] }}</td>
                            <td>{{ $hub[4] }}</td>
                            <td><span class="sl-status {{ $hub[6] }}">{{ $hub[5] }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif

    {{-- ── ACTIVE RIDERS ─────────────────────────────────────────── --}}
    @if($mode === 'riders')
    <div class="sl-page-toolbar">
        <div><span class="sl-eyebrow">Rider management</span><h2>Active riders</h2><p>All approved riders currently under this sorting center.</p></div>
        <div class="sl-toolbar-group"><a class="sl-btn sl-btn-primary" href="{{ route('logistics.rider-applications') }}">View applications</a></div>
    </div>

    <section class="sl-stat-grid" aria-label="Rider summary">
        <x-seller.stat-card label="Total Riders" value="24" change="Registered" icon="users" />
        <x-seller.stat-card label="On Duty Today" value="18" change="Active now" icon="orders" />
        <x-seller.stat-card label="On Delivery" value="11" change="In transit" icon="shipping" />
        <x-seller.stat-card label="Available" value="7" change="Ready to assign" icon="inventory" />
    </section>

    <section class="sl-card">
        <header class="sl-card-head">
            <div><span class="sl-eyebrow">Fleet</span><h2>All riders</h2></div>
            <a href="{{ route('logistics.rider-applications') }}" class="sl-text-link">Pending applications</a>
        </header>
        <div class="sl-table-wrap">
            <table class="sl-table">
                <thead><tr><th>Rider</th><th>Vehicle</th><th>Hub</th><th>Coverage area</th><th>Status</th><th>Deliveries today</th></tr></thead>
                <tbody>
                    @foreach ([
                        ['Juan Dela Cruz','Motorcycle','Laguna Hub – Main','Area A – Santa Cruz','Available','is-success',4],
                        ['Maria Santos','Bicycle','Laguna Hub – Main','Area B – Pagsanjan','On delivery','is-info',6],
                        ['Rico Torres','Motorcycle','Laguna Hub – South','Area C – Los Baños','On delivery','is-info',3],
                        ['Ana Reyes','Motorcycle','Laguna Hub – South','Area D – Calamba','Available','is-success',5],
                        ['Ben Cruz','Motorcycle','Laguna Hub – North','Area E – Biñan','Off duty','is-neutral',0],
                    ] as $rider)
                        <tr>
                            <td><strong>{{ $rider[0] }}</strong></td>
                            <td>{{ $rider[1] }}</td>
                            <td>{{ $rider[2] }}</td>
                            <td>{{ $rider[3] }}</td>
                            <td><span class="sl-status {{ $rider[5] }}">{{ $rider[4] }}</span></td>
                            <td>{{ $rider[6] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif

    {{-- ── OTHER MODES (parcels, assignments, monitoring, reports, messages, account) ── --}}
    @if(!in_array($mode, ['logistics','rider-applications','hubs','riders']))
    <div class="sl-page-toolbar">
        <div><span class="sl-eyebrow">Logistics Center</span><h2>{{ ucwords(str_replace('-', ' ', $mode)) }}</h2></div>
    </div>
    <section class="sl-card"><div style="padding:3rem;text-align:center;color:var(--sl-muted);">This section is under construction.</div></section>
    @endif

</div>
@endsection
