<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use EasyRdf\Graph;
use EasyRdf\Namespace as EasyRdfNamespace;

class FootballController extends Controller
{
    public function index()
    {
        // Tambahkan namespace prefix
        EasyRdfNamespace::set('schema', 'http://schema.org/');
        EasyRdfNamespace::set('ex', 'http://example.com/resource/');

        // Load file RDF
        $graph = new Graph();
        $graph->parseFile(public_path('data/football.rdf'));

        // Ambil semua SportsTeam
        $teams = $graph->allOfType('schema:SportsTeam');

        // Convert ke array agar mudah dipakai di Blade
        $clubs = [];

        foreach ($teams as $team) {
            $clubs[] = [
                'name'        => $team->get('schema:name'),
                'altName'     => $team->get('schema:alternateName'),
                'founded'     => $team->get('schema:foundingDate'),
                'country'     => $team->get('schema:addressCountry'),
                'stadium'     => optional($team->get('schema:homeLocation'))->getUri(),
                'logo'        => $team->get('schema:logo'),
                'owner'       => $team->get('schema:owner'),
                'coach'       => $team->get('schema:coach'),
            ];
        }

        return view('football', compact('clubs'));
    }
}
