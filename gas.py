import requests
import time
import random
from rdflib import Graph, Namespace, URIRef, Literal
from rdflib.namespace import RDF, RDFS, XSD, FOAF

# Konfigurasi API
API_KEY = "55555c4cda3a48f3b150199921861699"
BASE_URL = "http://api.football-data.org/v4"

headers = {'X-Auth-Token': API_KEY}

# Namespace untuk RDF
SCHEMA = Namespace("http://schema.org/")
EX = Namespace("http://example.com/")
FOOTBALL = Namespace("http://example.org/football/")

def create_rdf_graph():
    """Membuat RDF graph dengan namespace"""
    g = Graph()
    
    # Bind namespaces
    g.bind("schema", SCHEMA)
    g.bind("ex", EX)
    g.bind("football", FOOTBALL)
    g.bind("rdfs", RDFS)
    g.bind("xsd", XSD)
    
    return g

def make_api_request(url):
    """Melakukan request API dengan error handling"""
    try:
        response = requests.get(url, headers=headers, timeout=10)
        
        if response.status_code == 200:
            return response.json()
        elif response.status_code == 429:
            print(f"⚠️  Rate limit hit, waiting 60 seconds...")
            time.sleep(60)
            return make_api_request(url)  # Retry
        elif response.status_code == 403:
            print(f"❌ Access forbidden for {url}")
            return None
        else:
            print(f"❌ HTTP {response.status_code} for {url}")
            return None
            
    except requests.exceptions.RequestException as e:
        print(f"❌ Request error: {e}")
        return None

def get_all_competitions():
    """Mendapatkan SEMUA kompetisi"""
    url = f"{BASE_URL}/competitions"
    data = make_api_request(url)
    
    if data:
        competitions = data.get('competitions', [])
        print(f"✅ Ditemukan {len(competitions)} kompetisi")
        return competitions
    return []

def get_teams_for_competition(competition_code):
    """Mendapatkan tim dari kompetisi tertentu"""
    url = f"{BASE_URL}/competitions/{competition_code}/teams"
    data = make_api_request(url)
    
    if data:
        teams = data.get('teams', [])
        return teams
    return []

def get_team_players(team_id):
    """Mendapatkan pemain dari tim"""
    url = f"{BASE_URL}/teams/{team_id}"
    data = make_api_request(url)
    
    if data:
        players = data.get('squad', [])
        return players
    return []

def get_starting_xi_players(players):
    """Memilih 11 pemain inti berdasarkan posisi"""
    if len(players) < 11:
        return players[:11]
    
    # Group players by position
    players_by_position = {}
    for player in players:
        position = player.get('position', 'Unknown')
        if position not in players_by_position:
            players_by_position[position] = []
        players_by_position[position].append(player)
    
    # Pilih 11 pemain inti dengan formasi 4-4-2
    starting_xi = []
    
    # 1 Goalkeeper
    goalkeeper_positions = ['Goalkeeper', 'Keeper']
    for pos in goalkeeper_positions:
        if pos in players_by_position:
            starting_xi.append(players_by_position[pos][0])
            break
    
    # 4 Defender
    defenders = []
    defender_positions = ['Centre-Back', 'Defender', 'Left-Back', 'Right-Back', 'Center Back']
    for pos in defender_positions:
        if pos in players_by_position:
            defenders.extend(players_by_position[pos])
    starting_xi.extend(defenders[:4])
    
    # 4 Midfielder
    midfielders = []
    midfielder_positions = ['Defensive Midfield', 'Central Midfield', 'Attacking Midfield', 
                           'Left Midfield', 'Right Midfield', 'Midfielder']
    for pos in midfielder_positions:
        if pos in players_by_position:
            midfielders.extend(players_by_position[pos])
    starting_xi.extend(midfielders[:4])
    
    # 2 Forward
    forwards = []
    forward_positions = ['Centre-Forward', 'Forward', 'Striker', 'Left Winger', 'Right Winger', 'Attack']
    for pos in forward_positions:
        if pos in players_by_position:
            forwards.extend(players_by_position[pos])
    starting_xi.extend(forwards[:2])
    
    # Jika masih kurang, tambah dari pemain lain
    if len(starting_xi) < 11:
        all_players = [p for p in players if p not in starting_xi]
        starting_xi.extend(all_players[:11-len(starting_xi)])
    
    return starting_xi[:11]

