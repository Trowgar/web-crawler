<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Message\Weather;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class DashboardController extends AbstractController
{
    private MessageBusInterface $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard.html.twig');
    }

    #[Route('/weather', name: 'app_weather')]
    public function weather(Request $request): JsonResponse
    {
        $city = $request->query->get('city');
        $mode = $request->query->get('mode');

        $weather = new Weather($city, $mode);
        $data = $this->bus->dispatch($weather)->last(HandledStamp::class)->getResult();

        return $this->json([
            'date' => $data['date'],
            'temperature' => $data['temperature'],
            'wind_speed' => $data['wind_speed'],
        ]);
    }
}
