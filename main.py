from scraper_asia import scrape_all_asia
from update_rdf import add_club_to_rdf

def main():
    rdf_file = "C:/TubesWS/footballws/football.rdf"

    print("🏁 MULAI SCRAPING LIGA-LIGA ASIA")
    clubs = scrape_all_asia()

    print(f"📌 Total klub ditemukan: {len(clubs)}")
    print("📥 Memasukkan ke RDF...")

    for club in clubs:
        add_club_to_rdf(rdf_file, club)

    print("✅ SELESAI — Semua klub berhasil dimasukkan ke RDF.")

if __name__ == "__main__":
    main()
