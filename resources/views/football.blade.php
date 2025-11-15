<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Football Club Explorer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: radial-gradient(circle at top, #0b132b, #1c2541);
            color: #fff;
            font-family: 'Poppins', sans-serif;
        }
        h1 { color: #5bc0be; }
        .club-card {
            background: rgba(255,255,255,0.08);
            padding: 18px;
            border-radius: 16px;
            transition: 0.35s;
        }
        .club-card:hover {
            background: rgba(255,255,255,0.15);
            transform: translateY(-5px);
        }
        .search-bar { max-width: 480px; margin: auto; }
    </style>
</head>

<body>
<div class="container">
    <h1 class="text-center mt-4 mb-3">🏆 Football Club Explorer</h1>

    <form method="GET" action="{{ url('/football') }}" class="d-flex search-bar mb-3">
        <input type="text" name="q" class="form-control me-2" placeholder="Search club..." value="{{ $search ?? '' }}">
        <button class="btn btn-primary">Search</button>
    </form>

    <div class="text-center mb-3">
        <form method="GET">
            <select name="league" class="form-select d-inline w-auto">
                <option value="eng.1" {{ $league == 'eng.1' ? 'selected' : '' }}>Premier League</option>
                <option value="esp.1" {{ $league == 'esp.1' ? 'selected' : '' }}>La Liga</option>
                <option value="ger.1" {{ $league == 'ger.1' ? 'selected' : '' }}>Bundesliga</option>
                <option value="ita.1" {{ $league == 'ita.1' ? 'selected' : '' }}>Serie A</option>
                <option value="fra.1" {{ $league == 'fra.1' ? 'selected' : '' }}>Ligue 1</option>
            </select>
            <button class="btn btn-outline-info btn-sm">Change League</button>
        </form>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-4">

        @foreach($clubs as $club)
        <div class="col">
            <div class="club-card h-100">

                <h3>{{ $club['team']['name'] }}</h3>

                <img src="{{ $club['team']['logos'][0]['href'] }}" width="80" class="mb-2">

                <p><b>🏟 Stadium:</b> {{ $club['details']['stadium'] }}</p>
                <p><b>👔 Manager:</b> {{ $club['details']['manager'] }}</p>
                <p><b>📅 Founded:</b> {{ $club['details']['founded'] }}</p>
                <p><b>📊 Rank:</b> {{ $club['stats'][8]['value'] ?? '-' }}</p>

                <p class="text-secondary">{{ $club['details']['description'] }}</p>

            </div>
        </div>
        @endforeach

    </div>

    <footer class="text-center mt-5 text-secondary">
        Data source: API + Wikipedia (auto enriched)
    </footer>

</div>
</body>
</html>
