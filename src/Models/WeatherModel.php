<?php
namespace App\Models;

use PDO;

class WeatherModel {
    private static function generate_changes_str(array $changes) {
        $changes_str = "";
        foreach($changes as $field => $new_val) {
            $changes_str .= "$field = '$new_val', ";
        }

        return substr($changes_str, 0, -2);
    }

    private static function execute(string $query) {
        $connection = new PDO("mysql:host={$_ENV["DB_HOST"]};dbname={$_ENV["DB_NAME"]}", $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);
        $connection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stament = $connection->prepare($query);
        $results = $stament->execute();
        $result_code = $stament->errorCode();

        return $result_code === "00000" 
            ? ["ok" => true, "rowCount" => $stament->rowCount(), "data" => $stament->fetch(PDO::FETCH_ASSOC)]
            : ["ok" => false, "code" => $result_code];
    }

    static function findCache (string $location) {
        $query = "SELECT * FROM weather_info WHERE location = '{$location}'";
        $result = self::execute($query);
        return $result;
    }

    static function updateOrInsertCache(string $location, array $data, string $actual_date) {
        $prev_cache = self::findCache($location);
        if (!$prev_cache["ok"]) return false;

        if ($prev_cache["rowCount"] > 0) {
            $changes = self::generate_changes_str($data);
            $query = "UPDATE weather_info SET $changes WHERE id = {$prev_cache["data"]["id"]}";
        } else {
            $query = "INSERT INTO weather_info 
                (location, temperature, temperatureApparent, precipitationProbability, humidity, windSpeed, date_creation)
                VALUES ('{$location}', {$data["temperature"]}, {$data["temperatureApparent"]}, {$data["precipitationProbability"]},
                        {$data["humidity"]}, {$data["windSpeed"]}, '{$actual_date}')";
        }

        $result = self::execute($query);
        return $query;
    }
}
