<?php

namespace App\MessageHandler;

use App\Message\Weather;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherHandler
{
    private $api_key;
    private $api_endpoint;
    private $client;
    private $logger;

    public function __construct(string $api_key, string $api_endpoint, HttpClientInterface $client, LoggerInterface $logger)
    {
        $this->api_key = $api_key;
        $this->api_endpoint = $api_endpoint;
        $this->client = $client;
        $this->logger = $logger;
    }

    public function __invoke(Weather $message)
    {
        $city = $message->getCity();
        $mode = $message->getMode();

        try {
            if ($mode === 'today') {
                $response = $this->client->request('GET', "{$this->api_endpoint}current?city={$city}&key={$this->api_key}");
                $data = json_decode($response->getContent(), true);
                $weatherData = $data['data'][0];
                $temperature = round($weatherData['temp']);
                $wind_speed = round($weatherData['wind_spd'] * 3.6);
            } elseif ($mode === 'last_7_days') {
                $temperatureSum = 0;
                $windSpeedSum = 0;

                for ($i = 1; $i <= 7; $i++) {
                    $endDate = (new DateTime())->modify("-{$i} days")->format('Y-m-d');
                    $startDate = (new DateTime())->modify("-{$i} days")->modify('-1 day')->format('Y-m-d');
                    $response = $this->client->request('GET', "{$this->api_endpoint}history/daily?city={$city}&start_date={$startDate}&end_date={$endDate}&key={$this->api_key}");
                    $data = json_decode($response->getContent(), true);
                    $weatherData = $data['data'][0];
                    $temperatureSum += $weatherData['max_temp'];
                    $windSpeedSum += $weatherData['wind_spd'] * 3.6;
                }

                $temperature = round($temperatureSum / 7);
                $wind_speed = round($windSpeedSum / 7);
            } else {
                throw new \InvalidArgumentException("Invalid mode specified: {$mode}");
            }
        } catch (\Exception $e) {
            $this->logger->error('Error while fetching weather data: ' . $e->getMessage());
            return [
                'error' => 'An error occurred while fetching weather data. Please try again later.',
            ];
        }

        return [
            'date' => $mode === 'today' ? (new DateTime())->format('Y-m-d') : 'Last 7 days average',
            'temperature' => $temperature,
            'wind_speed' => $wind_speed,
        ];
    }

}
