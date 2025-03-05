<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\SupportNumbers;

use App\Btw\UuCalculator;
use App\Btw\Wahlkreise;
use App\Dto\Statistic;
use App\Entity\SupportNumbersLandesliste;
use App\Repository\SupportNumbersRepository;
use App\SupportNumbers\Dvo\StateItem;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Output
{
    public function __construct(
        private readonly SluggerInterface $slugger,
        private readonly CacheInterface $numbersCache,
        private readonly Environment $twig,
        private readonly UuCalculator $calculator,
        private readonly SupportNumbersRepository $supportNumbersRepo,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @return Statistic[]
     *
     * @throws InvalidArgumentException
     */
    public function getDataByState(string $state): array
    {
        /** @var StateItem[] $data */
        $data = [];
        foreach ($this->supportNumbersRepo->findByState($state) as $number) {
            $key = $this->slugger->slug($number->getName());
            if (\array_key_exists($key->toString(), $data)) {
                continue;
            }
            $data[$key->toString()] = new StateItem(
                $number->getBundesland(),
                $number->getName(),
                $this->numbersCache->get($key, fn (ItemInterface $item) => $this->recalculate($number)),
                $number->getType(),
                $number->getWahlkreis()
            );
        }

        return $this->buildStatistic($data);
    }

    /**
     * @param StateItem[] $data
     *
     * @return Statistic[]
     */
    private function buildStatistic(array $data): array
    {
        $statistic = [];
        foreach ($data as $item) {
            $approvedPercentage = $this->calculatePercentage($item, 'Approved');
            $unapprovedPercentage = $this->calculatePercentage($item, 'Unapproved');
            try {
                $statisticItem = new Statistic(
                    $item->name,
                    $item->item->approved,
                    $approvedPercentage,
                    $item->item->unapproved,
                    $item->item->unapproved + $item->item->approved,
                    $unapprovedPercentage,
                    $this->calculateColors($approvedPercentage),
                    $this->calculateColors($unapprovedPercentage),
                    $this->getMax($item),
                    $this->getStatus($approvedPercentage),
                    $item->type
                );
                if (str_starts_with($item->name, 'Landesliste')) {
                    array_unshift($statistic, $statisticItem);
                } else {
                    $statistic[] = $statisticItem;
                }
            } catch (LoaderError|RuntimeError|SyntaxError $e) {
                $this->logger->error("View error: {$e->getMessage()}");
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
            $param > 100 => 'darkred, red, orange, yellow, lawngreen, green', /* @phpstan-ignore-line */
            default => '',
        };
    }

    private function calculatePercentage(StateItem $item, string $type): int
    {
        $number = match ($type) {
            'Approved' => $item->item->approved,
            default => $item->item->unapproved + $item->item->approved,
        };

        return match ($item->type) {
            'Direktkandidaten', 'Direktkandidat', Type::DK_BTW->value => (int) floor(($number * 100) / 200),
            default => $this->calculator->calculatePercentage($number, $item),
        };
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    private function getStatus(int $percentage): string
    {
        return match ($percentage >= 100) {
            true => $this->twig->render('wsa/checkmark.html.twig'),
            default => '',
        };
    }

    private function getMax(StateItem $document): int
    {
        return match ($document->type) {
            Type::LL_BTW->value, 'Landesliste' => Wahlkreise::UU_NUMBERS[$document->state],
            default => $document->wahlkreis->getThreshold(),
        };
    }

    private function recalculate(SupportNumbersLandesliste $supportNumber): SupportNumbersItem
    {
        return new SupportNumbersItem(...$this->supportNumbersRepo->getNumbersByName($supportNumber->getName()));
    }
}
