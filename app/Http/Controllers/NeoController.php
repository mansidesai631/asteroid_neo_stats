<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use GuzzleHttp\Client;

class NeoController extends Controller
{
    public function callNeoApi($from_date, $to_date)
    {
        try {
            $client = new Client();
            $url = "https://api.nasa.gov/neo/rest/v1/feed?start_date=$from_date&end_date=$to_date&api_key=CaVsI0sJozezYJl2SjKErJmjjW2rNk31y7TkyAVO";
            $res = $client->request('GET', $url, []);
            $res = json_decode($res->getBody()->getContents(), true);
            return $res;
        } catch(\Exception $ex) {
            return response()->json(["message" => "Something went wrong."],403);
        }
    }

    public function getapidata(Request $request)
    {
        $input = $request->all();
        $from_date=$input['fromDate'];
        $to_date=$input['toDate'];

        $neo_api_data = $this->callNeoApi($from_date, $to_date);
        
        if(is_array($neo_api_data)){
            $data = $this->getReqiredData($neo_api_data);
            $data['fromdate']= $from_date;
            $data['todate']= $to_date;
            return response()->json($data, 200);
        } else{
            return response()->json(["message" => "Something went wrong."],403);
        }
    }

    public function getReqiredData($neo_api_data)
    {
        $neo_data_by_date = [];
        $neo_by_array = [];
        $E = [];
        $neo_velocity_kmph = [];
        $neo_distance_km = [];
        $neo_diameter_km = [];
        $neo_count_by_date = [];

        foreach ($neo_api_data['near_earth_objects'] as $key => $value) {
            $neo_data_by_date[$key] = $value;
            foreach ($neo_data_by_date[$key] as $data_by_date) {
                $neo_by_array[] = $data_by_date;

            }
        }
        $neo_data_by_date_arrkeys = array_keys($neo_data_by_date);
        foreach ($neo_data_by_date_arrkeys as $key => $value) {
            $neo_count_by_date[$value] = count($neo_data_by_date[$value]);
        }
        $neo_count_by_date_arry_keys = array_keys($neo_count_by_date);
        $neo_count_by_date_arry_values = array_values($neo_count_by_date);


        foreach ($neo_by_array as $neo) {
            $E[] = $neo;
            $neo_diameter_km[] = $neo['estimated_diameter']['kilometers'];
            $neo_velocity_kmph[] = $neo['close_approach_data'][0]['relative_velocity']['kilometers_per_hour'];
            $neo_distance_km[] = $neo['close_approach_data'][0]['miss_distance']['kilometers'];
        }

        foreach($neo_diameter_km as $diameter_km){
            $min_diameter_km[] = $diameter_km['estimated_diameter_min'];
            $max_diameter_km[] = $diameter_km['estimated_diameter_max'];
        }
        $average_min = array_sum($min_diameter_km);
        $average_max = array_sum($max_diameter_km);
        $average = ($average_min+$average_max)/2;

        arsort($neo_velocity_kmph);
        $fastestAseroid = Arr::first($neo_velocity_kmph);
        $fastestAseroidkey = array_key_first($neo_velocity_kmph);
        $fastestAseroidId = $neo_by_array[$fastestAseroidkey]['id'];

        asort($neo_distance_km);
        $closestAseroid = Arr::first($neo_distance_km);
        $closestAseroidkey = array_key_first($neo_velocity_kmph);
        $closestAseroidId = $neo_by_array[$closestAseroidkey]['id'];

        $data['fastestAseroidId']= $fastestAseroidId;
        $data['fastestAseroid']= $fastestAseroid;
        $data['closestAseroidId']= $closestAseroidId;
        $data['closestAseroid']= $closestAseroid;
        $data['neo_count_by_date_arry_keys']= $neo_count_by_date_arry_keys;
        $data['neo_count_by_date_arry_values']= $neo_count_by_date_arry_values;
        $data['average']= $average;

        return $data;
    }
}