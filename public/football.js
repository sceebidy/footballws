// Konfigurasi dan Data
const CONFIG = {
    searchDelay: 500,
    maxSuggestions: 6,
    minSearchLength: 1
};

const TYPO_CORRECTIONS = {
    // Negara
    'jerman': 'germany',
    'inggris': 'england',
    'spanyol': 'spain',
    'itali': 'italy',
    'prancis': 'france',
    'belanda': 'netherlands',
    'portugal': 'portugal',
    'brasil': 'brazil',
    'argentina': 'argentina',
    
    // Klub
    'mu': 'manchester united',
    'city': 'manchester city',
    'chelsea': 'chelsea',
    'liverpool': 'liverpool',
    'arsenal': 'arsenal',
    'barca': 'barcelona',
    'real': 'real madrid',
    'bayern': 'bayern munich',
    
    // Pelatih
    'pep': 'pep guardiola',
    'klopp': 'jurgen klopp',
    'mourinho': 'jose mourinho'
};

const COUNTRIES = [
    'England', 'Spain', 'Italy', 'Germany', 'France',
    'Netherlands', 'Portugal', 'Turkey', 'Belgium', 'Russia',
    'Ukraine', 'Argentina', 'Brazil', 'Mexico', 'USA',
    'Japan', 'South Korea', 'Saudi Arabia', 'Qatar', 'UAE',
    'Australia', 'China', 'South Africa', 'Thailand',
    'Malaysia', 'Singapore'
];

// Elemen DOM
const elements = {
    searchInput: document.getElementById('search-input'),
    criteriaSelect: document.getElementById('criteria-select'),
    suggestions: document.getElementById('suggestions'),
    resultsContainer: document.getElementById('results-container'),
    loading: document.getElementById('loading'),
    filterBtns: document.querySelectorAll('.filter-btn'),
    countryFilter: document.getElementById('country-filter'),
    countryGrid: document.getElementById('country-grid'),
    clearSearch: document.getElementById('clear-search'),
    didYouMean: document.getElementById('did-you-mean'),
    correctionSuggestions: document.getElementById('correction-suggestions')
};

// State
let state = {
    searchTimeout: null,
    currentSearch: '',
    activeFilter: 'all'
};

// Inisialisasi
function init() {
    loadCountries();
    setupEventListeners();
    setInitialState();
}

// Load countries data
function loadCountries() {
    const sortedCountries = [...COUNTRIES].sort();
    renderCountryFilter(sortedCountries);
}

// Render country filter buttons
function renderCountryFilter(countries) {
    elements.countryGrid.innerHTML = '';
    countries.forEach(country => {
        const button = document.createElement('button');
        button.className = 'country-btn';
        button.textContent = country;
        button.addEventListener('click', () => handleCountrySelect(country));
        elements.countryGrid.appendChild(button);
    });
}

// Setup event listeners
function setupEventListeners() {
    // Search input events
    elements.searchInput.addEventListener('input', handleSearchInput);
    elements.searchInput.addEventListener('keydown', handleSearchKeydown);
    
    // Clear search button
    elements.clearSearch.addEventListener('click', clearSearch);
    
    // Filter buttons
    elements.filterBtns.forEach(btn => {
        btn.addEventListener('click', () => handleFilterClick(btn));
    });
    
    // Criteria select
    elements.criteriaSelect.addEventListener('change', handleCriteriaChange);
    
    // Click outside suggestions
    document.addEventListener('click', handleClickOutside);
}

// Set initial state from URL
function setInitialState() {
    const urlParams = new URLSearchParams(window.location.search);
    const initialCriteria = urlParams.get('criteria') || 'all';
    elements.criteriaSelect.value = initialCriteria;
    
    const initialFilterBtn = document.querySelector(`.filter-btn[data-criteria="${initialCriteria}"]`);
    if (initialFilterBtn) {
        initialFilterBtn.classList.add('active');
        state.activeFilter = initialCriteria;
    }
    
    elements.searchInput.focus();
}

// Event Handlers
function handleSearchInput() {
    const query = this.value.trim();
    
    // Toggle clear button
    toggleClearButton(query);
    
    clearTimeout(state.searchTimeout);
    elements.loading.style.display = 'block';
    elements.didYouMean.style.display = 'none';
    
    if (query.length === 0) {
        elements.suggestions.style.display = 'none';
        performSearch('');
        return;
    }
    
    if (query.length >= CONFIG.minSearchLength) {
        fetchAutosuggest(query);
    }
    
    state.searchTimeout = setTimeout(() => {
        performSearch(query);
    }, CONFIG.searchDelay);
}

