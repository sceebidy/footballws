<?php

namespace App\Http\Controllers;

use EasyRdf\Sparql\Client;
use Illuminate\Http\Request;

class SoccerController extends Controller
{
    public function index()
    {
        try {
            // URL Fuseki dataset kamu
            $fusekiUrl = 'http://localhost:3030/football/sparql';
            $sparql = new Client($fusekiUrl);

            // Query SPARQL sesuai RDF di Fuseki
            $query = "
                PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                PREFIX foot: <http://example.org/football#>
                SELECT ?club ?team ?country ?stadium ?manager ?year
                WHERE {
                    ?club a foot:Club ;
                          foaf:name ?team ;
                          foot:country ?country ;
                          foot:stadium ?stadium ;
                          foot:manager ?manager ;
                          foot:formedYear ?year .
                }
                LIMIT 50
            ";

            $results = $sparql->query($query);

            $clubs = [];
            foreach ($results as $row) {
                $clubs[] = [
                    'club' => basename((string) $row->club),
                    'team' => (string) $row->team,
                    'stadium' => (string) $row->stadium,
                    'country' => (string) $row->country,
                    'manager' => (string) $row->manager,
                    'year' => (string) $row->year,
                ];
            }

            return view('soccer', compact('clubs'));
        } catch (\Exception $e) {
            $error = "Gagal menghubungkan ke Fuseki di $fusekiUrl";
            return view('soccer', compact('error'));
        }
    }
}
