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
        h1 {
            color: #5bc0be;
            text-shadow: 0 0 12px rgba(91,192,190,0.6);
        }
        .club-card {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 16px;
            padding: 18px;
            transition: 0.35s ease;
            height: 100%;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .club-card:hover {
            transform: translateY(-6px);
            background: rgba(255,255,255,0.12);
            box-shadow: 0 6px 16px rgba(0,0,0,0.5);
        }
        h3 {
            color: #5bc0be;
            font-size: 1.2rem;
            margin-bottom: 8px;
        }
        .desc {
            color: #ddd;
            font-size: 0.9rem;
            overflow: hidden;
            text-overflow: ellipsis;
            max-height: 70px;
        }
        .search-bar {
            max-width: 480px;
            margin: 25px auto;
        }
        .controls {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #5bc0be;
            border: none;
            transition: 0.3s;
        }
        .btn-primary:hover {
            background-color: #4ba3a1;
            transform: scale(1.05);
        }
        .btn-outline-info {
            color: #5bc0be;
            border-color: #5bc0be;
        }
        .btn-outline-info.active {
            background-color: #5bc0be;
            color: #fff;
        }
        .back-btn {
            display: inline-block;
            margin-top: 10px;
            color: #ccc;
            text-decoration: none;
            transition: 0.3s;
        }
        .back-btn:hover {
            color: #5bc0be;
            text-shadow: 0 0 8px rgba(91,192,190,0.6);
        }
        mark {
            background: #5bc0be;
            color: #000;
            border-radius: 3px;
            padding: 0 2px;
        }
        footer {
            font-size: 0.85rem;
            opacity: 0.75;
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

        @if(!empty($search))
            <div class="text-center">
                <a href="{{ url('/football') }}" class="back-btn">← Back to all clubs</a>
            </div>
        @endif

        <div class="controls mt-3">
            <a href="?sort=name{{ $search ? '&q='.urlencode($search) : '' }}" class="btn btn-outline-info btn-sm {{ $sort=='name'?'active':'' }}">Sort by Name</a>
            <a href="?sort=year{{ $search ? '&q='.urlencode($search) : '' }}" class="btn btn-outline-info btn-sm {{ $sort=='year'?'active':'' }}">Sort by Year</a>
        </div>

        @isset($error)
            <div class="alert alert-danger text-center">{{ $error }}</div>
        @endisset

        <div class="row row-cols-1 row-cols-md-3 g-4">
            @forelse($results as $r)
                @php
                    $teamName = $r['team']['value'];
                    if (!empty($search)) {
                        $pattern = '/' . preg_quote($search, '/') . '/i';
                        $teamName = preg_replace($pattern, '<mark>$0</mark>', $teamName);
                    }
                @endphp
                <div class="col">
                    <div class="club-card h-100">
                        <h3>{!! $teamName !!}</h3>
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

        <footer class="text-center mt-5 mb-3 text-secondary">
            Data source: RDF via Fuseki | Displayed with Laravel
        </footer>
    </div>
</body>
</html>
