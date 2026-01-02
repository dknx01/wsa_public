<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller;

use App\Number\NumbersService;
use App\Repository\WahlkreisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StatisticController extends AbstractController
{
    public function __construct(
        private readonly NumbersService $numbersService,
    ) {
    }

    #[Route('/statistic', name: 'new_statistic', methods: ['GET'])]
    public function overview(WahlkreisRepository $repository): Response
    {
        $allBundeslaender = $repository->findAllStatesWithNumbers();
        foreach ($allBundeslaender as $key => $bundeslaender) {
            $newKey = match ($bundeslaender) {
                'Nordrhein-Westfalen' => 'NRW',
                'Rheinland-Pfalz' => 'RLP',
                default => $bundeslaender,
            };
            $allBundeslaender[$newKey] = $bundeslaender;
            unset($allBundeslaender[$key]);
        }

        return $this->render(
            'statistic/new/index.html.twig',
            [
                'states' => $allBundeslaender,
            ]
        );
    }

    #[Route('/statistic/state/{state}', name: 'app_new_statistic_statedata')]
    public function stateData(string $state): JsonResponse
    {
        return new JsonResponse($this->numbersService->getByState($state));
    }
}
