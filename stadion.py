import requests
from bs4 import BeautifulSoup
import json
import time
import re
import xml.etree.ElementTree as ET
from datetime import datetime

class StadiumScraper:
    def __init__(self):
        self.session = requests.Session()
        self.session.headers.update({
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        })
    
    def get_wikipedia_stadium_data(self, stadium_name, team_name=""):
        """
        Scrape data stadium dari Wikipedia
        """
        print(f"Scraping data untuk: {stadium_name}")
        
        stadium_data = {
            'name': stadium_name,
            'capacity': '',
            'location': '',
            'coordinates': '',
            'opened': '',
            'owner': '',
            'operator': '',
            'construction_cost': '',
            'architect': '',
            'record_attendance': '',
            'field_size': '',
            'surface': 'Grass',
            'renovations': ''
        }
        
        try:
            # Cari halaman Wikipedia
            search_url = f"https://en.wikipedia.org/w/api.php"
            params = {
                'action': 'query',
                'list': 'search',
                'srsearch': f"{stadium_name} stadium",
                'format': 'json'
            }
            
            response = self.session.get(search_url, params=params)
            if response.status_code == 200:
                search_data = response.json()
                
                if search_data['query']['search']:
                    page_title = search_data['query']['search'][0]['title']
                    
                    # Dapatkan konten halaman
                    content_params = {
                        'action': 'query',
                        'prop': 'extracts|coordinates',
                        'titles': page_title,
                        'explaintext': True,
                        'format': 'json'
                    }
                    
                    content_response = self.session.get(search_url, params=content_params)
                    if content_response.status_code == 200:
                        content_data = content_response.json()
                        pages = content_data['query']['pages']
                        page_id = list(pages.keys())[0]
                        page_content = pages[page_id]
                        
                        # Extract coordinates
                        if 'coordinates' in page_content:
                            coords = page_content['coordinates'][0]
                            stadium_data['coordinates'] = f"{coords['lat']},{coords['lon']}"
                        
                        # Extract text content untuk parsing manual
                        extract = page_content.get('extract', '')
                        
                        # Parse data dari infobox menggunakan regex
                        stadium_data.update(self.parse_wikipedia_infobox(extract))
                        
            time.sleep(1)  # Rate limiting
            
        except Exception as e:
            print(f"Error scraping Wikipedia untuk {stadium_name}: {e}")
        
        return stadium_data
    
    def parse_wikipedia_infobox(self, text):
        """
        Parse data dari teks Wikipedia infobox
        """
        data = {}
        
        # Pattern untuk kapasitas
        capacity_patterns = [
            r'capacity.*?(\d{1,6}[,\d]*)',
            r'seating capacity.*?(\d{1,6}[,\d]*)',
            r'(\d{1,6}[,\d]*).*?seats'
        ]
        
        for pattern in capacity_patterns:
            match = re.search(pattern, text, re.IGNORECASE)
            if match:
                data['capacity'] = match.group(1).replace(',', '')
                break
        
        # Pattern untuk tahun pembukaan
        opened_patterns = [
            r'opened.*?(\d{4})',
            r'built.*?(\d{4})',
            r'opened.*?(\d{1,2}\s+\w+\s+\d{4})'
        ]
        
        for pattern in opened_patterns:
            match = re.search(pattern, text, re.IGNORECASE)
            if match:
                data['opened'] = match.group(1)
                break
        
        # Pattern untuk pemilik
        owner_patterns = [
            r'owner.*?([^\n,.]+)',
            r'owner.*?([A-Z][a-z]+(?:\s+[A-Z][a-z]+)*)'
        ]
        
        for pattern in owner_patterns:
            match = re.search(pattern, text, re.IGNORECASE)
            if match:
                data['owner'] = match.group(1).strip()
                break
        
        return data
    
    def get_osm_data(self, stadium_name, city="", country=""):
        """
        Dapatkan data dari OpenStreetMap Nominatim
        """
        try:
            query = f"{stadium_name} {city} {country}"
            url = "https://nominatim.openstreetmap.org/search"
            params = {
                'q': query,
                'format': 'json',
                'limit': 1
            }
            
            response = self.session.get(url, params=params)
            if response.status_code == 200:
                data = response.json()
                if data:
                    return {
                        'lat': data[0]['lat'],
                        'lon': data[0]['lon'],
                        'display_name': data[0]['display_name']
                    }
        except Exception as e:
            print(f"Error OSM untuk {stadium_name}: {e}")
        
        return None
    
    def get_stadiumdb_data(self, stadium_name):
        """
        Scrape dari StadiumDB (sumber khusus stadium)
        """
        try:
            # StadiumDB memiliki API/search yang bisa digunakan
            search_url = f"https://stadiumdb.com/search?q={stadium_name.replace(' ', '+')}"
            response = self.session.get(search_url)
            
            if response.status_code == 200:
                soup = BeautifulSoup(response.content, 'html.parser')
                
                # Cari link stadium
                stadium_link = soup.find('a', href=re.compile(r'/stadiums/'))
                if stadium_link:
                    stadium_url = f"https://stadiumdb.com{stadium_link['href']}"
                    stadium_response = self.session.get(stadium_url)
                    
                    if stadium_response.status_code == 200:
                        stadium_soup = BeautifulSoup(stadium_response.content, 'html.parser')
                        return self.parse_stadiumdb_page(stadium_soup)
        
        except Exception as e:
            print(f"Error StadiumDB untuk {stadium_name}: {e}")
        
        return {}
    
    def parse_stadiumdb_page(self, soup):
        """
        Parse halaman detail stadium dari StadiumDB
        """
        data = {}
        
        try:
            # Capacity
            capacity_elem = soup.find('th', string=re.compile('Capacity', re.IGNORECASE))
            if capacity_elem:
                capacity_value = capacity_elem.find_next('td')
                if capacity_value:
                    data['capacity'] = re.search(r'(\d+,?\d+)', capacity_value.text).group(1).replace(',', '')
            
            # Opened
            opened_elem = soup.find('th', string=re.compile('Built|Opened', re.IGNORECASE))
            if opened_elem:
                opened_value = opened_elem.find_next('td')
                if opened_value:
                    data['opened'] = opened_value.text.strip()
            
            # Cost
            cost_elem = soup.find('th', string=re.compile('Cost', re.IGNORECASE))
            if cost_elem:
                cost_value = cost_elem.find_next('td')
                if cost_value:
                    data['construction_cost'] = cost_value.text.strip()
        
        except Exception as e:
            print(f"Error parsing StadiumDB: {e}")
        
        return data

