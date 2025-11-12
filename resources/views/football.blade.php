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
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background particles */
        body::before {
            content: '';
            position: fixed;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(91, 192, 190, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 107, 107, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(120, 111, 255, 0.05) 0%, transparent 50%);
            animation: float 20s ease-in-out infinite;
            z-index: 0;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            33% { transform: translate(30px, -30px); }
            66% { transform: translate(-20px, 20px); }
        }

        .container {
            position: relative;
            z-index: 1;
        }

        /* Header Styles */
        .header-section {
            text-align: center;
            padding: 40px 20px 20px;
            position: relative;
        }

        .main-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 700;
            background: linear-gradient(135deg, #5bc0be 0%, #6fffe9 50%, #5bc0be 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            animation: slideDown 0.8s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .subtitle {
            color: #a0a0a0;
            font-size: 1rem;
            font-weight: 300;
            margin-bottom: 30px;
            animation: fadeIn 1s ease-out 0.3s both;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Search Bar */
        .search-section {
            max-width: 600px;
            margin: 0 auto 30px;
            animation: fadeIn 1s ease-out 0.5s both;
        }

        .search-wrapper {
            position: relative;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            padding: 8px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .search-wrapper:hover {
            border-color: rgba(91, 192, 190, 0.4);
            box-shadow: 0 8px 32px rgba(91, 192, 190, 0.2);
        }

        .search-wrapper:focus-within {
            border-color: rgba(91, 192, 190, 0.6);
            box-shadow: 0 8px 32px rgba(91, 192, 190, 0.3);
        }

        .search-input {
            background: transparent;
            border: none;
            color: #fff;
            padding: 12px 24px;
            font-size: 1rem;
            width: calc(100% - 120px);
            outline: none;
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .search-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, #5bc0be 0%, #3a9d9a 100%);
            border: none;
            border-radius: 50px;
            padding: 10px 28px;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .search-btn:hover {
            background: linear-gradient(135deg, #6fffe9 0%, #5bc0be 100%);
            transform: translateY(-50%) scale(1.05);
            box-shadow: 0 5px 20px rgba(91, 192, 190, 0.4);
        }

        /* Controls */
        .controls {
            text-align: center;
            margin-bottom: 30px;
            animation: fadeIn 1s ease-out 0.7s both;
        }

        .sort-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            padding: 10px 24px;
            color: #fff;
            font-size: 0.9rem;
            margin: 5px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .sort-btn:hover {
            background: rgba(91, 192, 190, 0.2);
            border-color: rgba(91, 192, 190, 0.4);
            color: #6fffe9;
            transform: translateY(-2px);
        }

        .sort-btn.active {
            background: linear-gradient(135deg, #5bc0be 0%, #3a9d9a 100%);
            border-color: #5bc0be;
            color: #fff;
        }

        /* Club Cards */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
            padding: 20px 0;
            animation: fadeIn 1s ease-out 0.9s both;
        }

        .club-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 25px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .club-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(91, 192, 190, 0.1) 0%, rgba(111, 255, 233, 0.05) 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .club-card:hover {
            transform: translateY(-10px) scale(1.02);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(91, 192, 190, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 0 40px rgba(91, 192, 190, 0.2);
        }

        .club-card:hover::before {
            opacity: 1;
        }

        .club-name {
            color: #6fffe9;
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .club-info {
            margin-bottom: 12px;
            display: flex;
            align-items: flex-start;
            position: relative;
            z-index: 1;
        }

        .info-icon {
            font-size: 1.2rem;
            margin-right: 10px;
            min-width: 25px;
        }

        .info-label {
            color: #a0a0a0;
            font-size: 0.85rem;
            font-weight: 600;
            margin-right: 8px;
        }

        .info-value {
            color: #e0e0e0;
            font-size: 0.9rem;
            flex: 1;
        }

        .club-desc {
            color: #b0b0b0;
            font-size: 0.85rem;
            line-height: 1.6;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            max-height: 80px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            position: relative;
            z-index: 1;
        }

        /* Alert */
        .alert-custom {
            background: rgba(255, 107, 107, 0.1);
            border: 1px solid rgba(255, 107, 107, 0.3);
            border-radius: 15px;
            padding: 15px 25px;
            text-align: center;
            margin-bottom: 30px;
            color: #ff6b6b;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #6a6a6a;
        }

        .empty-state-icon {
            font-size: 5rem;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 40px 20px;
            color: #5a5a5a;
            font-size: 0.9rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            margin-top: 60px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .cards-grid {
                grid-template-columns: 1fr;
            }

            .main-title {
                font-size: 2rem;
            }

            .search-input {
                width: calc(100% - 100px);
            }
        }

        /* Loading animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #5bc0be;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
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

        <!-- Controls -->
        <div class="controls">
            <a href="?sort=name" class="sort-btn {{ $sort=='name'?'active':'' }}">📝 Sort by Name</a>
            <a href="?sort=year" class="sort-btn {{ $sort=='year'?'active':'' }}">📅 Sort by Year</a>
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
                <div class="club-card">
                    <h3 class="club-name">{{ $r['team']['value'] }}</h3>
                    
                    <div class="club-info">
                        <span class="info-icon">🏟️</span>
                        <div>
                            <span class="info-label">Stadium:</span>
                            <span class="info-value">{{ $r['stadium']['value'] }}</span>
                        </div>
                    </div>

                    <div class="club-info">
                        <span class="info-icon">🌍</span>
                        <div>
                            <span class="info-label">Country:</span>
                            <span class="info-value">{{ $r['country']['value'] }}</span>
                        </div>
                    </div>

                    <div class="club-info">
                        <span class="info-icon">👔</span>
                        <div>
                            <span class="info-label">Manager:</span>
                            <span class="info-value">{{ $r['manager']['value'] }}</span>
                        </div>
                    </div>

                    <div class="club-info">
                        <span class="info-icon">📅</span>
                        <div>
                            <span class="info-label">Founded:</span>
                            <span class="info-value">{{ $r['year']['value'] }}</span>
                        </div>
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

        <!-- Footer -->
        <footer>
            <p>🔗 Data source: <strong>RDF via Apache Jena Fuseki</strong></p>
            <p>Built with Laravel & Semantic Web Technologies</p>
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