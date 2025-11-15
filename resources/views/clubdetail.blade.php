<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $club['name']['value'] ?? 'Club Detail' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: radial-gradient(circle at top, #0b132b, #1c2541);
            color: #fff;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
        }
        h1 {
            color: #5bc0be;
            text-shadow: 0 0 10px rgba(91,192,190,0.5);
        }
        .info-card {
            background: rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        a.back {
            color: #5bc0be;
            text-decoration: none;
        }
        a.back:hover {
            color: #4ba3a1;
            text-decoration: underline;
        }
        .logo-box {
            background: #fff;
            border-radius: 12px;
            padding: 10px;
            display: inline-block;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="container mt-5">

    <a href="{{ url('/football') }}" class="back">← Back</a>

    @if(isset($error))
        <div class="alert alert-danger mt-4">{{ $error }}</div>

    @elseif(!$club)
        <div class="alert alert-warning mt-4">⚠️ Club data not found.</div>

    @else

        <div class="info-card mt-3">

            {{-- Logo --}}
            @if(isset($club['logo']['value']))
                <div class="logo-box">
                    <img src="{{ $club['logo']['value'] }}" alt="logo" height="90">
                </div>
            @endif

            <h1>
                {{ $club['name']['value'] ?? 'Unknown Club' }}
            </h1>
            <p class="text-info">{{ $club['nickname']['value'] ?? '' }}</p>

            <p><strong>🏟️ Stadium:</strong> {{ $club['stadium']['value'] ?? '-' }}</p>
            <p><strong>🌍 Country:</strong> {{ $club['country']['value'] ?? '-' }}</p>
            <p><strong>📍 Location:</strong> {{ $club['location']['value'] ?? '-' }}</p>
            <p><strong>👔 Coach:</strong> {{ $club['coach']['value'] ?? '-' }}</p>
            <p><strong>🏢 Owner:</strong> {{ $club['owner']['value'] ?? '-' }}</p>
            <p><strong>🏆 Competition:</strong> {{ basename($club['competition']['value'] ?? '-') }}</p>
            <p><strong>📅 Founded:</strong> {{ $club['founded']['value'] ?? '-' }}</p>

            <hr class="text-secondary">

            <p><strong>Club URI:</strong>  
                <span class="text-info">{{ $clubUri ?? '' }}</span>
            </p>

        </div>

    @endif

</div>

</body>
</html>
