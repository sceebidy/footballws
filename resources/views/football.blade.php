<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>⚽ Football Club Explorer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: radial-gradient(circle at top, #0b132b, #1c2541);
            color: #fff;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
        }
        .club-card {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 16px;
            padding: 15px;
            transition: 0.3s;
            height: 100%;
        }
        .club-card:hover {
            transform: translateY(-6px);
            background: rgba(255,255,255,0.12);
        }
        h3 {
            color: #5bc0be;
            font-size: 1.15rem;
            margin-bottom: 8px;
        }
        .desc {
            color: #ddd;
            font-size: 0.85rem;
            overflow: hidden;
            text-overflow: ellipsis;
            max-height: 70px;
        }
        .search-bar {
            max-width: 450px;
            margin: 20px auto;
        }
        .controls {
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-4 mb-3">🏆 European Football Club Explorer</h1>

        <form method="GET" action="{{ url('/football') }}" class="d-flex search-bar">
            <input type="text" name="q" class="form-control me-2" placeholder="Search club..." value="{{ $search ?? '' }}">
            <button class="btn btn-primary">Search</button>
        </form>

        <div class="controls">
            <a href="?sort=name" class="btn btn-outline-info btn-sm {{ $sort=='name'?'active':'' }}">Sort by Name</a>
            <a href="?sort=year" class="btn btn-outline-info btn-sm {{ $sort=='year'?'active':'' }}">Sort by Year</a>
        </div>

        @isset($error)
            <div class="alert alert-danger text-center">{{ $error }}</div>
        @endisset

        <div class="row row-cols-1 row-cols-md-3 g-4">
            @forelse($results as $r)
                <div class="col">
                    <div class="club-card">
                        <h3>{{ $r['team']['value'] }}</h3>
                        <p><strong>🏟️ Stadium:</strong> {{ $r['stadium']['value'] }}</p>
                        <p><strong>🌍 Country:</strong> {{ $r['country']['value'] }}</p>
                        <p><strong>👔 Manager:</strong> {{ $r['manager']['value'] }}</p>
                        <p><strong>📅 Founded:</strong> {{ $r['year']['value'] }}</p>
                        <p class="desc">{{ $r['desc']['value'] }}</p>
                    </div>
                </div>
            @empty
                <p class="text-center mt-5">⚠️ No data found or Fuseki not running.</p>
            @endforelse
        </div>

        <footer class="text-center mt-4 mb-3 text-secondary">
            Data source: RDF via Fuseki | Displayed with Laravel
        </footer>
    </div>
</body>
</html>