def update_rdf_with_scraped_data(input_rdf_file, output_rdf_file):
    """
    Update file RDF dengan data yang di-scrape
    """
    scraper = StadiumScraper()
    
    # Baca file RDF asli
    tree = ET.parse(input_rdf_file)
    root = tree.getroot()
    
    # Dictionary namespace
    namespaces = {
        'rdf': 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
        'schema1': 'http://schema.org/',
        'ex': 'http://example.com/'
    }
    
    # Kumpulkan informasi stadium dari tim
    stadiums_to_scrape = []
    
    for team in root.findall('.//rdf:Description', namespaces):
        home_location = team.find('schema1:homeLocation', namespaces)
        team_name_elem = team.find('schema1:name', namespaces)
        country_elem = team.find('schema1:addressCountry', namespaces)
        
        if home_location is not None and home_location.text:
            stadiums_to_scrape.append({
                'stadium_name': home_location.text,
                'team_name': team_name_elem.text if team_name_elem is not None else '',
                'country': country_elem.text if country_elem is not None else ''
            })
    
    print(f"Found {len(stadiums_to_scrape)} stadiums to scrape")
    
    # Scrape data untuk setiap stadium
    scraped_stadiums = {}
    
    for stadium_info in stadiums_to_scrape:
        stadium_name = stadium_info['stadium_name']
        
        if stadium_name not in scraped_stadiums:
            print(f"\n--- Scraping data for: {stadium_name} ---")
            
            # Dapatkan data dari berbagai sumber
            wiki_data = scraper.get_wikipedia_stadium_data(stadium_name)
            osm_data = scraper.get_osm_data(stadium_name, country=stadium_info['country'])
            stadiumdb_data = scraper.get_stadiumdb_data(stadium_name)
            
            # Gabungkan data
            combined_data = {
                'name': stadium_name,
                'capacity': stadiumdb_data.get('capacity') or wiki_data.get('capacity') or '',
                'opened': stadiumdb_data.get('opened') or wiki_data.get('opened') or '',
                'construction_cost': stadiumdb_data.get('construction_cost') or '',
                'owner': wiki_data.get('owner') or '',
                'coordinates': wiki_data.get('coordinates') or '',
                'address': osm_data.get('display_name') if osm_data else '',
                'location': stadium_info['country']
            }
            
            scraped_stadiums[stadium_name] = combined_data
            
            # Tampilkan hasil
            for key, value in combined_data.items():
                if value:
                    print(f"  {key}: {value}")
            
            time.sleep(2)  # Hormati rate limit
    
    # Tambahkan stadium elements ke RDF
    for stadium_name, data in scraped_stadiums.items():
        stadium_elem = ET.Element('{http://www.w3.org/1999/02/22-rdf-syntax-ns#}Description')
        stadium_id = stadium_name.lower().replace(' ', '-').replace('&', 'and')
        stadium_elem.set('{http://www.w3.org/1999/02/22-rdf-syntax-ns#}about', 
                        f"http://example.com/stadium/{stadium_id}")
        
        # Type
        type_elem = ET.SubElement(stadium_elem, '{http://www.w3.org/1999/02/22-rdf-syntax-ns#}type')
        type_elem.set('{http://www.w3.org/1999/02/22-rdf-syntax-ns#}resource', 
                     'http://schema.org/StadiumOrArena')
        
        # Name
        if data['name']:
            name_elem = ET.SubElement(stadium_elem, '{http://schema.org/}name')
            name_elem.text = data['name']
        
        # Address
        if data['address']:
            address_elem = ET.SubElement(stadium_elem, '{http://schema.org/}address')
            address_elem.text = data['address']
        
        # Capacity
        if data['capacity']:
            capacity_elem = ET.SubElement(stadium_elem, '{http://schema.org/}capacity')
            capacity_elem.text = data['capacity']
        
        # Opened
        if data['opened']:
            opened_elem = ET.SubElement(stadium_elem, '{http://schema.org/}openingDate')
            opened_elem.text = data['opened']
        
        # Coordinates
        if data['coordinates']:
            geo_elem = ET.SubElement(stadium_elem, '{http://schema.org/}geo')
            geo_elem.text = data['coordinates']
        
        # Owner
        if data['owner']:
            owner_elem = ET.SubElement(stadium_elem, '{http://example.com/stadium/}stadiumOwner')
            owner_elem.text = data['owner']
        
        # Construction Cost
        if data['construction_cost']:
            cost_elem = ET.SubElement(stadium_elem, '{http://example.com/stadium/}constructionCost')
            cost_elem.text = data['construction_cost']
        
        # Tambahkan ke root
        root.append(stadium_elem)
    
    # Update referensi stadium di tim
    for team in root.findall('.//rdf:Description', namespaces):
        home_location = team.find('schema1:homeLocation', namespaces)
        if home_location is not None and home_location.text:
            stadium_name = home_location.text
            stadium_id = stadium_name.lower().replace(' ', '-').replace('&', 'and')
            
            # Ubah dari text menjadi resource reference
            home_location.text = ''
            home_location.set('{http://www.w3.org/1999/02/22-rdf-syntax-ns#}resource', 
                            f"http://example.com/stadium/{stadium_id}")
    
    # Simpan file RDF yang diperbarui
    tree.write(output_rdf_file, encoding='utf-8', xml_declaration=True)
    print(f"\nFile RDF berhasil diperbarui: {output_rdf_file}")

# Jalankan scraping
if __name__ == "__main__":
    input_file = "footballgas.rdf"
    output_file = "footballgas_with_stadiums.rdf"
    
    update_rdf_with_scraped_data(input_file, output_file)