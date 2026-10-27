<?php

class ApiService
{
    public function getData($lat, $lon)
    {
        $url = "https://weather.ewalabs.com/api/v1?lat=$lat&lon=$lon";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            die("Curl Error: " . curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, true);
    }
}