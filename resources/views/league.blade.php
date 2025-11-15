<!DOCTYPE html>
<html>
<head>
    <title>{{ $leagueName }} - Clubs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">

<div class="container mt-4">

    <a href="/football" class="text-info">← Back to Clubs</a>

    <h1 class="mt-3">🏆 {{ $leagueName }} — Clubs</h1>

    <div class="row row-cols-1 row-cols-md-3 g-4 mt-3">
        @foreach($results as $r)
            <div class="col">
                <div class="p-3 bg-secondary rounded">

                    @if(isset($r['logo']['value']))
                        <img src="{{ $r['logo']['value'] }}" height="70" class="mb-2">
                    @endif

                    <h3 class="text-info">
                        {{ $r['name']['value'] }}
                    </h3>

                    <p><strong>🏟️ Stadium:</strong> {{ $r['stadium']['value'] }}</p>
                    <p><strong>🌍 Country:</strong> {{ $r['country']['value'] }}</p>
                    <p><strong>📍 Location:</strong> {{ $r['location']['value'] }}</p>
                    <p><strong>👔 Coach:</strong> {{ $r['coach']['value'] }}</p>
                    <p><strong>📅 Founded:</strong> {{ $r['founded']['value'] }}</p>

                </div>
            </div>
        @endforeach
    </div>

</div>

</body>
</html>