function handleSearchKeydown(e) {
    if (e.key === 'Escape') {
        clearSearch();
    }
}

function handleFilterClick(button) {
    const criteria = button.dataset.criteria;
    elements.criteriaSelect.value = criteria;
    
    // Update active state
    elements.filterBtns.forEach(b => b.classList.remove('active'));
    button.classList.add('active');
    state.activeFilter = criteria;
    
    // Show/hide country filter
    toggleCountryFilter(criteria);
    
    // Perform search if there's a query
    const query = elements.searchInput.value.trim();
    if (query && criteria !== 'country') {
        performSearch(query);
    }
}

function handleCriteriaChange() {
    const query = elements.searchInput.value.trim();
    if (query) {
        performSearch(query);
    }
}

function handleCountrySelect(country) {
    // Remove active class from all country buttons
    document.querySelectorAll('.country-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Add active class to clicked button
    event.target.classList.add('active');
    
    // Perform search with selected country
    elements.searchInput.value = country;
    elements.criteriaSelect.value = 'country';
    performSearch(country);
}

function handleClickOutside(e) {
    if (!elements.searchInput.contains(e.target) && !elements.suggestions.contains(e.target)) {
        elements.suggestions.style.display = 'none';
    }
}

// Utility Functions
function toggleClearButton(query) {
    elements.clearSearch.style.display = query ? 'block' : 'none';
}

function toggleCountryFilter(criteria) {
    elements.countryFilter.style.display = criteria === 'country' ? 'block' : 'none';
}

function clearSearch() {
    elements.searchInput.value = '';
    elements.clearSearch.style.display = 'none';
    elements.suggestions.style.display = 'none';
    elements.didYouMean.style.display = 'none';
    performSearch('');
    elements.searchInput.focus();
}

// Search Functions
function performSearch(query) {
    const criteria = elements.criteriaSelect.value;
    state.currentSearch = query;
    
    if (query.length === 0) {
        window.location.href = '/football';
        return;
    }
    
    fetch(`/football/realtime-search?q=${encodeURIComponent(query)}&criteria=${criteria}`)
        .then(response => response.json())
        .then(data => {
            elements.loading.style.display = 'none';
            
            if (data.error) {
                showError(data.error);
            } else {
                updateURL(query, criteria);
                handleSearchResults(data.results, query);
            }
        })
        .catch(error => {
            elements.loading.style.display = 'none';
            console.error('Error searching:', error);
            showError('Gagal melakukan pencarian');
        });
}

function handleSearchResults(results, query) {
    if (results.length === 0) {
        const correction = checkTypoCorrection(query);
        if (correction) {
            showDidYouMean(query, correction);
        }
    }
    updateResults(results, query);
}

// Autosuggest Functions
function fetchAutosuggest(query) {
    fetch(`/football/autosuggest?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            showSuggestions(data, query);
        })
        .catch(error => {
            console.error('Error fetching suggestions:', error);
        });
}

function showSuggestions(suggestionsData, query) {
    elements.suggestions.innerHTML = '';
    
    if (suggestionsData.length === 0) {
        elements.suggestions.style.display = 'none';
        return;
    }
    
    const limitedSuggestions = suggestionsData.slice(0, CONFIG.maxSuggestions);
    
    limitedSuggestions.forEach(item => {
        const div = document.createElement('div');
        div.className = 'suggestion-item';
        
        let displayText = item.team?.value || item.country?.value || 
                        item.coach?.value || item.stadium?.value || 
                        item.location?.value;
        
        if (displayText) {
            div.textContent = displayText;
            div.addEventListener('click', () => {
                elements.searchInput.value = displayText;
                elements.suggestions.style.display = 'none';
                performSearch(displayText);
            });
            elements.suggestions.appendChild(div);
        }
    });
    
    elements.suggestions.style.display = 'block';
}

// Typo Correction Functions
function checkTypoCorrection(query) {
    const lowerQuery = query.toLowerCase().trim();
    
    // Exact match
    if (TYPO_CORRECTIONS[lowerQuery]) {
        return TYPO_CORRECTIONS[lowerQuery];
    }
    
    // Similarity check
    let bestMatch = null;
    let bestScore = 0;
    
    for (const [typo, correction] of Object.entries(TYPO_CORRECTIONS)) {
        const similarity = calculateSimilarity(lowerQuery, typo);
        if (similarity > 0.7 && similarity > bestScore) {
            bestScore = similarity;
            bestMatch = correction;
        }
    }
    
    return bestMatch;
}

function calculateSimilarity(str1, str2) {
    const longer = str1.length > str2.length ? str1 : str2;
    const shorter = str1.length > str2.length ? str2 : str1;
    
    if (longer.length === 0) return 1.0;
    
    return (longer.length - editDistance(longer, shorter)) / parseFloat(longer.length);
}

function editDistance(s1, s2) {
    s1 = s1.toLowerCase();
    s2 = s2.toLowerCase();
    
    const costs = [];
    for (let i = 0; i <= s1.length; i++) {
        let lastValue = i;
        for (let j = 0; j <= s2.length; j++) {
            if (i === 0) {
                costs[j] = j;
            } else {
                if (j > 0) {
                    let newValue = costs[j - 1];
                    if (s1.charAt(i - 1) !== s2.charAt(j - 1)) {
                        newValue = Math.min(Math.min(newValue, lastValue), costs[j]) + 1;
                    }
                    costs[j - 1] = lastValue;
                    lastValue = newValue;
                }
            }
        }
        if (i > 0) costs[s2.length] = lastValue;
    }
    return costs[s2.length];
}

function showDidYouMean(originalQuery, correction) {
    elements.correctionSuggestions.innerHTML = '';
    
    const suggestionSpan = document.createElement('span');
    suggestionSpan.className = 'correction-suggestion';
    suggestionSpan.textContent = correction;
    suggestionSpan.addEventListener('click', function() {
        elements.searchInput.value = correction;
        performSearch(correction);
        elements.didYouMean.style.display = 'none';
    });
    
    elements.correctionSuggestions.appendChild(suggestionSpan);
    elements.didYouMean.style.display = 'block';
}

// UI Update Functions
function updateURL(query, criteria) {
    const newUrl = `/football?q=${encodeURIComponent(query)}&criteria=${criteria}`;
    window.history.pushState({}, '', newUrl);
}

function showError(message) {
    elements.resultsContainer.innerHTML = `
        <div class="empty-state">
            <h3>Terjadi Kesalahan</h3>
            <p>${message}</p>
        </div>
    `;
}

function updateResults(results, searchQuery) {
    if (results.length === 0) {
        elements.resultsContainer.innerHTML = `
            <div class="empty-state">
                <h3>Data Tidak Ditemukan</h3>
                <p>Tidak ada hasil yang cocok dengan pencarian "<span class="search-query">${searchQuery}</span>"</p>
            </div>
        `;
        return;
    }
    
    let html = `
        <div class="results-header">
            <div class="results-count">
                Menampilkan <strong>${results.length}</strong> hasil
                untuk "<span class="search-query">${searchQuery}</span>"
            </div>
        </div>
        <div class="results-grid">
    `;
    
    results.forEach(result => {
        const clubUri = result.club?.value;
        const id = clubUri ? clubUri.split('/').pop() : '';
        const detailUrl = id ? `/football/${id}` : '#';
        
        html += `
            <div class="club-card">
                <div class="club-header">
                    <div>
                        <div class="club-name">${result.team?.value || 'N/A'}</div>
                        <div class="club-country">${result.country?.value || 'N/A'}</div>
                    </div>
                </div>
                <div class="club-details">
                    <div class="detail-item">
                        <span class="detail-label">Lokasi</span>
                        <span class="detail-value">${result.location?.value || 'N/A'}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Stadion</span>
                        <span class="detail-value">${result.stadium?.value || 'N/A'}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Pelatih</span>
                        <span class="detail-value">${result.coach?.value || 'N/A'}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Pemilik</span>
                        <span class="detail-value">${result.owner?.value || 'N/A'}</span>
                    </div>
                </div>
                ${id ? `
                <div class="club-actions">
                    <a href="${detailUrl}" class="detail-link">Lihat Detail Lengkap</a>
                </div>
                ` : ''}
            </div>
        `;
    });
    
    html += `</div>`;
    
    // Add statistics
    const uniqueCountries = [...new Set(results.map(r => r.country?.value).filter(Boolean))];
    const uniqueCoaches = [...new Set(results.map(r => r.coach?.value).filter(Boolean))];
    
    html += `
        <div class="statistics">
            <div class="stat-card">
                <div class="stat-number">${results.length}</div>
                <div class="stat-label">Total Klub Ditemukan</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">${uniqueCountries.length}</div>
                <div class="stat-label">Negara Berbeda</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">${uniqueCoaches.length}</div>
                <div class="stat-label">Pelatih Berbeda</div>
            </div>
        </div>
    `;
    
    elements.resultsContainer.innerHTML = html;
}

// Initialize the application
document.addEventListener('DOMContentLoaded', init);