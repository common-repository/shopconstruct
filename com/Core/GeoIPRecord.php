<?php


namespace ShopCT\Core;

/**
 * Geo IP Record class.
 */
class GeoIPRecord
{
    /**
     * Country code.
     *
     * @var string
     */
    public $country_code;

    /**
     * 3 letters country code.
     *
     * @var string
     */
    public $country_code3;

    /**
     * Country name.
     *
     * @var string
     */
    public $country_name;

    /**
     * Region.
     *
     * @var string
     */
    public $region;

    /**
     * City.
     *
     * @var string
     */
    public $city;

    /**
     * Postal code.
     *
     * @var string
     */
    public $postal_code;

    /**
     * Latitude
     *
     * @var float
     */
    public $latitude;

    /**
     * Longitude.
     *
     * @var float
     */
    public $longitude;

    /**
     * Area code.
     *
     * @var string
     */
    public $area_code;

    /**
     * DMA Code.
     *
     * Metro and DMA code are the same.
     * Use metro code instead.
     *
     * @var int
     */
    public $dma_code;

    /**
     * Metro code.
     *
     * @var int
     */
    public $metro_code;

    /**
     * Continent code.
     *
     * @var string
     */
    public $continent_code;
}
