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
            text-decoration: underline;
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
            <h1>{{ $club['team']['value'] ?? 'Unknown Club' }}</h1>
            <p><strong>🏟️ Stadium:</strong> {{ $club['stadium']['value'] ?? '-' }}</p>
            <p><strong>🌍 Country:</strong> {{ $club['country']['value'] ?? '-' }}</p>
            <p><strong>👔 Manager:</strong> {{ $club['manager']['value'] ?? '-' }}</p>
            <p><strong>📅 Founded:</strong> {{ $club['year']['value'] ?? '-' }}</p>

            <hr class="text-secondary">

            <p><strong>📝 Description:</strong></p>
            <p style="white-space: pre-line;">{{ $club['desc']['value'] ?? 'No description available.' }}</p>

            <hr class="text-secondary">

            <p><strong>🔗 Website:</strong>
                <a href="https://{{ $club['website']['value'] ?? '#' }}" target="_blank" class="text-info">
                    {{ $club['website']['value'] ?? '—' }}
                </a>
            </p>

            <p><strong>📱 Social Media:</strong>
                <a href="https://{{ $club['facebook']['value'] ?? '' }}" target="_blank" class="text-info">Facebook</a> |
                <a href="https://{{ $club['twitter']['value'] ?? '' }}" target="_blank" class="text-info">Twitter</a> |
                <a href="https://{{ $club['instagram']['value'] ?? '' }}" target="_blank" class="text-info">Instagram</a>
            </p>
        </div>
    @endif
</div>
</body>
</html>
