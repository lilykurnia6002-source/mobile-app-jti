<?php

class ApiService
{
    public function getData($lat, $lon) {
        $url = "https://weather.ewalabs.com/api/v1?lat=".$lat."&lon=".$lon;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $response = curl_exec($ch);

        if(curl_errno($ch)){
            throw new Exception("Curl Error: " . curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, true);
    }
}