<?php

namespace Valinteca\Aramex;

use Valinteca\Aramex\Services\AramexService;

class Aramex
{
    public static function createShipment($shipper, $order)
    {
        return (new AramexService)->createShipment($shipper, $order);
    }

    public static function fetchCountries()
    {
        return (new AramexService)->fetchCountries();
    }

    public static function fetchCities($country_code)
    {
        return (new AramexService)->fetchCities($country_code);
    }
}