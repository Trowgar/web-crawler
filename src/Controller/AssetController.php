<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AssetController extends AbstractController
{
    /**
     * @Route("/data/cities.json", name="data_asset")
     *
     * @return Response
     */
    public function cities(): Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/src/Data/cities.json';
        $content = file_get_contents($filePath);

        return new Response($content, 200, ['Content-Type' => 'application/json']);
    }
}
