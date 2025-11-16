<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class FootballController extends Controller
{
    private $endpoint = "http://localhost:3030/bola/query";

    public function index(Request $request)
    {
        $search = strtolower(trim($request->q ?? ''));
        $selectedLeague = $request->league ?? '';

        $client = new Client(['timeout' => 20]);

        // ==============================
        // Manual League Names Mapping
        // ==============================
        $leagueNames = [
            "39" => "Premier League",
            "140" => "La Liga",
            "135" => "Serie A",
            "78" => "Bundesliga",
            "61" => "Ligue 1",
            "253" => "MLS",
            "88" => "Eredivisie",
            "182" => "Liga Portugal",
            "144" => "Belgian Pro League",
            "1001" => "Liga 1 Indonesia",
            "2001" => "Liga 2 Indonesia",
        ];

        // ==============================
        // Ambil daftar liga dari RDF
        // ==============================
        $leagueQuery = "
            PREFIX schema: <http://schema.org/>
            PREFIX ex: <http://example.com/resource/>

            SELECT DISTINCT (STRAFTER(STR(?league), '/league/') AS ?lg)
            WHERE {
                ?club a schema:SportsTeam ;
                      ex:competition ?league .
            }
        ";

        $respLeague = $client->post($this->endpoint, [
            'form_params' => ['query' => $leagueQuery],
            'headers' => ['Accept' => 'application/sparql-results+json']
        ]);

        $jsonLeague = json_decode($respLeague->getBody(), true);
        $leagues = array_map(fn($r) => $r['lg']['value'], $jsonLeague['results']['bindings']);

        // ==============================
        // Filters
        // ==============================
        $filter = '';
        if ($search) {
            $safe = preg_quote($search, '/');
            $filter .= "FILTER(REGEX(LCASE(?name), '.*$safe.*'))";
        }

        $filterLeague = '';
        if ($selectedLeague) {
            $filterLeague = "
                BIND(IRI(CONCAT('http://example.com/league/', '$selectedLeague')) AS ?targetLeague)
                FILTER(?competition = ?targetLeague)
            ";
        }

        // ==============================
        // Query Klub
        // ==============================
        $query = "
            PREFIX schema: <http://schema.org/>
            PREFIX ex: <http://example.com/resource/>

            SELECT DISTINCT ?club ?name ?founded ?location ?country ?stadium ?competition ?coach ?logo
            WHERE {
                ?club a schema:SportsTeam ;
                      schema:name ?name ;
                      schema:addressCountry ?country ;
                      schema:foundingDate ?founded ;
                      schema:homeLocation ?location ;
                      schema:homeStadium ?stadium ;
                      schema:coach ?coach ;
                      schema:logo ?logo ;
                      ex:competition ?competition .
                $filter
                $filterLeague
            }
            LIMIT 500
        ";

        try {
            $resp = $client->post($this->endpoint, [
                'form_params' => ['query' => $query],
                'headers' => ['Accept' => 'application/sparql-results+json']
            ]);

            $json = json_decode($resp->getBody(), true);
            $results = $json['results']['bindings'] ?? [];

        } catch (\Exception $e) {
            return view('football', [
                'results' => [],
                'search' => $search,
                'leagues' => $leagues,
                'leagueNames' => $leagueNames,
                'error' => $e->getMessage()
            ]);
        }

        // ==============================
        // RETURN VIEW (BENAR)
        // ==============================
        return view('football', [
            'results' => $results,
            'search' => $search,
            'leagues' => $leagues,
            'leagueNames' => $leagueNames
        ]);
    }

    // ==============================
    // DETAIL CLUB
    // ==============================
    public function show($id)
    {
        $client = new Client(['timeout' => 20]);

        $clubQuery = "
            PREFIX schema: <http://schema.org/>
            PREFIX ex: <http://example.com/resource/>

            SELECT ?name ?founded ?location ?country ?stadium ?competition ?coach ?logo
            WHERE {
                BIND(IRI(CONCAT('http://example.com/team/', '$id')) AS ?club)

                ?club a schema:SportsTeam ;
                      schema:name ?name ;
                      schema:addressCountry ?country ;
                      schema:foundingDate ?founded ;
                      schema:homeLocation ?location ;
                      schema:homeStadium ?stadium ;
                      schema:coach ?coach ;
                      schema:logo ?logo ;
                      ex:competition ?competition .
            }
            LIMIT 1
        ";

        $playerQuery = "
            PREFIX ex: <http://example.com/resource/>

            SELECT ?player
            WHERE {
                BIND(IRI(CONCAT('http://example.com/team/', '$id')) AS ?club)
                ?club ex:player ?player .
            }
        ";

        try {
            $resClub = $client->post($this->endpoint, [
                'form_params' => ['query' => $clubQuery],
                'headers' => ['Accept' => 'application/sparql-results+json']
            ]);

            $clubJson = json_decode($resClub->getBody(), true);
            $club = $clubJson['results']['bindings'][0] ?? null;

            $resPlayer = $client->post($this->endpoint, [
                'form_params' => ['query' => $playerQuery],
                'headers' => ['Accept' => 'application/sparql-results+json']
            ]);

            $jsonPlayer = json_decode($resPlayer->getBody(), true);
            $players = array_map(fn($p) => $p['player']['value'], $jsonPlayer['results']['bindings']);

        } catch (\Exception $e) {
            return view('clubdetail', [
                'club' => null,
                'players' => [],
                'id' => $id,
                'error' => $e->getMessage()
            ]);
        }

        return view('clubdetail', [
            'club' => $club,
            'players' => $players,
            'id' => $id
        ]);
    }
}
