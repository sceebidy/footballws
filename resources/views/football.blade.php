<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Klub Sepak Bola</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #0091ff;
            --primary-light: #3d6df0;
            --bg-soft: #f5f7fb;
            --text-dark: #1f1f1f;
            --text-light: #6b7280;
            --card-radius: 18px;
            --transition: 0.25s ease;
        }

        body {
            background: var(--bg-soft);
            font-family: "Inter", "Segoe UI", sans-serif;
        }

        /* NAVBAR */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(6px);
            border-bottom: 1px solid #e5e7eb;
        }

        /* HERO */
        .hero-section {
            padding: 70px 0 90px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border-radius: 0 0 45px 45px;
            text-align: center;
        }

        .hero-section h1 {
            font-size: 46px;
            font-weight: 800;
        }

        /* SEARCH BOX */
        .filter-section {
            margin-top: -50px;
            background: white;
            padding: 25px;
            border-radius: 22px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        }

        .form-control, .form-select {
            border-radius: 14px;
            padding: 12px 18px;
        }

        #searchInput {
            border-width: 2px;
        }

        .input-group-text {
            background: white !important;
            border-right: none !important;
        }

        #suggestionBox {
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        .suggestion-item {
            padding: 10px 16px;
            font-size: 15px;
        }

        /* CLUB CARD */
        .club-card .card {
            border-radius: var(--card-radius);
            border: none;
            background: white;
            transition: var(--transition);
        }

        .club-card .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 35px rgba(0,0,0,0.12);
        }

        .card-img-container {
            background: #eef1fb;
            padding: 25px;
            height: 170px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .club-name {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .club-info {
            font-size: 14px;
            color: var(--text-light);
        }

        /* BUTTON */
        .btn-primary-custom {
            background: var(--primary);
            border: none;
            border-radius: 12px;
            padding: 10px;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-primary-custom:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
        }

        /* NO RESULTS */
        .no-results i {
            font-size: 60px;
            color: #d1d5db;
        }

        /* FOOTER */
        footer {
            margin-top: 60px;
            padding: 25px 0;
            background: #ffffff;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="#">
                <i class="fas fa-futbol me-2"></i>Football Clubs
            </a>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero-section">
        <div class="container">
            <h1>Daftar Klub Sepak Bola</h1>
            <p class="mt-2">Temukan informasi lengkap tentang klub favorit Anda</p>
        </div>
    </section>

    <!-- SEARCH & FILTER -->
    <div class="container">
        <div class="filter-section">
            <div class="row g-3">
                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input id="searchInput" type="text" class="form-control"
                               placeholder="Cari klub, pemain, pelatih, negara…" autocomplete="off">
                    </div>
                    <div id="suggestionBox" class="bg-white border mt-2 d-none"></div>
                </div>

                <div class="col-md-3 offset-md-1">
                    <select id="sortCountry" class="form-select">
                        <option value="">Urutkan negara</option>
                        <option value="asc">A → Z</option>
                        <option value="desc">Z → A</option>
                    </select>
                </div>
            </div>
        </div>
<!-- CHATBOT BUTTON -->
<div id="chatbotButton" style="
    position: fixed;
    bottom: 25px; right: 25px;
    background: #1a73e8;
    color: white;
    width: 60px; height: 60px;
    border-radius: 50%;
    display: flex; justify-content: center; align-items: center;
    font-size: 28px;
    cursor: pointer;
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    z-index: 999;
">
    💬
</div>

<!-- CHATBOT WINDOW -->
<div id="chatbotWindow" style="
    position: fixed;
    bottom: 100px; right: 25px;
    width: 320px; height: 430px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.3);
    display: none; flex-direction: column;
    overflow: hidden;
    z-index: 999;
">
    <div style="background:#1a73e8; color:white; padding:15px; font-weight:bold;">
        BotBall – Asisten Sepak Bola
    </div>

    <div id="chatContent" style="
        flex: 1; padding: 10px; overflow-y: auto;
        font-size: 14px;
    "></div>

    <div style="padding:10px; display:flex; gap:5px;">
        <input id="chatInput" type="text" placeholder="Tanya tentang klub..." 
               style="flex:1; padding:8px; border-radius:8px; border:1px solid #ddd;">
        <button id="chatSend" style="
            padding:8px 12px; background:#1a73e8; color:white;
            border:none; border-radius:8px; font-weight:bold;
        ">Kirim</button>
    </div>
</div>

        <!-- CLUB LIST -->
        <div class="row mt-4" id="clubList">
            @foreach ($results as $club)
           <div class="col-lg-4 col-md-6 mb-4 club-card fade-in"
    data-name="{{ strtolower($club['teamName']['value']) }}"
    data-country="{{ strtolower($club['country']['value']) }}"
    data-coach="{{ strtolower($club['coach']['value']) }}"
    data-players="{{ strtolower(implode(',', $club['players'] ?? [])) }}"
    data-stadium="{{ strtolower($club['stadiumName']['value'] ?? '') }}"
>


                <div class="card">
                    <div class="card-img-container">
                        <img src="{{ $club['logo']['value'] ?? 'https://via.placeholder.com/150' }}"
                             class="img-fluid" style="max-height: 130px;">
                    </div>

                    <div class="card-body">
                        <div class="club-name">{{ $club['teamName']['value'] }}</div>

                        <div class="club-info mt-2">
                            <i class="fas fa-flag me-2"></i>{{ $club['country']['value'] }}
                        </div>

                        <div class="club-info">
                            <i class="fas fa-user-tie me-2"></i>{{ $club['coach']['value'] }}
                        </div>

                        @php
                            $clubId = str_replace('http://example.com/team/', '', $club['club']['value']);
                        @endphp

                        <a href="{{ route('club.show', $clubId) }}"
                            class="btn btn-primary-custom w-100 mt-3">
                            Detail Klub
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- NO RESULTS -->
        <div id="noResults" class="text-center no-results mt-5 d-none">
            <i class="fas fa-search"></i>
            <h3 class="mt-3">Tidak ada hasil ditemukan</h3>
        </div>
    </div>

    <!-- FOOTER -->
    <footer>
        <div class="container text-center">
            <p class="m-0">© 2023 Football Clubs Directory</p>
        </div>
    </footer>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // ==============================
    // ELEMENT DASAR
    // ==============================
    const searchInput   = document.getElementById("searchInput");
    const cards         = document.querySelectorAll(".club-card");
    const suggestionBox = document.getElementById("suggestionBox");
    const sortCountry   = document.getElementById("sortCountry");
    const noResults     = document.getElementById("noResults");

    // ==============================
    // 1. KUMPULKAN ENTITAS RDF
    // ==============================
    const allEntities = [];

    // CLUB
    @foreach ($results as $club)
        allEntities.push({
            name: "{{ strtolower($club['teamName']['value']) }}",
            type: "club"
        });
    @endforeach

    // STADIUM
    @foreach ($results as $club)
        @if(isset($club['stadiumName']['value']))
            allEntities.push({
                name: "{{ strtolower($club['stadiumName']['value']) }}",
                type: "stadium"
            });
        @endif
    @endforeach

    // PLAYERS
    @foreach ($results as $club)
        @php
            $playersStr = $club['players']['value'] ?? '';
            $playersArr = array_filter(array_map('trim', explode(',', $playersStr)));
        @endphp
        @foreach ($playersArr as $player)
            allEntities.push({
                name: "{{ strtolower($player) }}",
                type: "player"
            });
        @endforeach
    @endforeach

    // ==============================
    // 2. LEVENSHTEIN DISTANCE
    // ==============================
    function levenshteinDistance(a, b) {
        const matrix = [];
        const alen = a.length;
        const blen = b.length;

        for (let i = 0; i <= blen; i++) matrix[i] = [i];
        for (let j = 0; j <= alen; j++) matrix[0][j] = j;

        for (let i = 1; i <= blen; i++) {
            for (let j = 1; j <= alen; j++) {
                if (b.charAt(i - 1) === a.charAt(j - 1)) {
                    matrix[i][j] = matrix[i - 1][j - 1];
                } else {
                    matrix[i][j] = Math.min(
                        matrix[i - 1][j - 1] + 1,
                        matrix[i][j - 1] + 1,
                        matrix[i - 1][j] + 1
                    );
                }
            }
        }
        return matrix[blen][alen];
    }

    // ==============================
    // NORMALISASI UNTUK FUZZY SEARCH
    // ==============================
    function normalize(str) {
        return str
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "");
    }

    function fuzzyMatch(keyword, text) {
        keyword = normalize(keyword);
        text = normalize(text);

        if (!text) return false;
        if (text.includes(keyword)) return true;

        const dist = levenshteinDistance(keyword, text);
        const sim = 1 - (dist / Math.max(keyword.length, text.length));

        return sim >= 0.55;
    }

    // ==============================
    // SMART SUGGESTION
    // ==============================
    function findSmartSuggestion(keyword) {
        keyword = keyword.toLowerCase().trim();
        if (keyword.length < 3) return null;

        let bestMatch = null;
        let bestScore = -9999;

        allEntities.forEach(item => {
            const name = item.name;
            const type = item.type;

            const lev = levenshteinDistance(keyword, name);
            const similarity = 1 - (lev / Math.max(keyword.length, name.length));
            let score = similarity * 50;

            if (name.startsWith(keyword)) score += 40;
            if (Math.abs(keyword.length - name.length) <= 2) score += 10;
            if (type === "club") score += 50;
            if (type === "stadium") score += 30;
            if (type === "player") score += 10;

            if (score > bestScore) {
                bestScore = score;
                bestMatch = name;
            }
        });

        return bestScore < 50 ? null : bestMatch;
    }

    function showSuggestion(match) {
        if (!match) {
            suggestionBox.classList.add("d-none");
            return;
        }
        suggestionBox.innerHTML = `
            <div class="suggestion-item">
                <i class="fas fa-lightbulb me-2 text-warning"></i>
                Maksud Anda: <strong>${match}</strong>
            </div>
        `;
        suggestionBox.classList.remove("d-none");

        suggestionBox.onclick = () => {
            searchInput.value = match;
            searchInput.dispatchEvent(new Event("input"));
            suggestionBox.classList.add("d-none");
        };
    }

    // ==============================
    // 3. DATA CHATBOT
    // ==============================
    const clubData = [];
    document.querySelectorAll(".club-card").forEach(card => {
        clubData.push({
            name: (card.dataset.name || "").toLowerCase(),
            country: (card.dataset.country || "").toLowerCase(),
            coach: (card.dataset.coach || "").toLowerCase(),
            stadium: (card.dataset.stadium || "").toLowerCase(),
            players: (card.dataset.players || "")
                .split(',')
                .map(s => s.trim().toLowerCase())
                .filter(Boolean)
        });
    });

    // ==============================
    // 4. UTIL
    // ==============================
    function cap(str) {
        if (!str) return "";
        return str.replace(/\b\w/g, chr => chr.toUpperCase());
    }

    function botReply(text) {
        document.getElementById("chatContent").innerHTML += `
            <div style="margin:10px 0;text-align:left;">
                <div style="display:inline-block;background:#e8f0fe;padding:8px 12px;border-radius:12px;">
                    ${text}
                </div>
            </div>
        `;
    }

    function userReply(text) {
        document.getElementById("chatContent").innerHTML += `
            <div style="margin:10px 0;text-align:right;">
                <div style="display:inline-block;background:#d1ecf1;padding:8px 12px;border-radius:12px;">
                    ${text}
                </div>
            </div>
        `;
    }

    // ==============================
    // 5. INTENT DETECTION
    // ==============================
    function detectIntents(msg) {
    msg = msg.toLowerCase();
    const intents = [];

    if (msg.includes("pelatih") || msg.includes("coach")) intents.push("coach");
    if (msg.includes("negara")  || msg.includes("country") || msg.includes("asal")) intents.push("country");
    if (msg.includes("stadion") || msg.includes("stadium") || msg.includes("arena")) intents.push("stadium");
    if (msg.includes("pemain")  || msg.includes("player")) intents.push("playerlist");

    // kasus pertanyaan "siapa ..."
    if (msg.startsWith("siapa") && !intents.includes("coach")) intents.push("coach");

    return intents.length ? intents : ["unknown"];
}


    // ==============================
// 6. ENTITY EXTRACTION (CLUB, PLAYER, STADIUM)
// ==============================

// normalisasi untuk mengatasi "1. FC Köln" vs "fc koln"
function normalize(str) {
    return str
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/[^a-zA-Z ]/g, "")
        .toLowerCase()
        .trim();
}

