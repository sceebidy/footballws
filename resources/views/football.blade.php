<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⚽ Football Club Explorer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #0a0e27 0%, #1a1f3a 50%, #0f1419 100%);
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
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            position: relative;
            z-index: 1;
        }
        .search-bar {
            max-width: 480px;
            margin: 25px auto;
        }

        .empty-state-icon {
            font-size: 5rem;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        /* Footer */
        footer {
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
        <!-- Header -->
        <div class="header-section">
            <h1 class="main-title">⚽ Football Club Explorer</h1>
            <p class="subtitle">Discover European football clubs with semantic web technology</p>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <form method="GET" action="{{ url('/football') }}">
                <div class="search-wrapper">
                    <input type="text" name="q" class="search-input" placeholder="Search for your favorite club..." value="{{ $search ?? '' }}" autocomplete="off">
                    <button type="submit" class="search-btn">Search</button>
                </div>
            </form>
        </div>

        @if(!empty($search))
            <div class="text-center">
                <a href="{{ url('/football') }}" class="back-btn">← Back to all clubs</a>
            </div>
        @endif

        <div class="controls mt-3">
            <a href="?sort=name{{ $search ? '&q='.urlencode($search) : '' }}" class="btn btn-outline-info btn-sm {{ $sort=='name'?'active':'' }}">Sort by Name</a>
            <a href="?sort=year{{ $search ? '&q='.urlencode($search) : '' }}" class="btn btn-outline-info btn-sm {{ $sort=='year'?'active':'' }}">Sort by Year</a>
        </div>

        <!-- Error Alert -->
        @isset($error)
            <div class="alert-custom">
                <strong>⚠️ Error:</strong> {{ $error }}
            </div>
        @endisset

        <!-- Club Cards Grid -->
        <div class="cards-grid">
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
                       <h3>
    <a href="{{ route('football.show', basename($r['club']['value'])) }}" 
       class="text-decoration-none text-info">
       {!! $teamName !!}
    </a>
</h3>
                        <p><strong>🏟️ Stadium:</strong> {{ $r['stadium']['value'] }}</p>
                        <p><strong>🌍 Country:</strong> {{ $r['country']['value'] }}</p>
                        <p><strong>👔 Manager:</strong> {{ $r['manager']['value'] }}</p>
                        <p><strong>📅 Founded:</strong> {{ $r['year']['value'] }}</p>
                        <p class="desc">{{ $r['desc']['value'] }}</p>
                    </div>

                    <div class="club-desc">{{ $r['desc']['value'] }}</div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-state-icon">⚽</div>
                    <h3>No clubs found</h3>
                    <p>Try adjusting your search or check if Fuseki server is running</p>
                </div>
            @endforelse
        </div>

        <footer class="text-center mt-4 mb-3 text-secondary">
            Data source: RDF via Fuseki | Displayed with Laravel
        </footer>
    </div>

    <script>
        // Add smooth scroll behavior
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href'))?.scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Card animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.club-card').forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = `all 0.6s ease ${index * 0.1}s`;
            observer.observe(card);
        });
    </script>
</body>
</html>