<?php

class Controller
{
    protected $apiService;

    public function __construct()
    {
        $this->apiService = new ApiService();
    }

    public function getWeather($latitude, $longitude)
    {
        try {
            $weatherData = $this->apiService->getData($latitude, $longitude);
            return $weatherData;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}