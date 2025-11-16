<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $club['team']['value'] ?? 'Club Detail' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f5f5;
            color: #111;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
        }

        /* Back Button */
        .back-btn {
            color: #444;
            font-size: 15px;
            text-decoration: none;
            font-weight: 500;
            transition: 0.2s;
        }
        .back-btn:hover {
            color: #000;
            text-decoration: underline;
        }

        h1 {
            color: #111;
            font-weight: 700;
            text-align: center;
            letter-spacing: 0.3px;
            margin-bottom: 28px;
        }

        /* Card */
        .detail-card {
            background: #ffffff;
            border: 1px solid #e5e5e5;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 4px 22px rgba(0,0,0,0.08);
            max-width: 750px;
            margin: auto;
        }

        .detail-item {
            font-size: 15px;
            color: #333;
            margin-bottom: 12px;
        }

        .detail-item strong {
            color: #000;
            font-weight: 600;
        }

        .logo-img {
            max-width: 160px;
            margin-bottom: 20px;
            filter: drop-shadow(0 2px 6px rgba(0,0,0,0.15));
        }

    </style>
</head>
<body>

<div class="container py-5">

    <a href="{{ url('/football') }}" class="back-btn mb-3 d-inline-block">
        ← Back to all clubs
    </a>

    @isset($error)
        <div class="alert alert-danger text-center mt-3">{{ $error }}</div>
    @endisset

    @if(!$club)
        <h2 class="text-center mt-5">⚠ Club data not found.</h2>
    @else

        <h1>{{ $club['team']['value'] }}</h1>

        <div class="detail-card">

            @if(isset($club['logo']['value']))
                <div class="text-center">
                    <img src="{{ $club['logo']['value'] }}" class="logo-img" alt="Club Logo">
                </div>
            @endif

            <p class="detail-item"><strong>Alternate Name:</strong> {{ $club['alt']['value'] ?? '-' }}</p>
            <p class="detail-item"><strong>Country:</strong> {{ $club['country']['value'] ?? '-' }}</p>
            <p class="detail-item"><strong>Home Location:</strong> {{ $club['location']['value'] ?? '-' }}</p>
            <p class="detail-item"><strong>Stadium:</strong> {{ $club['stadium']['value'] ?? '-' }}</p>
            <p class="detail-item"><strong>Coach:</strong> {{ $club['coach']['value'] ?? '-' }}</p>
            <p class="detail-item"><strong>Owner:</strong> {{ $club['owner']['value'] ?? '-' }}</p>

        </div>

    @endif

</div>

</body>
</html>
