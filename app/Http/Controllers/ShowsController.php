<?php

namespace App\Http\Controllers;

use \Illuminate\Http\Request;
use GuzzleHttp\Client;

class ShowsController extends Controller
{
    public function index(Request $request) {
        $client = new Client([
            'base_uri' => 'http://api.tvmaze.com'
        ]);
        
        if (!$request->has('page') || !$request->has('amount')) {
            return response()->json(['code' => 0, 'message' => 'No more data'], 400);
        }
        
        $page = $request->get('page');
        $amount = $request->get('amount');
        $startpoint = $page * $amount;
        $resultCount = 0;
        $responseBody = [];
        $currentPage = 0;
        
        while ($startpoint + $amount > $resultCount) {
            $extReq = $client->request('GET', 'shows?page=' . $currentPage);
            
            if ($extReq->getStatusCode() == 404) {
                return response()->json(['code' => 1, 'message' => 'No more data'], 404);
            }
            
            $responseBody = array_merge($responseBody, json_decode($extReq->getBody()->getContents()));
            $resultCount = count($responseBody);
            $currentPage++;
        }
        
        return response(array_slice($responseBody, $startpoint, $amount))->header('Content-Type', 'application/json');
    }
}
