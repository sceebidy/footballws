<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pencarian Klub Sepak Bola</title>
    <link rel="stylesheet" href="{{ asset('/football.css') }}">
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <h1>Sistem Pencarian Klub Sepak Bola</h1>
            <p>Database lengkap klub sepak bola dengan informasi terperinci</p>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <div class="search-input-container">
                <div class="search-container">
                    <input type="text" id="search-input" 
                           placeholder="Masukkan nama klub, negara, pelatih, stadion, atau lokasi..." 
                           value="{{ $search ?? '' }}">
                    <button id="clear-search" class="clear-btn" style="display: none;">×</button>
                    <select id="criteria-select">
                        <option value="all" {{ ($criteria ?? 'all') == 'all' ? 'selected' : '' }}>Semua Kategori</option>
                        <option value="team" {{ ($criteria ?? '') == 'team' ? 'selected' : '' }}>Nama Klub</option>
                        <option value="country" {{ ($criteria ?? '') == 'country' ? 'selected' : '' }}>Negara</option>
                        <option value="coach" {{ ($criteria ?? '') == 'coach' ? 'selected' : '' }}>Pelatih</option>
                        <option value="stadium" {{ ($criteria ?? '') == 'stadium' ? 'selected' : '' }}>Stadion</option>
                        <option value="location" {{ ($criteria ?? '') == 'location' ? 'selected' : '' }}>Lokasi</option>
                        <option value="owner" {{ ($criteria ?? '') == 'owner' ? 'selected' : '' }}>Pemilik</option>
                    </select>
                </div>
                <div id="suggestions"></div>
                <div class="did-you-mean" id="did-you-mean" style="display: none;">
                    <p>Maksud Anda:</p>
                    <div id="correction-suggestions"></div>
                </div>
            </div>

            <!-- Quick Filters -->
            <div class="quick-filters">
                <button class="filter-btn active" data-criteria="all">Semua</button>
                <button class="filter-btn" data-criteria="team">Klub</button>
                <button class="filter-btn" data-criteria="country">Negara</button>
                <button class="filter-btn" data-criteria="coach">Pelatih</button>
                <button class="filter-btn" data-criteria="stadium">Stadion</button>
            </div>

            <!-- Country Filter Section -->
            <div class="country-filter" id="country-filter" style="display: none;">
                <h3>Filter Berdasarkan Negara</h3>
                <div class="country-grid" id="country-grid">
                    <!-- Negara akan dimuat secara dinamis -->
                </div>
            </div>
        </div>

        <!-- Loading Indicator -->
        <div class="loading" id="loading">
            <div class="loading-spinner"></div>
            <div>Sedang mencari data...</div>
        </div>

        <!-- Results Container -->
        <div class="results-container" id="results-container">
            @if(isset($error))
                <div class="empty-state">
                    <h3>Terjadi Kesalahan</h3>
                    <p>{{ $error }}</p>
                </div>
            @elseif(empty($results))
                @if(isset($search) && $search != '')
                    <div class="empty-state">
                        <h3>Data Tidak Ditemukan</h3>
                        <p>Tidak ada hasil yang cocok dengan pencarian "<span class="search-query">{{ $search }}</span>"</p>
                    </div>
                @else
                    <div class="empty-state">
                        <h3>Selamat Datang</h3>
                        <p>Gunakan form pencarian di atas untuk menemukan informasi klub sepak bola</p>
                    </div>
                @endif
            @else
                <div class="results-header">
                    <div class="results-count">
                        Menampilkan <strong>{{ count($results) }}</strong> hasil
                        @if(isset($search) && $search != '')
                            untuk "<span class="search-query">{{ $search }}</span>"
                        @endif
                    </div>
                </div>

                <div class="results-grid">
                    @foreach($results as $result)
                        <div class="club-card">
                            <div class="club-header">
                                <div>
                                    <div class="club-name">{{ $result['team']['value'] ?? 'N/A' }}</div>
                                    <div class="club-country">{{ $result['country']['value'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                            
                            <div class="club-details">
                                <div class="detail-item">
                                    <span class="detail-label">Lokasi</span>
                                    <span class="detail-value">{{ $result['location']['value'] ?? 'N/A' }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Stadion</span>
                                    <span class="detail-value">{{ $result['stadium']['value'] ?? 'N/A' }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Pelatih</span>
                                    <span class="detail-value">{{ $result['coach']['value'] ?? 'N/A' }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Pemilik</span>
                                    <span class="detail-value">{{ $result['owner']['value'] ?? 'N/A' }}</span>
                                </div>
                            </div>

                            @if(isset($result['club']['value']))
                                @php
                                    $clubUri = $result['club']['value'];
                                    $parts = explode('/', $clubUri);
                                    $id = end($parts);
                                @endphp
                                <div class="club-actions">
                                    <a href="{{ route('football.show', $id) }}" class="detail-link">Lihat Detail Lengkap</a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Statistics -->
                <div class="statistics">
                    <div class="stat-card">
                        <div class="stat-number">{{ count($results) }}</div>
                        <div class="stat-label">Total Klub Ditemukan</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">{{ count(array_unique(array_column(array_column($results, 'country'), 'value'))) }}</div>
                        <div class="stat-label">Negara Berbeda</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">{{ count(array_unique(array_column(array_column($results, 'coach'), 'value'))) }}</div>
                        <div class="stat-label">Pelatih Berbeda</div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script src="{{ asset('/football.js') }}"></script>
</body>
</html>