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

        // FILTER
        $filter = '';
        if ($search) {
            $safe = preg_quote($search, '/');
            $filter = "FILTER (REGEX(LCASE(?team), '.*{$safe}.*'))";
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
                'error' => $e->getMessage(),
            ]);
        }

        return view('football', compact('results', 'search'));
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
