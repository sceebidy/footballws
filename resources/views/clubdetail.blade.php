<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $club['team']['value'] ?? 'Club Detail' }}</title>
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
            text-shadow: 0 0 12px rgba(91,192,190,0.6);
        }
        .detail-card {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 18px;
            padding: 25px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.35);
            max-width: 700px;
            margin: auto;
        }
        .detail-item strong {
            color: #5bc0be;
        }
        .logo-img {
            max-width: 150px;
            margin-bottom: 20px;
        }
        .back-btn {
            color: #ccc;
            text-decoration: none;
            transition: 0.3s;
        }
        .back-btn:hover {
            color: #5bc0be;
        }
    </style>
</head>
<body>

<div class="container py-5">

    {{-- BACK BUTTON --}}
    <a href="{{ url('/football') }}" class="back-btn">← Back to all clubs</a>

    {{-- ERROR --}}
    @isset($error)
        <div class="alert alert-danger text-center mt-3">{{ $error }}</div>
    @endisset

    {{-- DATA NOT FOUND --}}
    @if(!$club)
        <h2 class="text-center mt-5">⚠ Club data not found.</h2>
    @else

        <h1 class="text-center mb-4">{{ $club['team']['value'] }}</h1>

        <div class="detail-card">

            {{-- LOGO --}}
            @if(isset($club['logo']['value']))
                <div class="text-center">
                    <img src="{{ $club['logo']['value'] }}" class="logo-img" alt="Club Logo">
                </div>
            @endif

            {{-- ALTERNATE NAME --}}
            <p class="detail-item"><strong>Alternate Name:</strong> {{ $club['alt']['value'] ?? '-' }}</p>

            {{-- COUNTRY --}}
            <p class="detail-item"><strong>Country:</strong> {{ $club['country']['value'] ?? '-' }}</p>

            {{-- LOCATION --}}
            <p class="detail-item"><strong>Home Location:</strong> {{ $club['location']['value'] ?? '-' }}</p>

            {{-- STADIUM --}}
            <p class="detail-item"><strong>Stadium:</strong> {{ $club['stadium']['value'] ?? '-' }}</p>

            {{-- COACH --}}
            <p class="detail-item"><strong>Coach:</strong> {{ $club['coach']['value'] ?? '-' }}</p>

            {{-- OWNER --}}
            <p class="detail-item"><strong>Owner:</strong> {{ $club['owner']['value'] ?? '-' }}</p>

        </div>

    @endif

</div>

</body>
</html>
