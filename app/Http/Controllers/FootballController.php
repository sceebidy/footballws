<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class FootballController extends Controller
{
    public function index(Request $request)
    {
        $sparqlEndpoint = 'http://localhost:3030/gas/query';

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
            return view('football', ['results' => [], 'error' => $e->getMessage()]);
        }

        return view('football', compact('results'));
    }



    // ===========================
    // DETAIL CLUB + DETAIL PLAYER
    // ===========================
    public function show($id)
    {
        $sparqlEndpoint = 'http://localhost:3030/gas/query';

        // DETAIL CLUB
        $clubQuery = "
            PREFIX schema1: <http://schema.org/>
            PREFIX ex: <http://example.com/>

            SELECT ?team ?country ?coach ?logo ?location ?founding
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

                FILTER(STR(?club) = 'http://example.com/team/{$id}')
            }
            GROUP BY ?team ?country ?coach ?logo ?location ?founding
        ";

        // AMBIL SEMUA PLAYER DETAIL
        $allPlayersQuery = "
            PREFIX schema1: <http://schema.org/>
            PREFIX ex: <http://example.com/>

            SELECT ?player ?name ?position ?nationality ?birth
            WHERE {
                ?player a schema1:Person ;
                        schema1:name ?name .

                OPTIONAL { ?player ex:position ?position . }
                OPTIONAL { ?player schema1:nationality ?nationality . }
                OPTIONAL { ?player schema1:birthDate ?birth . }
            }
        ";

        try {
            $client = new Client(['timeout' => 15]);

            // DETAIL CLUB
            $clubRes = $client->post($sparqlEndpoint, [
                'form_params' => ['query' => $clubQuery],
                'headers' => ['Accept' => 'application/sparql-results+json']
            ]);
            $club = json_decode($clubRes->getBody(), true)['results']['bindings'][0] ?? null;

            if (!$club) {
                return view('clubdetail', [
                    'club' => null,
                    'players' => [],
                    'id' => $id
                ]);
            }

            // LIST PLAYER DARI CLUB (STRING)
            $clubPlayerNames = array_map('trim', explode(',', $club['playerList']['value']));

            // SEMUA PLAYER DETAIL
            $allPlayersRes = $client->post($sparqlEndpoint, [
                'form_params' => ['query' => $allPlayersQuery],
                'headers' => ['Accept' => 'application/sparql-results+json']
            ]);
            $allPlayers = json_decode($allPlayersRes->getBody(), true)['results']['bindings'] ?? [];

            // MATCHING BERDASARKAN NAMA
            $teamPlayers = [];
            foreach ($allPlayers as $p) {
                $name = $p['name']['value'];

                if (in_array($name, $clubPlayerNames)) {
                    $teamPlayers[] = $p;
                }
            }

        } catch (\Exception $e) {
            return view('clubdetail', [
                'club' => null,
                'players' => [],
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
        }

        return view('clubdetail', [
            'club' => $club,
            'players' => $teamPlayers,
            'id' => $id,
        ]);
    }
}
