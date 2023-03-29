<?php

namespace App\Message;

class Weather
{
    private string $city;
    private string $mode;

    public function __construct(string $city, string $mode)
    {
        $this->city = $city;
        $this->mode = $mode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getMode(): string
    {
        return $this->mode;
    }
}
