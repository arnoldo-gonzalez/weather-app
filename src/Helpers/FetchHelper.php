<?php
namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class FetchHelper {
    private static $headers = ["headers" => ["accept" => "application/json"]];

    public static function getData(string $location) {
        $client = new Client();
        $baseUrl = "https://api.tomorrow.io/v4/weather/realtime";   

        try {
            $response = $client->request("GET", $baseUrl . "?location={$location}&apikey={$_ENV["API_KEY"]}", self::$headers);
    
            $data = $response->getBody();
            $array_data = json_decode($data->getContents(), true);

            if (array_key_exists("data", $array_data)) return ["ok" => true, "result" => $array_data["data"]];
            return ["ok" => false];
        } catch (ClientException $e) {
            return ["ok" => false];
        }
    }

    public static function getTime(string $location) {
        $client = new Client();
        $baseUrl = "https://timezone.abstractapi.com/v1/current_time/";

        try {
            $response = $client->request("GET", $baseUrl . "?api_key={$_ENV["API_TIME_KEY"]}&location=$location", self::$headers);

            $data = $response->getBody();
            $array_data = json_decode($data->getContents(), true);
    
            if (array_key_exists("datetime", $array_data)) return ["ok" => true, "datetime" => $array_data["datetime"]];
            return ["ok" => false];
        } catch (ClientException $e) {
            return ["ok" => false];
        }
    }
}
