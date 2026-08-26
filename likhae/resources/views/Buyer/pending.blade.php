<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Submitted — LIKHAE</title>
    @vite(['resources/css/Guest/register.css'])
</head>
<body class="min-h-screen bg-[#f5f2ed] text-[#111] antialiased">
<main class="flex min-h-screen items-center justify-center px-4 py-12">
    <div class="w-full max-w-md text-center">
        <a href="{{ url('/') }}" class="mb-8 inline-flex items-center gap-2">
            <span class="grid h-8 w-8 place-items-center rounded-md bg-[#d92d2f] text-sm font-black text-white">L</span>
            <span class="text-xl font-black tracking-tight">LIKHAE</span>
        </a>

        <div class="border border-[#ddd6ce] bg-white p-8 shadow-[0_18px_45px_rgba(0,0,0,.06)]">
            <div class="mx-auto mb-5 grid h-14 w-14 place-items-center rounded-full bg-[#fff9e8] text-2xl">⏳</div>
            <h1 class="text-lg font-black">Registration Submitted!</h1>
            <p class="mt-3 text-sm leading-6 text-[#6f6861]">
                Your buyer account is pending administrator approval.<br>
                You'll receive a confirmation at your registered email once approved.
            </p>
            <a href="{{ url('/') }}" class="mt-6 inline-flex h-11 items-center justify-center bg-[#d92d2f] px-6 text-sm font-bold text-white transition hover:bg-[#bd2024]">
                Back to Home
            </a>
        </div>
    </div>
</main>
</body>
</html>
