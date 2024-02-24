<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\FetchHelper;
use App\Traits\WeatherTraits;
use App\Models\WeatherModel;

class WeatherController {
    use WeatherTraits;

    private function return_json(Response $res, array $data, int $code) {
        $payload = json_encode($data);
        $res->getBody()->write($payload);
        return $res
            ->withHeader("Content-Type", "application/json")
            ->withStatus($code);
    }

    function get_newest(Request $req, Response $res, array $args) {
        $queryParams = $req->getQueryParams();
        if (!array_key_exists("location", $queryParams) || !self::validateLocation($queryParams["location"])) 
            return self::return_json($res, ["ok" => false, "message" => "Location not specified or not valid"], 400);

        $location = strtolower($queryParams["location"]);
        $response_data = FetchHelper::getData($location);
        $actualDate = FetchHelper::getTime($location);
        if (!$response_data["ok"] || !$actualDate["ok"])
            return self::return_json($res, ["ok" => false, "message" => "Location not found, or network error"], 400);

        $result_data = $response_data["result"];
        $date_creation = $actualDate["datetime"];
        $data = self::format_data($result_data["values"], $date_creation);

        $was_updated = WeatherModel::updateOrInsertCache($location, $data, $date_creation);
        if (!$was_updated) return self::return_json($res, ["ok" => false, "message" => "Something went wrong, try again later"], 400);

        return self::return_json($res, ["ok" => true, "message" => "Everythig is fine", "data" => $data], 200);
    }

    function get_cache(Request $req, Response $res, array $args) {
        $queryParams = $req->getQueryParams();
        if (!array_key_exists("location", $queryParams) || !self::validateLocation($queryParams["location"])) 
            return self::return_json($res, ["ok" => false, "message" => "Location not specified or not valid"], 400);

        $location = strtolower($queryParams["location"]);
        $cache = WeatherModel::findCache(strtolower($location));
        if (!$cache["ok"]) return self::return_json($res, ["ok" => false, "message" => "Something went wrong, try again later"], 400);
        if ( !($cache["rowCount"] > 0) )
            return self::return_json($res, ["ok" => false, "message" => "There is no cache for this location"], 200);

        $data = self::format_data($cache["data"], $cache["data"]["date_creation"]);
        return self::return_json($res, ["ok" => true, "message" => "Everythig is fine", "data" => $data], 200);
    }
}
