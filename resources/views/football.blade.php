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
            cursor: pointer;
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
        .search-bar {
            max-width: 480px;
            margin: 25px auto;
        }
    </style>
</head>
<body>
<div class="container">

    <h1 class="text-center mt-4 mb-3">🏆 RDF Football Club Explorer</h1>

    <!-- SEARCH + FILTER LIGA -->
    <form method="GET" action="{{ url('football') }}" class="d-flex search-bar justify-content-center gap-2">
        <input type="text" name="q" class="form-control" style="max-width:300px"
               placeholder="Search club..." value="{{ $search ?? '' }}">
        <button class="btn btn-primary">Search</button>

        <button type="button" class="btn btn-outline-info"
                onclick="window.location='{{ url('football') }}?mode=league'">
            Cari Liga
        </button>

        <button type="button" class="btn btn-outline-light"
                onclick="window.location='{{ url('football') }}'">
            Mode Klub
        </button>
    </form>

    @isset($error)
        <div class="alert alert-danger text-center">{{ $error }}</div>
    @endisset


    <!-- ===================================================
         MODE: PILIH LIGA
    ===================================================== -->
    @if(request('mode') === 'league')

        <h3 class="text-center mb-3">📚 Pilih Liga</h3>

        <div class="row row-cols-1 row-cols-md-3 g-4 mt-2">

            @foreach($leagueNames as $id => $name)
                <div class="col">
                    <div class="club-card text-center"
                         onclick="window.location='{{ url("football?league=$id") }}'">

                        <img src="/img/leagues/{{ $id }}.png"
                             onerror="this.style.display='none'"
                             class="img-fluid mb-3 rounded"
                             style="max-height:90px; background:#fff; padding:10px">

                        <h3>{{ $name }}</h3>
                    </div>
                </div>
            @endforeach

        </div>

        <footer class="text-center mt-5 mb-3 text-secondary">
            Mode Liga — pilih salah satu untuk melihat klub
        </footer>

        @php return; @endphp
    @endif


    <!-- ===================================================
         MODE: LIST KLUB BY LIGA
    ===================================================== -->
    @if(request('league'))
        <h3 class="text-center mb-4">
            🏆 Liga: {{ $leagueNames[request('league')] ?? request('league') }}
        </h3>
    @endif


    <!-- ===================================================
         MODE: LIST SEMUA KLUB
    ===================================================== -->
    <div class="row row-cols-1 row-cols-md-3 g-4">

        @forelse($results as $r)

            @php
                $clubId = basename($r['club']['value']);
                $compId = basename($r['competition']['value']);
                $leagueName = $leagueNames[$compId] ?? $compId;
            @endphp

            <div class="col">
                <div class="club-card h-100">

                    @if(isset($r['logo']['value']))
                        <img src="{{ $r['logo']['value'] }}" class="img-fluid mb-2 rounded"
                             style="max-height: 80px; background:#fff; padding:6px;">
                    @endif

                    <h3>
                        <a href="{{ route('football.show', $clubId) }}"
                           class="text-decoration-none text-info">
                            {{ $r['name']['value'] }}
                        </a>
                    </h3>

                    <p><strong>🏟️ Stadium:</strong> {{ $r['stadium']['value'] }}</p>
                    <p><strong>🌍 Country:</strong> {{ $r['country']['value'] }}</p>
                    <p><strong>👔 Coach:</strong> {{ $r['coach']['value'] }}</p>
                    <p><strong>📅 Founded:</strong> {{ $r['founded']['value'] }}</p>

                    <p><strong>🏆 Competition:</strong>
                        <a href="{{ url("football?league=$compId") }}" class="text-info">
                            {{ $leagueName }}
                        </a>
                    </p>

                    <p><strong>📍 Location:</strong> {{ $r['location']['value'] }}</p>
                    <p><strong>🏢 Owner:</strong> {{ $r['owner']['value'] ?? '-' }}</p>

                </div>
            </div>

        @empty
            <p class="text-center mt-5">⚠️ No data found or Fuseki not running.</p>
        @endforelse

    </div>

    <footer class="text-center mt-5 mb-3 text-secondary">
        Data source: RDF via Apache Fuseki | Rendered with Laravel
    </footer>

</div>
</body>
</html>
