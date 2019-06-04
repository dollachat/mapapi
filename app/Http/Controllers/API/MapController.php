<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage; // use storage in laravel
// Guzzle -> for api request in php
use GuzzleHttp\Client;

class MapController extends Controller
{
    private $radius = 1500; // default search radius

    public function index(Request $request)
    {
        if(!$request->has('location')){
            $this->error_request();
        }
        $place_params = [
            'query' => [
                'input' => $request->location,
                'inputtype' => 'textquery',
                'fields' => 'formatted_address,geometry',
                'key' => 'AIzaSyAzQ5c-32YyNKOL9ZxV8FKGrypLtx2Unic'
                ]
            ];

        $client = new Client([
            'base_uri' => 'https://maps.googleapis.com/maps/api/place/',
        ]);

        $find_place_response = $client->request('GET','findplacefromtext/json?',$place_params);
        $find_place_body =  json_decode($find_place_response->getBody());

        $formatted_address = $find_place_body->candidates[0]->formatted_address;
        $lat = $find_place_body->candidates[0]->geometry->location->lat;
        $lng = $find_place_body->candidates[0]->geometry->location->lng;

        $location = (string)$lat.','.(string)$lng;

        $nearby_params = [
            'query' => [
                'location' => $location,
                'radius' => (string)$this->radius,
                'type' => 'restaurant',
                'key' => 'AIzaSyAzQ5c-32YyNKOL9ZxV8FKGrypLtx2Unic'
                ]
            ];
        $find_nearby_response = $client->request('GET','nearbysearch/json?',$nearby_params);
        $find_nearby_body =  json_decode($find_nearby_response->getBody());
        $next_page_token = $find_nearby_body->next_page_token; // use for next set of result
        $restaurants = $find_nearby_body->results; // get array of result , maximun return from google is 20 items

        return response()->json($find_nearby_body);
    }

    public function test(Request $request)
    {
        $test = [
          ['name'=>'John','age'=>'24'],
          ['name'=>'Dan','age'=>'26'],
          ['name'=>'Jing','age'=>'44'],
          ['name'=>'Man','age'=>'34'],
          ['name'=>'Free','age'=>'64'],
          ['name'=>'Mon','age'=>'10'],
        ];

        return response()->json($test);
    }
    public function get_photo($maxwidth,$photoreference)
    {
      $api_key = "AIzaSyAzQ5c-32YyNKOL9ZxV8FKGrypLtx2Unic";
      $maxwidth = (string)$maxwidth;
      $photoreference = (string)$photoreference;
      $url = 'https://maps.googleapis.com/maps/api/place/photo?maxwidth='.$maxwidth.'&photoreference='.$photoreference.'&key='.$api_key;
      // $url ='https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference=CmRaAAAABK4wI5XrTtVhwqqWKPaR2J2bTMU-AAkttZAWP6jkLfs9tGSEhYOwkN5UdBqEUgkqB2WvMtj4SA3GgDIOsvsGBkW3b0Yq_pIi8CdWSlIXn-ZD5LuT7cQPENS5D8RSYLhdEhA8042dW4DGpEt0efA08BHnGhSlUeBpjwixP3hBpPpIc11gfNVrmw&key=AIzaSyAzQ5c-32YyNKOL9ZxV8FKGrypLtx2Unic';
      // $client = new Client();
      // $response = $client->request('GET',$url);
      // $response = $response->getBody();
      $result = file_get_contents($url);
      // Storage::put('file.jpg', $result);
      return $result;
      // dd($result);
      // echo $response;
        // return response()->json($test);
    }
}
