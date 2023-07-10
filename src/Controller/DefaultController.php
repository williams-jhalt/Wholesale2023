<?php

namespace App\Controller;

use App\Service\StatisticsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_default')]
    public function index(StatisticsService $statisticsService): Response
    {

        $statistics = $statisticsService->getGeneralStatistics();

        return $this->render('default/index.html.twig', [
            'statistics' => $statistics
        ]);
    }
}
