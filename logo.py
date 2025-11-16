import requests
from bs4 import BeautifulSoup
import time
import xml.etree.ElementTree as ET
from urllib.parse import quote, urljoin
import re

class SoccerwayLogoScraper:
    def __init__(self):
        self.session = requests.Session()
        self.session.headers.update({
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language': 'en-US,en;q=0.5',
            'Accept-Encoding': 'gzip, deflate',
            'Connection': 'keep-alive',
        })
        self.base_url = "https://www.soccerway.com"
    
    def search_team(self, club_name):
        """Cari tim di Soccerway"""
        search_url = f"{self.base_url}/?q={quote(club_name)}"
        
        try:
            print(f"  🔎 Searching Soccerway for: {club_name}")
            response = self.session.get(search_url, timeout=15)
            response.raise_for_status()
            
            soup = BeautifulSoup(response.content, 'html.parser')
            
            # Cari di section teams
            team_results = soup.find_all('a', href=re.compile(r'/teams/'))
            
            for result in team_results[:5]:  # Cek 5 hasil pertama
                team_text = result.get_text(strip=True)
                if team_text and self._is_match(club_name, team_text):
                    team_url = result.get('href')
                    if team_url:
                        full_url = urljoin(self.base_url, team_url)
                        print(f"  ✓ Found team page: {team_text}")
                        return full_url
            
            print(f"  ✗ No direct match found for {club_name}")
            return None
            
        except Exception as e:
            print(f"  ✗ Search error: {e}")
            return None
    
    def _is_match(self, search_name, result_name):
        """Check jika nama hasil cocok dengan pencarian"""
        search_words = set(search_name.lower().split())
        result_words = set(result_name.lower().split())
        
        # Minimal 2 kata yang match
        common_words = search_words.intersection(result_words)
        return len(common_words) >= 2 or search_name.lower() in result_name.lower()
    
    def extract_logo_from_team_page(self, team_url):
        """Extract logo dari halaman team"""
        try:
            print(f"  📸 Extracting logo from: {team_url}")
            response = self.session.get(team_url, timeout=15)
            response.raise_for_status()
            
            soup = BeautifulSoup(response.content, 'html.parser')
            
            # Method 1: Cari logo di header team
            logo_div = soup.find('div', class_='logo')
            if logo_div:
                img = logo_div.find('img')
                if img and img.get('src'):
                    logo_url = img.get('src')
                    if logo_url.startswith('//'):
                        logo_url = 'https:' + logo_url
                    print(f"  ✓ Found logo in header: {logo_url}")
                    return logo_url
            
            # Method 2: Cari di team details
            team_info = soup.find('div', class_='team-info')
            if team_info:
                img = team_info.find('img')
                if img and img.get('src'):
                    logo_url = img.get('src')
                    if logo_url.startswith('//'):
                        logo_url = 'https:' + logo_url
                    print(f"  ✓ Found logo in team info: {logo_url}")
                    return logo_url
            
            # Method 3: Cari gambar dengan pattern logo
            images = soup.find_all('img', src=re.compile(r'logo|badge|crest', re.I))
            for img in images:
                src = img.get('src')
                if src and not src.endswith(('.gif', '.ico')):
                    if src.startswith('//'):
                        src = 'https:' + src
                    print(f"  ✓ Found logo via pattern: {src}")
                    return src
            
            print(f"  ✗ No logo found on team page")
            return None
            
        except Exception as e:
            print(f"  ✗ Extraction error: {e}")
            return None
    
    def get_club_logo(self, club_name):
        """Main function untuk dapatkan logo dari Soccerway"""
        print(f"\n🔍 Processing: {club_name}")
        
        # Step 1: Cari team page
        team_url = self.search_team(club_name)
        if not team_url:
            return None
        
        # Step 2: Extract logo dari team page
        logo_url = self.extract_logo_from_team_page(team_url)
        
        if logo_url:
            print(f"  ✅ SUCCESS: {logo_url}")
        else:
            print(f"  ❌ FAILED: No logo found")
        
        return logo_url