// fuzzy matching Levenshtein
function fuzzyScore(a, b) {
    const dist = levenshteinDistance(a, b);
    return 1 - (dist / Math.max(a.length, b.length));
}

function extractClub(msg) {
    const query = normalize(msg);

    let bestMatch = null;
    let bestScore = 0;

    for (const c of clubData) {
        const name = normalize(c.name);
        if (!name) continue;

        // perfect match
        if (query === name) return c;

        // partial match
        if (name.includes(query) || query.includes(name)) return c;

        // fuzzy match
        const score = fuzzyScore(query, name);

        if (score > bestScore) {
            bestScore = score;
            bestMatch = c;
        }
    }

    return bestScore >= 0.55 ? bestMatch : null;
}

function extractPlayer(msg) {
    const query = normalize(msg);

    for (const c of clubData) {
        for (const p of c.players) {
            if (!p) continue;

            const pname = normalize(p);

            if (query === pname) return { player: p, club: c };
            if (pname.includes(query) || query.includes(pname)) return { player: p, club: c };

            const score = fuzzyScore(query, pname);
            if (score >= 0.65) return { player: p, club: c };
        }
    }
    return null;
}

function extractStadium(msg) {
    const query = normalize(msg);

    for (const c of clubData) {
        if (!c.stadium) continue;

        const stadium = normalize(c.stadium);

        if (query === stadium) return c;
        if (stadium.includes(query) || query.includes(stadium)) return c;

        const score = fuzzyScore(query, stadium);
        if (score >= 0.60) return c;
    }
    return null;
}


    // ==============================
    // 7. CHATBOT LOGIC
    // ==============================
    function processChat() {
        const input = document.getElementById("chatInput");
        const msg = input.value.trim();
        if (!msg) return;

        userReply(msg);
        input.value = "";

        const message = msg.toLowerCase();
        const intents  = detectIntents(message);
        const club    = extractClub(message);

        const foundPlayer = extractPlayer(message);
        if (foundPlayer) {
            botReply(`Pemain <b>${cap(foundPlayer.player)}</b> bermain untuk <b>${cap(foundPlayer.club.name)}</b>.`);
            return;
        }

        const stadiumClub = extractStadium(message);
        if (stadiumClub) {
            botReply(`Stadion <b>${cap(stadiumClub.name)}</b> adalah <b>${cap(stadiumClub.stadium)}</b>.`);
            return;
        }

        if (club) {
    let responses = [];

    // Loop semua intent yang ditemukan
    intents.forEach(intent => {

        if (intent === "coach") {
            responses.push(
                `Pelatih <b>${cap(club.name)}</b> adalah <b>${cap(club.coach)}</b>.`
            );
        }

        if (intent === "country") {
            responses.push(
                `<b>${cap(club.name)}</b> berasal dari <b>${cap(club.country)}</b>.`
            );
        }

        if (intent === "stadium") {
            responses.push(
                `Stadion <b>${cap(club.name)}</b> adalah <b>${cap(club.stadium || "Tidak diketahui")}</b>.`
            );
        }

        if (intent === "playerlist") {
            const list = club.players.map(cap).join(", ");
            responses.push(
                `Daftar pemain <b>${cap(club.name)}</b>:<br>${list}`
            );
        }
    });

    // Jika ada jawaban multi-intent
    if (responses.length > 0) {
        botReply(responses.join("<br>"));
        return;
    }

    // Jika tidak ada intent yang dikenali, tampilkan info klub lengkap
    botReply(`
        Klub <b>${cap(club.name)}</b><br>
        Negara: ${cap(club.country)}<br>
        Pelatih: ${cap(club.coach)}<br>
        Stadion: ${cap(club.stadium || "Tidak diketahui")}
    `);

    return;
}

        const fallback = [
            "Maaf, saya tidak menemukan itu di database RDF.",
            "Coba tanya tentang klub, pemain, pelatih, stadion, atau negara.",
            "Saya tidak paham maksudnya. Coba lebih spesifik."
        ];
        botReply(fallback[Math.floor(Math.random() * fallback.length)]);
    }

    // ==============================
    // 8. SEARCH FILTER + FUZZY (UPGRADE)
    // ==============================
    searchInput.addEventListener("input", function () {
        const keyword = this.value.toLowerCase().trim();
        let visibleCount = 0;

        document.body.style.background =
            keyword
                ? "linear-gradient(135deg, #bbdefb, #e1bee7)"
                : "linear-gradient(135deg, #f5f7fa, #f5f7fb)";

        cards.forEach(card => {
            const name    = card.dataset.name || "";
            const coach   = card.dataset.coach || "";
            const country = card.dataset.country || "";
            const players = card.dataset.players || "";
            const stadium = card.dataset.stadium || "";

            const match =
                !keyword ||
                fuzzyMatch(keyword, name) ||
                fuzzyMatch(keyword, coach) ||
                fuzzyMatch(keyword, country) ||
                fuzzyMatch(keyword, players) ||
                fuzzyMatch(keyword, stadium);

            card.style.display = match ? "block" : "none";
            if (match) visibleCount++;
        });

        noResults.classList.toggle("d-none", visibleCount !== 0);

        if (keyword.length >= 3 && visibleCount === 0) {
            const closest = findSmartSuggestion(keyword);
            showSuggestion(closest);
        } else {
            suggestionBox.classList.add("d-none");
        }
    });

    // ==============================
    // 9. SORTING NEGARA
    // ==============================
    sortCountry.addEventListener("change", function () {
        const cardsArr  = Array.from(cards);
        const container = document.getElementById("clubList");

        const sorted = cardsArr.sort((a, b) => {
            const ca = a.dataset.country || "";
            const cb = b.dataset.country || "";

            if (this.value === "asc") return ca.localeCompare(cb);
            if (this.value === "desc") return cb.localeCompare(ca);
            return 0;
        });

        sorted.forEach(card => container.appendChild(card));
    });

    // ==============================
    // 10. TUTUP SUGGESTION
    // ==============================
    document.addEventListener("click", function (e) {
        if (!suggestionBox.contains(e.target) && e.target !== searchInput) {
            suggestionBox.classList.add("d-none");
        }
    });

    // ==============================
    // 11. CHATBOT EVENT
    // ==============================
    const chatbotButton = document.getElementById("chatbotButton");
    const chatbotWindow = document.getElementById("chatbotWindow");
    const chatSend      = document.getElementById("chatSend");
    const chatInput     = document.getElementById("chatInput");

    chatbotButton.onclick = () => {
        chatbotWindow.style.display =
            (!chatbotWindow.style.display || chatbotWindow.style.display === "none")
                ? "flex"
                : "none";
    };

    chatSend.onclick = processChat;
    chatInput.addEventListener("keypress", e => {
        if (e.key === "Enter") processChat();
    });

    // ==============================
    // 12. ANIMASI KARTU
    // ==============================
    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll(".club-card").forEach((card, index) => {
            card.style.animationDelay = `${index * 0.08}s`;
        });
    });
</script>
