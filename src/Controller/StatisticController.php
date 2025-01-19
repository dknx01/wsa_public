<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller;

use App\Btw\UuCalculator;
use App\Btw\Wahlkreise;
use App\Dto\Statistic;
use App\Repository\StatisticRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StatisticController extends AbstractController
{
    public function __construct(private readonly UuCalculator $calculator)
    {
    }

    #[Route('/statistic/state/{state}')]
    public function stateData(string $state, StatisticRepository $statisticRepository): JsonResponse
    {
        return new JsonResponse($this->buildStatistic($statisticRepository->findByState($state)));
    }

    #[Route('/statistic')]
    public function index(StatisticRepository $statisticRepository): Response
    {
        $allBundeslaender = $statisticRepository->findAllBundeslaender();
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
            'statistic/index.html.twig',
            [
                'states' => $allBundeslaender,
            ]
        );
    }

    /**
     * @param array<\App\Entity\Statistic> $data
     *
     * @return array<Statistic>
     */
    private function buildStatistic(array $data): array
    {
        $statistic = [];
        foreach ($data as $item) {
            $approvedPercentage = $this->calculatePercentage($item, 'Approved');
            $unapprovedPercentage = $this->calculatePercentage($item, 'Unapproved');
            $statisticItem = new Statistic(
                $item->getName(),
                $item->getApproved() ?? 0,
                $approvedPercentage,
                $item->getUnapproved() ?? 0,
                ($item->getUnapproved() ?? 0) + $item->getApproved() ?? 0,
                $unapprovedPercentage,
                $this->calculateColors($approvedPercentage),
                $this->calculateColors($unapprovedPercentage),
                $this->getMax($item, false),
                $this->getStatus($approvedPercentage)
            );
            if (str_ends_with($item->getName(), 'Landesliste')) {
                array_unshift($statistic, $statisticItem);
            } else {
                $statistic[] = $statisticItem;
            }
        }

        return $statistic;
    }

    private function calculateColors(int $param): string
    {
        return match (true) {
            $param <= 25 => 'darkred, red',
            $param <= 50 => 'darkred, red, orange',
            $param <= 75 => 'darkred, red, orange, yellow',
            $param <= 100 => 'darkred, red, orange, yellow, lawngreen',
            $param >= 100 => 'darkred, red, orange, yellow, lawngreen, green',
            default => '',
        };
    }

    public function calculatePercentage(\App\Entity\Statistic $item, string $type): int
    {
        $number = match ($type) {
            'Approved' => $item->getApproved() ?? 0,
            default => ($item->getUnapproved() ?? 0) + ($item->getApproved() ?? 0),
        };

        return match ($item->getType()) {
            'Direktkandidaten', 'Direktkandidat' => (int) floor(($number * 100) / 200),
            default => $this->calculator->calculatePercentage($number, $item->getBundesland()),
        };
    }

    private function getStatus(int $percentage): string
    {
        return match ($percentage >= 100) {
            true => $this->renderView('wsa/checkmark.html.twig'),
            default => '',
        };
    }

    private function getMax(\App\Entity\Statistic $document, bool $withSecurity = false): int
    {
        $max = 'Landesliste' === $document->getType() ? Wahlkreise::UU_NUMBERS[$document->getBundesland()] : 200;

        return match ($withSecurity) {
            true => ($max * UuCalculator::SECURITY) / 100,
            default => 0,
        } + $max;
    }
}