def add_competition_to_rdf(g, competition):
    """Menambahkan kompetisi ke RDF"""
    comp_id = competition.get('id')
    comp_uri = URIRef(f"{EX}league/{comp_id}")
    
    g.add((comp_uri, RDF.type, SCHEMA.SportsOrganization))
    g.add((comp_uri, SCHEMA.name, Literal(competition.get('name', 'Unknown'))))
    
    # Area information
    area = competition.get('area', {})
    if isinstance(area, dict):
        area_name = area.get('name', 'Unknown')
        g.add((comp_uri, SCHEMA.addressCountry, Literal(area_name)))
    
    # Competition code
    if competition.get('code'):
        g.add((comp_uri, EX.code, Literal(competition.get('code'))))
    
    return comp_uri

def add_team_to_rdf(g, team, competition_uri):
    """Menambahkan tim ke RDF dengan struktur seperti contoh"""
    team_id = team.get('id')
    if not team_id:
        return None
        
    team_uri = URIRef(f"{EX}team/{team_id}")
    
    # Basic team info seperti contoh
    g.add((team_uri, RDF.type, SCHEMA.SportsTeam))
    g.add((team_uri, SCHEMA.name, Literal(team.get('name', 'Unknown'))))
    
    # Address country
    area = team.get('area', {})
    if isinstance(area, dict):
        country = area.get('name', 'Unknown')
        g.add((team_uri, SCHEMA.addressCountry, Literal(country)))
    
    # Founding date
    if team.get('founded'):
        g.add((team_uri, SCHEMA.foundingDate, Literal(team['founded'], datatype=XSD.gYear)))
    
    # Home location (gunakan venue)
    if team.get('venue'):
        g.add((team_uri, SCHEMA.homeLocation, Literal(team['venue'])))
    
    # Logo
    if team.get('crest'):
        g.add((team_uri, SCHEMA.logo, URIRef(team['crest'])))
    
    # Competition
    g.add((team_uri, EX.competition, competition_uri))
    
    # Coach
    coach = team.get('coach', {})
    if coach and coach.get('name'):
        g.add((team_uri, SCHEMA.coach, Literal(coach['name'])))
    
    return team_uri

def add_player_to_rdf(g, player, team_uri):
    """Menambahkan pemain ke RDF dan link ke team"""
    player_name = player.get('name', 'Unknown')
    # Link player to team (seperti contoh - menggunakan literal name)
    g.add((team_uri, EX.player, Literal(player_name)))
    
    # Juga buat individual player resource
    player_id = player.get('id')
    if player_id:
        player_uri = URIRef(f"{EX}player/{player_id}")
        g.add((player_uri, RDF.type, SCHEMA.Person))
        g.add((player_uri, SCHEMA.name, Literal(player_name)))
        
        if player.get('position'):
            g.add((player_uri, EX.position, Literal(player.get('position'))))
        
        if player.get('nationality'):
            g.add((player_uri, SCHEMA.nationality, Literal(player.get('nationality'))))
        
        if player.get('dateOfBirth'):
            g.add((player_uri, SCHEMA.birthDate, Literal(player.get('dateOfBirth'))))
    
    return True

