<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class FootballController extends Controller
{
    // ===========================
    // HALAMAN UTAMA
    // ===========================
    public function index(Request $request)
    {
        $sparqlEndpoint = 'http://localhost:3030/tugasbesar/query';

        // Menambahkan stadiumName tetapi tidak mengubah fungsi utama
        $query = "
            PREFIX schema1: <http://schema.org/>
            PREFIX ex: <http://example.com/>

            SELECT ?club ?teamName ?country ?coach ?logo ?stadiumName
                   (GROUP_CONCAT(DISTINCT ?playerName; separator=\", \") AS ?players)
            WHERE {
                ?club a schema1:SportsTeam ;
                      schema1:name ?teamName ;
                      schema1:addressCountry ?country ;
                      schema1:coach ?coach ;
                      schema1:logo ?logo ;
                      ex:player ?playerName .

                OPTIONAL {
                    ?club schema1:homeLocation ?stadiumIRI .
                    ?stadiumIRI schema1:name ?stadiumName .
                }
            }
            GROUP BY ?club ?teamName ?country ?coach ?logo ?stadiumName
            ORDER BY ?teamName
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
                'error' => $e->getMessage()
            ]);
        }

        return view('football', compact('results'));
    }

    // ===========================
    // DETAIL CLUB + PLAYER + STADIUM
    // ===========================
    public function show($id)
    {
        $sparqlEndpoint = 'http://localhost:3030/tugasbesar/query';

        // CLUB DETAIL
        $clubQuery = "
            PREFIX schema1: <http://schema.org/>
            PREFIX ex: <http://example.com/>

            SELECT ?team ?country ?coach ?logo ?location ?founding ?stadium
                   (GROUP_CONCAT(DISTINCT ?playerName; separator=\", \") AS ?playerList)
            WHERE {
                ?club a schema1:SportsTeam ;
                      schema1:name ?team ;
                      schema1:addressCountry ?country ;
                      schema1:coach ?coach ;
                      schema1:logo ?logo ;
                      schema1:homeLocation ?location ;
                      schema1:foundingDate ?founding ;
                      ex:player ?playerName .

                OPTIONAL { ?club schema1:homeLocation ?stadium . }

                FILTER(STR(?club) = 'http://example.com/team/{$id}')
            }
            GROUP BY ?team ?country ?coach ?logo ?location ?founding ?stadium
        ";

        // STADIUM DETAIL
        $stadiumQuery = "
            PREFIX schema1: <http://schema.org/>

            SELECT ?stadium ?name ?capacity ?address ?geo
            WHERE {
                ?club a schema1:SportsTeam ;
                      schema1:name ?team ;
                      schema1:homeLocation ?stadium .

                ?stadium a schema1:StadiumOrArena ;
                         schema1:name ?name .

                OPTIONAL { ?stadium schema1:capacity ?capacity . }
                OPTIONAL { ?stadium schema1:address ?address . }
                OPTIONAL { ?stadium schema1:geo ?geo . }

                FILTER(STR(?club) = 'http://example.com/team/{$id}')
            }
            LIMIT 1
        ";

        // ALL PLAYER DETAIL
        $allPlayersQuery = "
            PREFIX schema1: <http://schema.org/>
            PREFIX ex: <http://example.com/>

            SELECT ?player ?name ?position ?nationality ?birth ?image
            WHERE {
                ?player a schema1:Person ;
                        schema1:name ?name .

                OPTIONAL { ?player ex:position ?position . }
                OPTIONAL { ?player schema1:nationality ?nationality . }
                OPTIONAL { ?player schema1:birthDate ?birth . }
                OPTIONAL { ?player schema1:image ?image . }
            }
        ";

        try {
            $client = new Client(['timeout' => 15]);

            // CLUB DETAIL
            $clubRes = $client->post($sparqlEndpoint, [
                'form_params' => ['query' => $clubQuery],
                'headers' => ['Accept' => 'application/sparql-results+json']
            ]);
            $clubData = json_decode($clubRes->getBody(), true);
            $club = $clubData['results']['bindings'][0] ?? null;

            if (!$club) {
                return view('clubdetail', [
                    'club' => null,
                    'players' => [],
                    'stadium' => null,
                    'id' => $id
                ]);
            }

            // STADIUM
            $stadiumRes = $client->post($sparqlEndpoint, [
                'form_params' => ['query' => $stadiumQuery],
                'headers' => ['Accept' => 'application/sparql-results+json']
            ]);
            $stadiumData = json_decode($stadiumRes->getBody(), true);
            $stadium = $stadiumData['results']['bindings'][0] ?? null;

            // PLAYER STRING → ARRAY
            $clubPlayerNames = isset($club['playerList']['value'])
                ? array_map('trim', explode(',', $club['playerList']['value']))
                : [];

            // ALL PLAYERS
            $allPlayersRes = $client->post($sparqlEndpoint, [
                'form_params' => ['query' => $allPlayersQuery],
                'headers' => ['Accept' => 'application/sparql-results+json']
            ]);

            $allPlayersData = json_decode($allPlayersRes->getBody(), true);
            $allPlayers = $allPlayersData['results']['bindings'] ?? [];

            // MATCH PLAYERS
            $teamPlayers = [];
            foreach ($allPlayers as $p) {
                if (isset($p['name']['value']) &&
                    in_array($p['name']['value'], $clubPlayerNames)) {
                    $teamPlayers[] = $p;
                }
            }

        } catch (\Exception $e) {
            return view('clubdetail', [
                'club' => null,
                'players' => [],
                'stadium' => null,
                'id' => $id,
                'error' => $e->getMessage()
            ]);
        }

        return view('clubdetail', [
            'club' => $club,
            'players' => $teamPlayers,
            'stadium' => $stadium,
            'id' => $id
        ]);
    }

    // ===========================
    // SEARCH FUNCTION
    // ===========================
    public function search(Request $request)
    {
        $searchTerm = trim($request->input('search', ''));
        $sparqlEndpoint = 'http://localhost:3030/tugasbesar/query';
        $safeSearch = addslashes($searchTerm);

        // SUPPORT: team, country, coach, players, stadium name
        $query = "
            PREFIX schema1: <http://schema.org/>
            PREFIX ex: <http://example.com/>

            SELECT ?club ?teamName ?country ?coach ?logo
                   (GROUP_CONCAT(DISTINCT ?playerName; separator=\", \") AS ?players)
            WHERE {
                ?club a schema1:SportsTeam ;
                      schema1:name ?teamName ;
                      schema1:addressCountry ?country ;
                      schema1:coach ?coach ;
                      schema1:logo ?logo ;
                      ex:player ?playerName .

                OPTIONAL {
                    ?club schema1:homeLocation ?stadiumIRI .
                    ?stadiumIRI schema1:name ?stadiumName .
                }

                FILTER(
                    REGEX(LCASE(?teamName), LCASE(\"$safeSearch\")) ||
                    REGEX(LCASE(?country), LCASE(\"$safeSearch\")) ||
                    REGEX(LCASE(?coach), LCASE(\"$safeSearch\")) ||
                    REGEX(LCASE(?playerName), LCASE(\"$safeSearch\")) ||
                    REGEX(LCASE(?stadiumName), LCASE(\"$safeSearch\"))
                )
            }
            GROUP BY ?club ?teamName ?country ?coach ?logo
            ORDER BY ?teamName
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
                'error' => $e->getMessage()
            ]);
        }

        return view('football', compact('results', 'searchTerm'));
    }
}