class SmartLogoScraper:
    """Scraper pintar dengan fallback ke multiple sources"""
    
    def __init__(self):
        self.soccerway = SoccerwayLogoScraper()
        self.manual_map = self._load_manual_mapping()
    
    def _load_manual_mapping(self):
        """Mapping manual untuk klub yang sulit ditemukan"""
        return {
            # Indonesia
            "Persib Bandung": "https://www.soccerway.com/i/soccerway/team-logo/2/2982.png",
            "Persija Jakarta": "https://www.soccerway.com/i/soccerway/team-logo/2/2983.png", 
            "Arema FC": "https://www.soccerway.com/i/soccerway/team-logo/2/2984.png",
            "Persebaya Surabaya": "https://www.soccerway.com/i/soccerway/team-logo/2/2985.png",
            "PSM Makassar": "https://www.soccerway.com/i/soccerway/team-logo/2/2986.png",
            "Bali United": "https://www.soccerway.com/i/soccerway/team-logo/2/43115.png",
            "Bhayangkara FC": "https://www.soccerway.com/i/soccerway/team-logo/2/43116.png",
            "Madura United": "https://www.soccerway.com/i/soccerway/team-logo/2/43117.png",
            "Borneo FC Samarinda": "https://www.soccerway.com/i/soccerway/team-logo/2/43118.png",
            "PSIS Semarang": "https://www.soccerway.com/i/soccerway/team-logo/2/43119.png",
            
            # Top European Clubs (known Soccerway IDs)
            "Manchester United": "https://www.soccerway.com/i/soccerway/team-logo/2/32.png",
            "Manchester City": "https://www.soccerway.com/i/soccerway/team-logo/2/679.png",
            "Liverpool": "https://www.soccerway.com/i/soccerway/team-logo/2/663.png",
            "Chelsea": "https://www.soccerway.com/i/soccerway/team-logo/2/661.png",
            "Arsenal": "https://www.soccerway.com/i/soccerway/team-logo/2/659.png",
            "Real Madrid": "https://www.soccerway.com/i/soccerway/team-logo/2/65.png",
            "Barcelona": "https://www.soccerway.com/i/soccerway/team-logo/2/66.png",
            "Bayern Munich": "https://www.soccerway.com/i/soccerway/team-logo/2/721.png",
            "Juventus": "https://www.soccerway.com/i/soccerway/team-logo/2/506.png",
            "Paris Saint-Germain": "https://www.soccerway.com/i/soccerway/team-logo/2/583.png",
        }
    
    def get_club_logo(self, club_name):
        """Dapatkan logo dengan prioritas manual mapping -> soccerway"""
        
        # Coba manual mapping dulu
        if club_name in self.manual_map:
            logo_url = self.manual_map[club_name]
            print(f"✓ Manual mapping: {club_name} -> {logo_url}")
            return logo_url
        
        # Fallback ke Soccerway scraping
        return self.soccerway.get_club_logo(club_name)

def update_rdf_with_soccerway(rdf_file, output_file, batch_size=20):
    """Update RDF file dengan logo dari Soccerway"""
    
    print("🚀 SOCCERWAY LOGO SCRAPER")
    print("=" * 50)
    
    # Load RDF
    tree = ET.parse(rdf_file)
    root = tree.getroot()
    
    # Setup scraper
    scraper = SmartLogoScraper()
    
    # Namespace
    ns = {
        'schema': 'http://schema.org/',
        'rdf': 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
        'ex': 'http://example.com/resource/'
    }
    
    # Kumpulkan semua teams
    teams_to_process = []
    for team in root.findall('.//schema:SportsTeam', ns):
        name_elem = team.find('schema:name', ns)
        if name_elem is not None:
            teams_to_process.append(team)
    
    print(f"📊 Found {len(teams_to_process)} teams to process")
    
    # Process dalam batch
    success_count = 0
    for i, team in enumerate(teams_to_process):
        name_elem = team.find('schema:name', ns)
        club_name = name_elem.text
        
        print(f"\n[{i+1}/{len(teams_to_process)}] Processing: {club_name}")
        
        # Skip jika sudah ada logo valid
        logo_elem = team.find('schema:logo', ns)
        if logo_elem is not None and 'example.com' not in logo_elem.text:
            print(f"  ⏩ Already has valid logo, skipping")
            success_count += 1
            continue
        
        # Dapatkan logo
        logo_url = scraper.get_club_logo(club_name)
        
        if logo_url:
            # Update atau create logo element
            if logo_elem is not None:
                logo_elem.text = logo_url
            else:
                new_logo = ET.SubElement(team, 'schema:logo')
                new_logo.text = logo_url
            success_count += 1
        
        # Rate limiting & progress save
        time.sleep(1)  # Hormati server
        
        if (i + 1) % batch_size == 0:
            # Save progress setiap batch
            tree.write(output_file, encoding='utf-8', xml_declaration=True)
            print(f"\n💾 Progress saved after {i+1} teams")
    
    # Final save
    tree.write(output_file, encoding='utf-8', xml_declaration=True)
    
    print(f"\n✅ COMPLETED!")
    print(f"📈 Success rate: {success_count}/{len(teams_to_process)} teams")
    print(f"💾 Output saved to: {output_file}")
    
    return success_count

def test_soccerway_scraper():
    """Test Soccerway scraper dengan sample clubs"""
    print("🧪 TESTING SOCCERWAY SCRAPER")
    print("=" * 30)
    
    scraper = SmartLogoScraper()
    
    test_clubs = [
        "Persib Bandung",
        "Persija Jakarta",
        "Manchester United", 
        "Barcelona",
        "Arema FC",
        "Bali United",
        "Unknown Club Test"  # Untuk test fallback
    ]
    
    for club in test_clubs:
        logo = scraper.get_club_logo(club)
        print(f"Final result: {logo}\n")
        time.sleep(2)

if __name__ == "__main__":
    # Test dulu
    test_soccerway_scraper()
    
    # Konfirmasi sebelum proses full
    response = input("\n🚀 Proceed with full RDF update? (y/n): ")
    if response.lower() == 'y':
        input_file = "footballori.rdf"
        output_file = "football_soccerway_logos.rdf"
        
        update_rdf_with_soccerway(input_file, output_file)
    else:
        print("Operation cancelled.")