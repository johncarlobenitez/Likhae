@extends('Admin.layouts.app')
@section('title', 'Settings — LIKHAE Admin')

@section('content')
<div class="page-header">
    <div>
        <p class="page-eyebrow">SYSTEM</p>
        <h1 class="page-title">Settings</h1>
        <p class="page-description">Configure platform-wide settings and preferences.</p>
    </div>
</div>

<div class="grid gap-5 xl:grid-cols-[1fr_1fr]">

    {{-- General Settings --}}
    <div class="panel">
        <div class="panel-header">
            <div><p class="panel-eyebrow">GENERAL</p><h2>Platform Settings</h2></div>
        </div>
        <div class="p-5">
            <form>
                @csrf
                <div class="grid gap-4">
                    <div>
                        <label class="form-label">Platform Name</label>
                        <input type="text" class="form-input" value="LIKHAE Marketplace">
                    </div>
                    <div>
                        <label class="form-label">Support Email</label>
                        <input type="email" class="form-input" value="support@likhae.com">
                    </div>
                    <div>
                        <label class="form-label">Platform Fee (%)</label>
                        <input type="number" class="form-input" value="5" min="0" max="100">
                    </div>
                    <div>
                        <label class="form-label">Maintenance Mode</label>
                        <select class="form-input">
                            <option>Off</option>
                            <option>On</option>
                        </select>
                    </div>
                </div>
                <div class="mt-5 flex justify-end">
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Admin Account --}}
    <div class="panel">
        <div class="panel-header">
            <div><p class="panel-eyebrow">ACCOUNT</p><h2>Admin Account</h2></div>
        </div>
        <div class="p-5">
            <form>
                @csrf
                <div class="grid gap-4">
                    <div>
                        <label class="form-label">Admin Name</label>
                        <input type="text" class="form-input" value="Admin">
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" class="form-input" value="admintest@likhae.com">
                    </div>
                    <div>
                        <label class="form-label">New Password</label>
                        <input type="password" class="form-input" placeholder="Leave blank to keep current">
                    </div>
                    <div>
                        <label class="form-label">Confirm Password</label>
                        <input type="password" class="form-input" placeholder="Confirm new password">
                    </div>
                </div>
                <div class="mt-5 flex justify-end">
                    <button type="submit" class="btn-primary">Update Account</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Registration Settings --}}
    <div class="panel">
        <div class="panel-header">
            <div><p class="panel-eyebrow">REGISTRATION</p><h2>Registration Controls</h2></div>
        </div>
        <div class="p-5">
            <div class="divide-y divide-[#E5E0D9]">
                @foreach([
                    ['Allow Buyer Registration',  true],
                    ['Allow Seller Registration',  true],
                    ['Allow Courier Registration', true],
                    ['Require ID Verification',    true],
                    ['Auto-approve Buyers',        false],
                ] as [$label, $enabled])
                    <div class="flex items-center justify-between py-4">
                        <span class="text-sm font-semibold">{{ $label }}</span>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="checkbox" class="sr-only peer" {{ $enabled ? 'checked' : '' }}>
                            <div class="h-5 w-9 rounded-full bg-[#E5E0D9] peer-checked:bg-[#D92D2F] transition after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-4"></div>
                        </label>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 flex justify-end">
                <button class="btn-primary">Save</button>
            </div>
        </div>
    </div>

    {{-- Notification Settings --}}
    <div class="panel">
        <div class="panel-header">
            <div><p class="panel-eyebrow">NOTIFICATIONS</p><h2>Email Notifications</h2></div>
        </div>
        <div class="p-5">
            <div class="divide-y divide-[#E5E0D9]">
                @foreach([
                    ['New Seller Application',  true],
                    ['New Courier Application',  true],
                    ['Refund Request',           true],
                    ['Disputed Order',           true],
                    ['Daily Sales Summary',      false],
                ] as [$label, $enabled])
                    <div class="flex items-center justify-between py-4">
                        <span class="text-sm font-semibold">{{ $label }}</span>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="checkbox" class="sr-only peer" {{ $enabled ? 'checked' : '' }}>
                            <div class="h-5 w-9 rounded-full bg-[#E5E0D9] peer-checked:bg-[#D92D2F] transition after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-4"></div>
                        </label>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 flex justify-end">
                <button class="btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection
