<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class FootballController extends Controller
{
    public function league($id)
{
    $sparqlEndpoint = 'http://localhost:3030/football/query';

    $query = "
        PREFIX schema: <http://schema.org/>
        PREFIX ex: <http://example.com/resource/>

        SELECT ?club ?name ?nickname ?founded ?location ?country ?stadium ?owner ?coach ?logo
        WHERE {
            BIND(IRI(CONCAT('http://example.com/resource/', '$id')) AS ?league)

            ?club a schema:SportsTeam ;
                  schema:name ?name ;
                  schema:alternateName ?nickname ;
                  schema:foundingDate ?founded ;
                  schema:homeLocation ?location ;
                  schema:addressCountry ?country ;
                  schema:homeStadium ?stadium ;
                  schema:owner ?owner ;
                  schema:coach ?coach ;
                  schema:logo ?logo ;
                  ex:competition ?league .
        }
        LIMIT 300
    ";

    try {
        $client = new Client(['timeout' => 20]);
        $res = $client->post($sparqlEndpoint, [
            'form_params' => ['query' => $query],
            'headers' => ['Accept' => 'application/sparql-results+json']
        ]);

        $json = json_decode($res->getBody(), true);
        $results = $json['results']['bindings'] ?? [];
    } catch (\Exception $e) {
        return view('league', [
            'results' => [],
            'leagueName' => $id,
            'error' => $e->getMessage()
        ]);
    }

    return view('league', [
        'results' => $results,
        'leagueName' => $id
    ]);
}

    public function index(Request $request)
{
    $sparqlEndpoint = 'http://localhost:3030/football/query';
    $search = strtolower(trim($request->q ?? ''));
    $mode = $request->mode ?? 'club';   // default tampil klub
    $selectedLeague = $request->league ?? '';

    $client = new Client(['timeout' => 20]);

    // =======================
    // 1️⃣ Ambil daftar liga
    // =======================
    $leagueQuery = "
        PREFIX schema: <http://schema.org/>
        PREFIX ex: <http://example.com/resource/>

        SELECT DISTINCT (STRAFTER(STR(?league), '/resource/') AS ?lg)
        WHERE {
            ?club a schema:SportsTeam ;
                  ex:competition ?league .
        }
    ";

    $resLeague = $client->post($sparqlEndpoint, [
        'form_params' => ['query' => $leagueQuery],
        'headers' => ['Accept' => 'application/sparql-results+json']
    ]);

    $jsonLeague = json_decode($resLeague->getBody(), true);
    $leagues = array_map(fn($r) => $r['lg']['value'], $jsonLeague['results']['bindings']);


    // =======================
    // 2️⃣ Filter search
    // =======================
    $filter = '';
    if ($search) {
        $safe = preg_quote($search, '/');
        $filter .= "FILTER(REGEX(LCASE(?name), '.*$safe.*'))";
    }

    // =======================
    // 3️⃣ Filter liga jika dipilih
    // =======================
    $filterLeague = "";
    if ($selectedLeague) {
        $filterLeague = "
            BIND(IRI(CONCAT('http://example.com/resource/', '$selectedLeague')) AS ?targetLeague)
            FILTER(?competition = ?targetLeague)
        ";
    }

    // =======================
    // 4️⃣ Query klub (normal)
    // =======================
    $query = "
        PREFIX schema: <http://schema.org/>
        PREFIX ex: <http://example.com/resource/>

        SELECT ?club ?name ?nickname ?founded ?location ?country ?stadium ?competition ?owner ?coach ?logo
        WHERE {
            ?club a schema:SportsTeam ;
                  schema:name ?name ;
                  schema:alternateName ?nickname ;
                  schema:foundingDate ?founded ;
                  schema:homeLocation ?location ;
                  schema:addressCountry ?country ;
                  schema:homeStadium ?stadium ;
                  ex:competition ?competition ;
                  schema:owner ?owner ;
                  schema:coach ?coach ;
                  schema:logo ?logo .

            $filter
            $filterLeague
        }
        LIMIT 300
    ";

    try {
        $res = $client->post($sparqlEndpoint, [
            'form_params' => ['query' => $query],
            'headers' => ['Accept' => 'application/sparql-results+json']
        ]);

        $json = json_decode($res->getBody(), true);
        $results = $json['results']['bindings'] ?? [];

    } catch (\Exception $e) {
        return view('football', [
            'results' => [],
            'search' => $search,
            'mode' => $mode,
            'leagues' => $leagues,
            'error' => $e->getMessage()
        ]);
    }

    return view('football', [
        'results' => $results,
        'search' => $search,
        'mode' => $mode,
        'leagues' => $leagues
    ]);
}


    // ================================
    // 🔥 DETAIL CLUB
    // ================================
    public function show($id)
    {
        $sparqlEndpoint = 'http://localhost:3030/football/query';

        $query = "
            PREFIX schema: <http://schema.org/>
            PREFIX ex: <http://example.com/resource/>

            SELECT ?name ?nickname ?founded ?location ?country ?stadium ?competition ?owner ?coach ?logo
            WHERE {
                BIND(IRI(CONCAT('http://example.com/resource/', '$id')) AS ?club)

                ?club a schema:SportsTeam ;
                      schema:name ?name ;
                      schema:alternateName ?nickname ;
                      schema:foundingDate ?founded ;
                      schema:homeLocation ?location ;
                      schema:addressCountry ?country ;
                      schema:homeStadium ?stadium ;
                      ex:competition ?competition ;
                      schema:owner ?owner ;
                      schema:coach ?coach ;
                      schema:logo ?logo .
            }
            LIMIT 1
        ";

        try {
            $client = new Client(['timeout' => 20]);
            $res = $client->post($sparqlEndpoint, [
                'form_params' => ['query' => $query],
                'headers' => ['Accept' => 'application/sparql-results+json']
            ]);

            $json = json_decode($res->getBody(), true);
            $club = $json['results']['bindings'][0] ?? null;

        } catch (\Exception $e) {
            return view('clubdetail', [
                'club' => null,
                'error' => $e->getMessage(),
                'id' => $id
            ]);
        }

        return view('clubdetail', compact('club', 'id'));
    }
}
