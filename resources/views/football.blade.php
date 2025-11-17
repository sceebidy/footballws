<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Klub Sepak Bola</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #1a73e8;
            --secondary-color: #34a853;
            --accent-color: #fbbc05;
            --dark-color: #202124;
            --light-color: #f8f9fa;
            --gradient-start: #e3f2fd;
            --gradient-end: #e8eaf6;
            --gradient-search: linear-gradient(135deg, #bbdefb, #e1bee7);
        }

        body {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            transition: background 0.5s ease;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), #6c5ce7);
            color: white;
            border-radius: 0 0 30px 30px;
            padding: 3rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            height: 100%;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .card-img-container {
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            padding: 1rem;
        }

        .card-img-top {
            max-height: 120px;
            width: auto;
            object-fit: contain;
        }

        .card-body {
            padding: 1.5rem;
        }

        .club-name {
            color: var(--dark-color);
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 1.25rem;
        }

        .club-info {
            color: #5f6368;
            margin-bottom: 0.3rem;
            font-size: 0.9rem;
        }

        .club-info i {
            width: 20px;
            color: var(--primary-color);
        }

        .btn-primary {
            background: linear-gradient(to right, var(--primary-color), #4285f4);
            border: none;
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 115, 232, 0.4);
        }

        .search-container {
            position: relative;
        }

        #searchInput {
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            border: 2px solid #e0e0e0;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        #searchInput:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(26, 115, 232, 0.25);
        }

        #suggestionBox {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1000;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            max-height: 200px;
            overflow-y: auto;
        }

        .suggestion-item {
            padding: 0.5rem 1rem;
            cursor: pointer;
            transition: background 0.2s;
            border-bottom: 1px solid #f0f0f0;
        }

        .suggestion-item:hover {
            background-color: #f1f3f4;
        }

        .suggestion-item:last-child {
            border-bottom: none;
        }

        .filter-section {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .form-control, .form-select {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(26, 115, 232, 0.25);
        }

        .highlight {
            background-color: var(--accent-color);
            padding: 0 2px;
            border-radius: 3px;
            font-weight: 600;
        }

        .no-results {
            text-align: center;
            padding: 3rem;
            color: #5f6368;
        }

        .no-results i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #dadce0;
        }

        .footer {
            background: rgba(255, 255, 255, 0.9);
            padding: 2rem 0;
            margin-top: 4rem;
            border-top: 1px solid #e0e0e0;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .hero-section {
                border-radius: 0 0 20px 20px;
                padding: 2rem 0;
            }
            
            .card-img-container {
                height: 150px;
            }
            
            .filter-section {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="#">
                <i class="fas fa-futbol me-2"></i>Football Clubs
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Clubs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Players</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">Daftar Klub Sepak Bola</h1>
            <p class="lead mb-4">Temukan informasi lengkap tentang klub sepak bola favorit Anda</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Search & Filter Section -->
        <div class="filter-section">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="search-container">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input id="searchInput" type="text" class="form-control border-start-0" 
                                   placeholder="Cari klub, pelatih, pemain, negara..." autocomplete="off">
                        </div>
                        <div id="suggestionBox" class="bg-white border p-2 mt-1 rounded d-none"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="sortCountry" class="form-select">
                        <option value="">Urutkan berdasarkan Negara</option>
                        <option value="asc">A → Z</option>
                        <option value="desc">Z → A</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Club Cards -->
        <div class="row" id="clubList">
            @foreach ($results as $club)
                <div class="col-lg-4 col-md-6 mb-4 club-card fade-in"
                     data-name="{{ strtolower($club['teamName']['value']) }}"
                     data-country="{{ strtolower($club['country']['value']) }}"
                     data-coach="{{ strtolower($club['coach']['value']) }}"
                     data-players="{{ strtolower(implode(',', $club['players'] ?? [])) }}">

                    <div class="card">
                        <div class="card-img-container">
                            <img src="{{ $club['logo']['value'] ?? 'https://via.placeholder.com/150?text=No+Logo' }}" 
                                 class="card-img-top" alt="{{ $club['teamName']['value'] }}">
                        </div>
                        <div class="card-body">
                            <h5 class="club-name">{{ $club['teamName']['value'] }}</h5>
                            
                            <div class="club-info">
                                <i class="fas fa-flag me-2"></i>
                                <span class="club-country">{{ $club['country']['value'] }}</span>
                            </div>
                            
                            <div class="club-info">
                                <i class="fas fa-user-tie me-2"></i>
                                <span class="club-coach">{{ $club['coach']['value'] }}</span>
                            </div>
                            

                            @php
                                $clubId = str_replace('http://example.com/team/', '', $club['club']['value']);
                            @endphp

                            <a href="{{ route('club.show', $clubId) }}" 
                               class="btn btn-primary btn-sm mt-3 w-100">
                                <i class="fas fa-info-circle me-1"></i> Detail Klub
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- No Results Message -->
        <div id="noResults" class="no-results d-none">
            <i class="fas fa-search"></i>
            <h3>Tidak ada hasil yang ditemukan</h3>
            <p>Coba ubah kata kunci pencarian atau filter yang digunakan</p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container text-center">
            <p class="mb-0">&copy; 2023 Football Clubs Directory. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const searchInput = document.getElementById("searchInput");
        const cards = document.querySelectorAll(".club-card");
        const suggestionBox = document.getElementById("suggestionBox");
        const sortCountry = document.getElementById("sortCountry");
        const filterLeague = document.getElementById("filterLeague");
        const noResults = document.getElementById("noResults");

        // 💡 Daftar nama untuk suggestion
        const allNames = [
            @foreach ($results as $club)
                "{{ strtolower($club['teamName']['value']) }}",
            @endforeach
        ];

        // 💡 Daftar pemain untuk pencarian
        const allPlayers = [
            @foreach ($results as $club)
                @if(isset($club['players']))
                    @foreach($club['players'] as $player)
                        "{{ strtolower($player) }}",
                    @endforeach
                @endif
            @endforeach
        ];

        searchInput.addEventListener("input", function () {
            const keyword = this.value.toLowerCase();

            // 🔥 Background berubah sesuai keyword
            document.body.style.background =
                keyword
                    ? "linear-gradient(135deg, #bbdefb, #e1bee7)"
                    : "linear-gradient(135deg, #e3f2fd, #e8eaf6)";

            // --- Auto-filter card ---
            let visibleCount = 0;
            
            cards.forEach(card => {
                const name = card.dataset.name;
                const coach = card.dataset.coach;
                const country = card.dataset.country;
                const players = card.dataset.players;

                if (
                    name.includes(keyword) ||
                    coach.includes(keyword) ||
                    country.includes(keyword) ||
                    players.includes(keyword)
                ) {
                    card.style.display = "block";
                    visibleCount++;
                } else {
                    card.style.display = "none";
                }
            });

            // Tampilkan pesan jika tidak ada hasil
            if (visibleCount === 0) {
                noResults.classList.remove("d-none");
            } else {
                noResults.classList.add("d-none");
            }

            // --- Suggestion ---
            if (keyword.length > 1) {
                const similarNames = allNames.filter(n => n.includes(keyword));
                const similarPlayers = allPlayers.filter(p => p.includes(keyword));
                const similar = [...new Set([...similarNames, ...similarPlayers])].slice(0, 5);
                
                showSuggestions(similar);
            } else {
                suggestionBox.classList.add("d-none");
            }

            // --- Highlight text ---
            document.querySelectorAll(".club-name, .club-coach, .club-country").forEach(el => {
                const text = el.innerText;
                if (keyword) {
                    const regex = new RegExp(keyword, "gi");
                    el.innerHTML = text.replace(regex, match => `<span class='highlight'>${match}</span>`);
                } else {
                    el.innerHTML = text;
                }
            });
        });

        // --- Suggestion Function ---
        function showSuggestions(list) {
            if (list.length === 0) {
                suggestionBox.classList.add("d-none");
                return;
            }
            
            suggestionBox.innerHTML = "";
            list.forEach(item => {
                const div = document.createElement("div");
                div.classList.add("suggestion-item");
                div.innerHTML = `<i class="fas fa-search me-2 text-muted"></i> ${item}`;
                div.onclick = () => {
                    searchInput.value = item;
                    searchInput.dispatchEvent(new Event("input"));
                    suggestionBox.classList.add("d-none");
                };
                suggestionBox.appendChild(div);
            });
            suggestionBox.classList.remove("d-none");
        }

        // --- Sorting Negara ---
        sortCountry.addEventListener("change", function() {
            const cardsArr = Array.from(cards);
            const list = document.getElementById("clubList");

            let sorted = cardsArr.sort((a, b) => {
                const ca = a.dataset.country;
                const cb = b.dataset.country;

                if (this.value === "asc") return ca.localeCompare(cb);
                if (this.value === "desc") return cb.localeCompare(ca);
                return 0;
            });

            sorted.forEach(card => list.appendChild(card));
        });

        // Tutup suggestion box ketika klik di luar
        document.addEventListener("click", function(e) {
            if (!searchInput.contains(e.target) && !suggestionBox.contains(e.target)) {
                suggestionBox.classList.add("d-none");
            }
        });

        // Animasi fade in untuk kartu
        document.addEventListener("DOMContentLoaded", function() {
            const cards = document.querySelectorAll(".club-card");
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>
</body>
</html>