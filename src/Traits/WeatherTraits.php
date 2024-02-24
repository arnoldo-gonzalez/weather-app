<?php
namespace App\Traits;

trait WeatherTraits {
    private static function validateLocation(string $location) {
        $invalid_symbols = "/[\\;$\+\"\/\<\:\>\&\?\¿\'\´\¨]/";
        return !preg_match($invalid_symbols, $location);
    }

    private function format_data(array $data, string $date_creation) {
        $formated_data = [
            "date_creation" => $date_creation,
            "temperature" => (int) $data["temperature"],
            "temperatureApparent" => (int) $data["temperatureApparent"],
            "precipitationProbability" => (int) $data["precipitationProbability"],
            "humidity" => (int) $data["humidity"],
            "windSpeed" => (int) $data["windSpeed"]
        ];

        return $formated_data;
    }
}
