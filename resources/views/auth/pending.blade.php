<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Application Pending - LIKHAE</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #f8f7f4; color: #171717; font-family: Poppins, Arial, sans-serif; }
        .panel { width: min(520px, calc(100% - 40px)); padding: 44px; background: #fff; border: 1px solid #e7e2db; text-align: center; box-shadow: 0 22px 60px rgba(52,34,26,.08); }
        .mark { display: inline-grid; place-items: center; width: 48px; height: 48px; background: #8f1719; color: #fff; font-weight: 700; margin-bottom: 24px; }
        h1 { margin: 0 0 12px; font-size: 28px; } p { color: #6f706f; line-height: 1.7; } a { color: #8f1719; font-weight: 600; text-decoration: none; }
    </style>
</head>
<body>
    <main class="panel">
        <span class="mark">L</span>
        <h1>Application submitted</h1>
        <p>Your {{ $accountType ?? 'LIKHAE' }} application has been saved and is waiting for administrator approval. You can sign in once your account is approved.</p>
        <a href="{{ route(in_array($accountType, ['Logistics', 'Rider']) ? 'logistics.login' : 'login') }}">Return to login</a>
    </main>
</body>
</html>
