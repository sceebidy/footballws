<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class FootballController extends Controller
{
    private $endpoint = 'http://localhost:3030/TugasWs/query';
    private $timeout = 15;

    private function runQuery($query)
    {
        try {
            $client = new Client(['timeout' => $this->timeout]);

            $response = $client->post($this->endpoint, [
                'form_params' => ['query' => $query],
                'headers' => ['Accept' => 'application/sparql-results+json']
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['results']['bindings'] ?? [];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function index(Request $request)
    {
        $search = strtolower(trim($request->query('q', '')));
        $criteria = $request->query('criteria', 'all');

        // typo normalizer
        $search = strtr($search, [
            '0'=>'o','1'=>'i','3'=>'e','4'=>'a','5'=>'s','7'=>'t','@'=>'a','$'=>'s','!'=>'i'
        ]);

        $filter = '';
        if ($search) {
            $safe = preg_quote($search, '/');

            if ($criteria !== 'all') {
                $filter = "FILTER (REGEX(LCASE(?$criteria), '.*$safe.*'))";
            } else {
                $filter = "
                    FILTER (
                        REGEX(LCASE(?team), '.*$safe.*') ||
                        REGEX(LCASE(?alt), '.*$safe.*') ||
                        REGEX(LCASE(?country), '.*$safe.*') ||
                        REGEX(LCASE(?location), '.*$safe.*') ||
                        REGEX(LCASE(?stadium), '.*$safe.*') ||
                        REGEX(LCASE(?coach), '.*$safe.*') ||
                        REGEX(LCASE(?owner), '.*$safe.*')
                    )
                ";
            }
        }

        // SAFE SPARQL
        $query = "
            PREFIX schema: <http://schema.org/>

            SELECT ?club ?team ?alt ?country ?location ?stadium ?coach ?owner ?logo
            WHERE {
                ?club a schema:SportsTeam .
                OPTIONAL { ?club schema:name ?team . }
                OPTIONAL { ?club schema:alternateName ?alt . }
                OPTIONAL { ?club schema:addressCountry ?country . }
                OPTIONAL { ?club schema:homeLocation ?location . }
                OPTIONAL { ?club schema:homeStadium ?stadium . }
                OPTIONAL { ?club schema:coach ?coach . }
                OPTIONAL { ?club schema:owner ?owner . }
                OPTIONAL { ?club schema:logo ?logo . }
                $filter
            }
            ORDER BY ?team
            LIMIT 300
        ";

        $results = $this->runQuery($query);

        return view('football', [
            'results' => $results,
            'search'  => $search,
            'criteria'=> $criteria,
            'error'   => isset($results['error']) ? $results['error'] : null
        ]);
    }

public function show($id)
{
    $sparqlEndpoint = 'http://localhost:3030/TugasWs/query';

    $query = "
        PREFIX schema: <http://schema.org/>

        SELECT ?team ?alt ?country ?location ?stadium ?coach ?owner ?logo
        WHERE {
            ?club a schema:SportsTeam ;
                  schema:name ?team .

            OPTIONAL { ?club schema:alternateName ?alt . }
            OPTIONAL { ?club schema:addressCountry ?country . }
            OPTIONAL { ?club schema:homeLocation ?location . }
            OPTIONAL { ?club schema:homeStadium ?stadium . }
            OPTIONAL { ?club schema:coach ?coach . }
            OPTIONAL { ?club schema:owner ?owner . }
            OPTIONAL { ?club schema:logo ?logo . }

            FILTER(STR(?club) = \"http://example.com/resource/$id\")
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
