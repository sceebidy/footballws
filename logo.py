import re

# Baca file
with open('footballori.rdf', 'r', encoding='utf-8') as file:
    content = file.read()

# Ganti semua example.com logos dengan URL nyata
content = re.sub(
    r'https://example\.com/logo/([a-zA-Z0-9]+)\.png',
    r'https://footylogos.com/logos/\1.png',
    content
)

# Simpan file yang sudah diperbaiki
with open('football_fixed.rdf', 'w', encoding='utf-8') as file:
    file.write(content)