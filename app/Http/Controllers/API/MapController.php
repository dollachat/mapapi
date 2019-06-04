<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage; // use storage in laravel
use Illuminate\Support\Facades\Cache; // cache lib

// Guzzle -> for api request in php
use GuzzleHttp\Client;

class MapController extends Controller
{
    private $radius = 1500; // default search radius

    public function index(Request $request)
    {
        if(!$request->has('location')){
          // error when no location from search box
            $this->error_request();
        }
        if(Cache::get( 'locationkey') === $request->location){
          // if location match cache key return cache
          $returncache = json_decode(Cache::get( 'nearbycache'));
          return response()->json($returncache);

        } else {
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

        /*
        use find place to get lat and lng of search place ie. Bangsue then use nearbysearch of that lat and lng
        */

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
        Cache::put( 'locationkey', $request->location , 300);  // cache for 5 minutes
        Cache::put( 'nearbycache', json_encode($find_nearby_body) , 300);  // cache for 5 minutes
        return response()->json($find_nearby_body);
      }
    }


    public function next_set(Request $request)
    {
      // same with nearby search function
      $client = new Client([
          'base_uri' => 'https://maps.googleapis.com/maps/api/place/',
      ]);



      $next_page_param = [
          'query' => [
              'pagetoken' => $request->next_page_token,
              'key' => 'AIzaSyAzQ5c-32YyNKOL9ZxV8FKGrypLtx2Unic'
              ]
          ];
      $find_nearby_response = $client->request('GET','nearbysearch/json?',$next_page_param);
      $find_nearby_body =  json_decode($find_nearby_response->getBody());

      return response()->json($find_nearby_body);
    }

    public function get_photo($maxwidth,$photoreference)
    {
      /*
      return img from photoreference to src tag of html
      */
      $api_key = "AIzaSyAzQ5c-32YyNKOL9ZxV8FKGrypLtx2Unic";
      $maxwidth = (string)$maxwidth;
      $photoreference = (string)$photoreference;
      $url = 'https://maps.googleapis.com/maps/api/place/photo?maxwidth='.$maxwidth.'&photoreference='.$photoreference.'&key='.$api_key;
      $result = file_get_contents($url);
      return $result;

    }
}