def main():
    print("🔥 FIXED - GAS SEMUA LIGA & 300+ KLUB!")
    print("=" * 50)
    
    # Test API
    test_url = f"{BASE_URL}/competitions/PL"
    response = requests.get(test_url, headers=headers)
    if response.status_code != 200:
        print("❌ Gagal terkoneksi dengan API")
        print(f"Status: {response.status_code}, Response: {response.text}")
        return
    
    # Inisialisasi RDF Graph
    g = create_rdf_graph()
    
    # Get ALL competitions
    print("📋 MENGAMBIL SEMUA KOMPETISI...")
    all_competitions = get_all_competitions()
    
    if not all_competitions:
        print("❌ Tidak ada kompetisi ditemukan")
        return
    
    # Filter kompetisi yang likely punya data teams
    # Priority: League competitions first
    league_competitions = [c for c in all_competitions if c.get('type') == 'LEAGUE']
    cup_competitions = [c for c in all_competitions if c.get('type') != 'LEAGUE']
    
    # Urutkan berdasarkan prioritas
    priority_codes = ['PL', 'PD', 'BL1', 'SA', 'FL1', 'DED', 'PPL', 'BSA', 'CLI']  # Top leagues
    prioritized_leagues = []
    other_leagues = []
    
    for comp in league_competitions:
        if comp.get('code') in priority_codes:
            prioritized_leagues.append(comp)
        else:
            other_leagues.append(comp)
    
    # Gabungkan semua kompetisi dengan prioritas
    sorted_competitions = prioritized_leagues + other_leagues + cup_competitions
    
    total_teams = 0
    total_players = 0
    target_teams = 300
    
    print(f"🎯 TARGET: {target_teams} KLUB")
    print(f"📊 PRIORITAS: {len(prioritized_leagues)} liga top")
    print("=" * 40)
    
    # Process competitions dengan prioritas
    for i, comp in enumerate(sorted_competitions, 1):
        if total_teams >= target_teams:
            break
            
        comp_code = comp.get('code', comp.get('id'))
        comp_name = comp.get('name')
        comp_type = comp.get('type', 'UNKNOWN')
        
        print(f"\n[{i}/{len(sorted_competitions)}] 🏆 {comp_type}: {comp_name}")
        print("-" * 50)
        
        # Add competition
        comp_uri = add_competition_to_rdf(g, comp)
        
        # Get teams from this competition
        teams = get_teams_for_competition(comp_code)
        
        if not teams:
            print(f"   ⚠️  Tidak ada tim ditemukan di API")
            # Skip untuk kompetisi tanpa tim
            continue
        
        print(f"   ⚽ Ditemukan {len(teams)} tim")
        
        # Process teams until we reach 300 total
        teams_to_process = teams[:target_teams - total_teams] if total_teams + len(teams) > target_teams else teams
        
        successful_teams = 0
        for j, team in enumerate(teams_to_process, 1):
            team_name = team.get('name')
            team_id = team.get('id')
            
            print(f"   [{j}/{len(teams_to_process)}] 🏁 {team_name}")
            
            # Add team
            team_uri = add_team_to_rdf(g, team, comp_uri)
            
            if team_uri:
                # Get players dengan error handling
                players = get_team_players(team_id)
                
                if players:
                    # Pilih 11 pemain inti
                    starting_xi = get_starting_xi_players(players)
                    total_players += len(starting_xi)
                    successful_teams += 1
                    
                    print(f"      👥 {len(starting_xi)} Pemain Inti:")
                    
                    # Add 11 pemain inti
                    for k, player in enumerate(starting_xi, 1):
                        player_name = player.get('name', 'Unknown')
                        position = player.get('position', 'Unknown')
                        if k <= 3:  # Hanya print 3 pertama untuk hemat space
                            print(f"         {k:2d}. {player_name} ({position})")
                        elif k == 4:
                            print(f"         ... dan {len(starting_xi) - 3} pemain lainnya")
                        
                        add_player_to_rdf(g, player, team_uri)
                else:
                    print(f"      ⚠️  Tidak ada data pemain")
                    # Tetap hitung tim meski tanpa pemain
                    successful_teams += 1
                
                total_teams = successful_teams
                
                # Progress update
                print(f"      📊 Progress: {total_teams}/{target_teams} klub")
                
                # Rate limiting yang lebih agresif
                time.sleep(2)  # Jeda 2 detik antara tim
            
            if total_teams >= target_teams:
                break
        
        # Rate limiting antara kompetisi
        if total_teams < target_teams:
            wait_time = random.randint(5, 10)
            print(f"   ⏳ Jeda {wait_time} detik...")
            time.sleep(wait_time)
    
    # Save to file
    output_file = "MASSIVE_FOOTBALL_DATASET_FIXED.rdf"
    print(f"\n💾 MENYIMPAN FILE RDF MASSAL...")
    
    # Serialize dengan format XML/RDF
    g.serialize(destination=output_file, format='xml', encoding='utf-8')
    print(f"✅ Berhasil disimpan: {output_file}")
    
    # Final Statistics
    print(f"\n🎉 DATA SCRAPING SELESAI! STATISTIK FINAL:")
    print("=" * 50)
    
    teams_count = len(list(g.subjects(RDF.type, SCHEMA.SportsTeam)))
    players_count = len([p for p in g.subjects(RDF.type, SCHEMA.Person)])
    competitions_count = len(list(g.subjects(RDF.type, SCHEMA.SportsOrganization)))
    
    # Hitung total player references
    player_refs = len(list(g.predicates(EX.player)))
    
    print(f"🏆 LIGA: {competitions_count}")
    print(f"⚽ KLUB: {teams_count} (TARGET: {target_teams})")
    print(f"👤 PEMAIN INTI: {player_refs} references")
    print(f"👤 PEMAIN INDIVIDUAL: {players_count} resources")
    print(f"📈 TOTAL TRIPLE RDF: {len(g):,}")
    
    # Show sample structure
    print(f"\n🔍 CONTOH STRUKTUR OUTPUT:")
    print("=" * 40)
    
    # Ambil sample team untuk ditampilkan
    sample_teams = list(g.subjects(RDF.type, SCHEMA.SportsTeam))[:1]
    for team_uri in sample_teams:
        team_name = g.value(team_uri, SCHEMA.name)
        country = g.value(team_uri, SCHEMA.addressCountry)
        founded = g.value(team_uri, SCHEMA.foundingDate)
        location = g.value(team_uri, SCHEMA.homeLocation)
        coach = g.value(team_uri, SCHEMA.coach)
        
        print(f"\n<rdf:Description rdf:about=\"{team_uri}\">")
        print(f"    <rdf:type rdf:resource=\"http://schema.org/SportsTeam\"/>")
        print(f"    <schema:name>{team_name}</schema:name>")
        
        if country:
            print(f"    <schema:addressCountry>{country}</schema:addressCountry>")
        
        if founded:
            print(f"    <schema:foundingDate rdf:datatype=\"http://www.w3.org/2001/XMLSchema#gYear\">{founded}</schema:foundingDate>")
        
        if location:
            print(f"    <schema:homeLocation>{location}</schema:homeLocation>")
        
        # Logo
        logo = g.value(team_uri, SCHEMA.logo)
        if logo:
            print(f"    <schema:logo>{logo}</schema:logo>")
        
        # Competition
        comps = list(g.objects(team_uri, EX.competition))
        for comp in comps:
            comp_name = g.value(comp, SCHEMA.name)
            if comp_name:
                print(f"    <ex:competition rdf:resource=\"{comp}\"/>")
        
        if coach:
            print(f"    <schema:coach>{coach}</schema:coach>")
        
        # Players (11 pemain inti)
        players = list(g.objects(team_uri, EX.player))[:11]
        for player in players:
            print(f"    <ex:player>{player}</ex:player>")
        
        print(f"</rdf:Description>")

if __name__ == "__main__":
    main()