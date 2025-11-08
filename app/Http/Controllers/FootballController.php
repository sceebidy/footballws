<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class FootballController extends Controller
{
    public function index(Request $request)
    {
        $sparqlEndpoint = 'http://localhost:3030/football/query'; // Ganti kalau Fuseki beda port/dataset

        $search = strtolower($request->query('q', ''));
        $sort = $request->query('sort', 'name');

        // Query SPARQL ambil data klub tanpa gambar
        $query = "
            PREFIX foot: <http://example.org/football#>
            PREFIX foaf: <http://xmlns.com/foaf/0.1/>
            SELECT ?team ?country ?stadium ?manager ?year ?desc
            WHERE {
                ?club a foot:Club ;
                      foaf:name ?team ;
                      foot:country ?country ;
                      foot:stadium ?stadium ;
                      foot:manager ?manager ;
                      foot:formedYear ?year ;
                      foot:description ?desc .
                " . ($search ? "FILTER(CONTAINS(LCASE(?team), '{$search}'))" : "") . "
            }
            LIMIT 100
        ";

        try {
            $client = new Client(['timeout' => 15]);
            $response = $client->post($sparqlEndpoint, [
                'form_params' => ['query' => $query],
                'headers' => ['Accept' => 'application/sparql-results+json']
            ]);

            $data = json_decode($response->getBody(), true);
            $results = $data['results']['bindings'] ?? [];

            // Sort hasilnya di sisi Laravel
            usort($results, function ($a, $b) use ($sort) {
                switch ($sort) {
                    case 'year':
                        return ($a['year']['value'] ?? 0) <=> ($b['year']['value'] ?? 0);
                    default:
                        return strcmp($a['team']['value'], $b['team']['value']);
                }
            });

        } catch (\Exception $e) {
            $results = [];
            $error = $e->getMessage();
            return view('football', compact('results', 'search', 'sort', 'error'));
        }

        return view('football', compact('results', 'search', 'sort'));
    }
}
