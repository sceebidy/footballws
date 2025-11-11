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
        $sort = $request->query('sort', 'name');

        // 🧠 Normalisasi karakter mirip agar typo seperti madr1d -> madrid
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

        // 🧩 Filter REGEX mirip LIKE '%keyword%'
        $filter = '';
        if ($search) {
            $safeSearch = preg_quote($search, '/');
            $filter = "FILTER(REGEX(LCASE(?team), '.*{$safeSearch}.*'))";
        }

        // 🧠 Query ke Fuseki
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
                {$filter}
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

            // ✨ Fuzzy filtering di sisi PHP
            if ($search) {
                $results = array_filter($results, function ($item) use ($search) {
                    $team = strtolower($item['team']['value']);
                    $distance = levenshtein($search, $team);
                    $similarity = 0;
                    similar_text($search, $team, $similarity);

                    return str_contains($team, $search)
                        || $distance <= 3
                        || $similarity >= 70;
                });

                // Urutkan berdasarkan kemiripan
                usort($results, function ($a, $b) use ($search) {
                    $teamA = strtolower($a['team']['value']);
                    $teamB = strtolower($b['team']['value']);
                    similar_text($search, $teamA, $simA);
                    similar_text($search, $teamB, $simB);
                    return $simB <=> $simA;
                });
            } else {
                // Urutkan default
                usort($results, function ($a, $b) use ($sort) {
                    switch ($sort) {
                        case 'year':
                            return ($a['year']['value'] ?? 0) <=> ($b['year']['value'] ?? 0);
                        default:
                            return strcmp($a['team']['value'], $b['team']['value']);
                    }
                });
            }

        } catch (\Exception $e) {
            $results = [];
            $error = $e->getMessage();
            return view('football', compact('results', 'search', 'sort', 'error'));
        }

        return view('football', compact('results', 'search', 'sort'));
    }
}
