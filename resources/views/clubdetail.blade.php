<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $club['team']['value'] ?? 'Club Detail' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #1a73e8;
            --secondary-color: #34a853;
            --accent-color: #fbbc05;
            --dark-color: #202124;
            --light-color: #f8f9fa;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }
        
        .club-header {
            background: linear-gradient(135deg, var(--primary-color), #6c5ce7);
            color: white;
            border-radius: 0 0 30px 30px;
            padding: 3rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }
        
        .club-logo {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }
        
        .club-info-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            height: 100%;
        }
        
        .info-item {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.25rem;
        }
        
        .info-value {
            color: #5f6368;
        }
        
        .players-section {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }
        
        .player-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            height: 100%;
        }
        
        .player-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary-color);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .player-name {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .player-position {
            color: var(--primary-color);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .search-player {
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            border: 2px solid #e0e0e0;
            margin-bottom: 1.5rem;
        }
        
        .search-player:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(26, 115, 232, 0.25);
        }
        
        .back-button {
            background: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .back-button:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 115, 232, 0.4);
        }
        
        .highlight {
            background-color: var(--accent-color);
            padding: 0 2px;
            border-radius: 3px;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .club-header {
                border-radius: 0 0 20px 20px;
                padding: 2rem 0;
            }
            
            .club-logo, .club-info-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="/">
                <i class="fas fa-futbol me-2"></i>Football Clubs
            </a>
            <a href="/" class="back-button ms-auto">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </nav>

    <!-- Club Header -->
    <div class="club-header">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">{{ $club['team']['value'] ?? 'Club Detail' }}</h1>
            <p class="lead">Informasi lengkap tentang klub dan daftar pemain</p>
        </div>
    </div>

    <div class="container py-4">
        @if(!$club)
            <div class="alert alert-danger text-center">
                <h4 class="alert-heading">Data klub tidak ditemukan</h4>
                <p>Klub yang Anda cari tidak ada dalam database.</p>
                <a href="/" class="btn btn-primary">Kembali ke Daftar Klub</a>
            </div>
        @else
            <div class="row mb-5">
                <div class="col-lg-4 mb-4">
                    <div class="club-logo">
                        <img src="{{ $club['logo']['value'] }}" class="img-fluid" alt="{{ $club['team']['value'] }}" style="max-height: 200px;">
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="club-info-card">
                        <h3 class="fw-bold mb-4">Informasi Klub</h3>
                        
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-flag me-2"></i> Negara</div>
                            <div class="info-value">{{ $club['country']['value'] }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-user-tie me-2"></i> Pelatih</div>
                            <div class="info-value">{{ $club['coach']['value'] }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-map-marker-alt me-2"></i> Lokasi</div>
                            <div class="info-value">{{ $club['location']['value'] }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-calendar-alt me-2"></i> Tahun Berdiri</div>
                            <div class="info-value">{{ $club['founding']['value'] }}</div>
                        </div>
                        

                    </div>
                </div>
            </div>

            <!-- Players Section -->
            <div class="players-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold mb-0">Daftar Pemain</h3>
                    <div class="col-md-4">
                        <input type="text" class="form-control search-player" id="playerSearch" placeholder="Cari nama pemain...">
                    </div>
                </div>
                
                <div class="row" id="playerList">
                    
                    @foreach($players as $player)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4 player-item">
                            <div class="player-card text-center">
                                <div class="player-avatar mb-3">
                                    <i class="fas fa-user-circle fa-3x text-primary"></i>
                                </div>
                                <div class="player-name">{{ $player['name']['value'] }}</div>
                                <div class="player-position">
    {{ $player['position']['value'] ?? 'Unknown Position' }}
</div>

                               <div class="player-nationality">
    <i class="fas fa-flag me-1"></i> 
    {{ $player['nationality']['value'] ?? '-' }}
</div>
<div class="player-birth">
    <i class="fas fa-calendar me-1"></i>
    {{ $player['birth']['value'] ?? '-' }}
</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div id="noPlayers" class="text-center py-5 d-none">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Tidak ada pemain yang ditemukan</h4>
                </div>
            </div>
        @endif
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.getElementById('playerSearch').addEventListener('input', function() {
            const keyword = this.value.toLowerCase();
            const players = document.querySelectorAll('.player-item');
            let visibleCount = 0;
            
            players.forEach(player => {
                const name = player.querySelector('.player-name').textContent.toLowerCase();
                
                if (name.includes(keyword)) {
                    player.style.display = 'block';
                    visibleCount++;
                    
                    // Highlight teks yang cocok
                    if (keyword) {
                        const regex = new RegExp(keyword, 'gi');
                        const originalText = player.querySelector('.player-name').textContent;
                        player.querySelector('.player-name').innerHTML = originalText.replace(regex, 
                            match => `<span class="highlight">${match}</span>`);
                    }
                } else {
                    player.style.display = 'none';
                }
            });
            
            // Tampilkan pesan jika tidak ada hasil
            const noPlayers = document.getElementById('noPlayers');
            if (visibleCount === 0) {
                noPlayers.classList.remove('d-none');
            } else {
                noPlayers.classList.add('d-none');
            }
        });
    </script>
</body>
</html>