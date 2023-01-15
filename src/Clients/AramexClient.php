<?php

namespace Valinteca\Aramex\Clients;

use SoapClient;

class AramexClient
{
    private $api_path = '';

    public function __construct()
    {
        $this->api_path = __DIR__ . '/../api/';
    }

    public function createShipment($data)
    {
        return (new SoapClient($this->api_path . "shipping.wsdl"))->CreateShipments($data);
    }

    public function fetchCountries($data)
    {
        return (new SoapClient($this->api_path . "location.wsdl"))->FetchCountries($data);
    }

    public function fetchCities($data)
    {
        return (new SoapClient($this->api_path . "location.wsdl"))->FetchCities($data);
    }

    public function track($data)
    {
        return (new SoapClient($this->api_path . "tracking.wsdl"))->TrackShipments($data);
    }
}