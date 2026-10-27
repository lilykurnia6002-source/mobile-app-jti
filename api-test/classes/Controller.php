<?php

require_once __DIR__ . '/ApiService.php';

class Controller
{
    private $apiService;

    public function __construct()
    {
        $this->apiService = new ApiService();
    }

    public function getWeather($lat, $lon)
    {
        return $this->apiService->getData($lat, $lon);
    }
}