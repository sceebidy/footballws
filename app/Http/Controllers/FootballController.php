<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class FootballController extends Controller
{
    public function index(Request $request)
    {
        $sparqlEndpoint = 'http://localhost:3030/football/query';

        $search = strtolower(trim($request->query('q', '')));
        $criteria = $request->query('criteria', 'all');

        // NORMALISASI TYPO
        $replacements = [
            '0' => 'o',
            '1' => 'i',
            '3' => 'e',
            '4' => 'a',
            '5' => 's',
            '7' => 't',
            '@' => 'a',
            '$' => 's',
            '!' => 'i'
        ];
        $search = strtr($search, $replacements);

        // FILTER MULTI-KRITERIA
        $filter = '';
        if ($search) {
            $safe = preg_quote($search, '/');
            
            switch ($criteria) {
                case 'team':
                    $filter = "FILTER (REGEX(LCASE(?team), '.*{$safe}.*'))";
                    break;
                case 'country':
                    $filter = "FILTER (REGEX(LCASE(?country), '.*{$safe}.*'))";
                    break;
                case 'coach':
                    $filter = "FILTER (REGEX(LCASE(?coach), '.*{$safe}.*'))";
                    break;
                case 'stadium':
                    $filter = "FILTER (REGEX(LCASE(?stadium), '.*{$safe}.*'))";
                    break;
                case 'location':
                    $filter = "FILTER (REGEX(LCASE(?location), '.*{$safe}.*'))";
                    break;
                case 'owner':
                    $filter = "FILTER (REGEX(LCASE(?owner), '.*{$safe}.*'))";
                    break;
                case 'all':
                default:
                    $filter = "
                        FILTER (
                            REGEX(LCASE(?team), '.*{$safe}.*') ||
                            REGEX(LCASE(?alt), '.*{$safe}.*') ||
                            REGEX(LCASE(?country), '.*{$safe}.*') ||
                            REGEX(LCASE(?location), '.*{$safe}.*') ||
                            REGEX(LCASE(?stadium), '.*{$safe}.*') ||
                            REGEX(LCASE(?coach), '.*{$safe}.*') ||
                            REGEX(LCASE(?owner), '.*{$safe}.*')
                        )
                    ";
                    break;
            }
        }

        // SPARQL UNTUK SCHEMA.ORG
        $query = "
            PREFIX schema: <http://schema.org/>

            SELECT ?club ?team ?alt ?country ?location ?stadium ?coach ?owner ?logo
            WHERE {
                ?club a schema:SportsTeam ;
                      schema:name ?team ;
                      schema:alternateName ?alt ;
                      schema:addressCountry ?country ;
                      schema:homeLocation ?location ;
                      schema:homeStadium ?stadium ;
                      schema:coach ?coach ;
                      schema:owner ?owner ;
                      schema:logo ?logo .
                $filter
            }
            ORDER BY ?team
            LIMIT 300
        ";

        try {
            $client = new Client(['timeout' => 15]);
            $response = $client->post($sparqlEndpoint, [
                'form_params' => ['query' => $query],
                'headers' => ['Accept' => 'application/sparql-results+json']
            ]);

            $data = json_decode($response->getBody(), true);
            $results = $data['results']['bindings'] ?? [];

        } catch (\Exception $e) {
            return view('football', [
                'results' => [],
                'search' => $search,
                'criteria' => $criteria,
                'error' => $e->getMessage(),
            ]);
        }

        return view('football', compact('results', 'search', 'criteria'));
    }

    // METHOD BARU UNTUK AUTOSUGGEST/SEARCH-AS-YOU-TYPE
    public function autosuggest(Request $request)
    {
        $sparqlEndpoint = 'http://localhost:3030/football/query';

        $search = strtolower(trim($request->query('q', '')));

        // NORMALISASI TYPO
        $replacements = [
            '0' => 'o',
            '1' => 'i',
            '3' => 'e',
            '4' => 'a',
            '5' => 's',
            '7' => 't',
            '@' => 'a',
            '$' => 's',
            '!' => 'i'
        ];
        $search = strtr($search, $replacements);

        $results = [];

        if (strlen($search) >= 1) { // Mulai suggest dari 1 karakter
            $safe = preg_quote($search, '/');

            $query = "
                PREFIX schema: <http://schema.org/>

                SELECT DISTINCT ?team ?country ?coach ?stadium ?location
                WHERE {
                    ?club a schema:SportsTeam ;
                          schema:name ?team ;
                          schema:addressCountry ?country ;
                          schema:homeLocation ?location ;
                          schema:homeStadium ?stadium ;
                          schema:coach ?coach .
                    FILTER (
                        REGEX(LCASE(?team), '.*{$safe}.*') ||
                        REGEX(LCASE(?country), '.*{$safe}.*') ||
                        REGEX(LCASE(?location), '.*{$safe}.*') ||
                        REGEX(LCASE(?stadium), '.*{$safe}.*') ||
                        REGEX(LCASE(?coach), '.*{$safe}.*')
                    )
                }
                LIMIT 10
            ";

            try {
                $client = new Client(['timeout' => 10]);
                $response = $client->post($sparqlEndpoint, [
                    'form_params' => ['query' => $query],
                    'headers' => ['Accept' => 'application/sparql-results+json']
                ]);

                $data = json_decode($response->getBody(), true);
                $results = $data['results']['bindings'] ?? [];

            } catch (\Exception $e) {
                // Return empty array jika error
                return response()->json([]);
            }
        }

        return response()->json($results);
    }

    // METHOD UNTUK REAL-TIME SEARCH (halaman langsung menampilkan hasil saat ketik)
    public function realtimeSearch(Request $request)
    {
        $sparqlEndpoint = 'http://localhost:3030/football/query';

        $search = strtolower(trim($request->query('q', '')));

        // NORMALISASI TYPO
        $replacements = [
            '0' => 'o',
            '1' => 'i',
            '3' => 'e',
            '4' => 'a',
            '5' => 's',
            '7' => 't',
            '@' => 'a',
            '$' => 's',
            '!' => 'i'
        ];
        $search = strtr($search, $replacements);

        $filter = '';
        if ($search) {
            $safe = preg_quote($search, '/');
            $filter = "
                FILTER (
                    REGEX(LCASE(?team), '.*{$safe}.*') ||
                    REGEX(LCASE(?alt), '.*{$safe}.*') ||
                    REGEX(LCASE(?country), '.*{$safe}.*') ||
                    REGEX(LCASE(?location), '.*{$safe}.*') ||
                    REGEX(LCASE(?stadium), '.*{$safe}.*') ||
                    REGEX(LCASE(?coach), '.*{$safe}.*') ||
                    REGEX(LCASE(?owner), '.*{$safe}.*')
                )
            ";
        }

        $query = "
            PREFIX schema: <http://schema.org/>

            SELECT ?club ?team ?alt ?country ?location ?stadium ?coach ?owner ?logo
            WHERE {
                ?club a schema:SportsTeam ;
                      schema:name ?team ;
                      schema:alternateName ?alt ;
                      schema:addressCountry ?country ;
                      schema:homeLocation ?location ;
                      schema:homeStadium ?stadium ;
                      schema:coach ?coach ;
                      schema:owner ?owner ;
                      schema:logo ?logo .
                $filter
            }
            ORDER BY ?team
            LIMIT 50
        ";

        try {
            $client = new Client(['timeout' => 10]);
            $response = $client->post($sparqlEndpoint, [
                'form_params' => ['query' => $query],
                'headers' => ['Accept' => 'application/sparql-results+json']
            ]);

            $data = json_decode($response->getBody(), true);
            $results = $data['results']['bindings'] ?? [];

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['results' => $results, 'search' => $search]);
    }

    // METHOD UNTUK PENCARIAN SPESIFIK BERDASARKAN FIELD
    public function searchByField(Request $request, $field, $value)
    {
        $sparqlEndpoint = 'http://localhost:3030/football/query';

        $safeValue = preg_quote(strtolower($value), '/');

        $query = "
            PREFIX schema: <http://schema.org/>

            SELECT ?club ?team ?alt ?country ?location ?stadium ?coach ?owner ?logo
            WHERE {
                ?club a schema:SportsTeam ;
                      schema:name ?team ;
                      schema:alternateName ?alt ;
                      schema:addressCountry ?country ;
                      schema:homeLocation ?location ;
                      schema:homeStadium ?stadium ;
                      schema:coach ?coach ;
                      schema:owner ?owner ;
                      schema:logo ?logo .
                FILTER (REGEX(LCASE(?$field), '.*{$safeValue}.*'))
            }
            ORDER BY ?team
            LIMIT 300
        ";

        try {
            $client = new Client(['timeout' => 15]);
            $response = $client->post($sparqlEndpoint, [
                'form_params' => ['query' => $query],
                'headers' => ['Accept' => 'application/sparql-results+json']
            ]);

            $data = json_decode($response->getBody(), true);
            $results = $data['results']['bindings'] ?? [];

        } catch (\Exception $e) {
            return view('football', [
                'results' => [],
                'search' => $value,
                'criteria' => $field,
                'error' => $e->getMessage(),
            ]);
        }

        return view('football', [
            'results' => $results,
            'search' => $value,
            'criteria' => $field
        ]);
    }

    // METHOD CONVENIENCE UNTUK ROUTE SPESIFIK
    public function byCountry($country)
    {
        return $this->searchByField(new Request(), 'country', $country);
    }

    public function byCoach($coach)
    {
        return $this->searchByField(new Request(), 'coach', $coach);
    }

    public function byStadium($stadium)
    {
        return $this->searchByField(new Request(), 'stadium', $stadium);
    }

    public function byLocation($location)
    {
        return $this->searchByField(new Request(), 'location', $location);
    }

    public function byOwner($owner)
    {
        return $this->searchByField(new Request(), 'owner', $owner);
    }

    public function show($id)
    {
        $sparqlEndpoint = 'http://localhost:3030/football/query';

        // DETAIL QUERY SESUAI RDF SCHEMA.ORG
        $query = "
            PREFIX schema: <http://schema.org/>

            SELECT ?team ?alt ?country ?location ?stadium ?coach ?owner ?logo
            WHERE {
                ?club a schema:SportsTeam ;
                      schema:name ?team ;
                      schema:alternateName ?alt ;
                      schema:addressCountry ?country ;
                      schema:homeLocation ?location ;
                      schema:homeStadium ?stadium ;
                      schema:coach ?coach ;
                      schema:owner ?owner ;
                      schema:logo ?logo .
                FILTER(STR(?club) = 'http://example.com/resource/{$id}')
            }
            LIMIT 1
        ";

        try {
            $client = new Client(['timeout' => 15]);
            $response = $client->post($sparqlEndpoint, [
                'form_params' => ['query' => $query],
                'headers' => ['Accept' => 'application/sparql-results+json']
            ]);

            $data = json_decode($response->getBody(), true);
            $club = $data['results']['bindings'][0] ?? null;

        } catch (\Exception $e) {
            return view('clubdetail', [
                'club' => null,
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
        }

        return view('clubdetail', compact('club', 'id'));
    }
}