<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller\Admin\New;

use App\Entity\SupportNumbersLandesliste;
use App\Repository\SupportNumbersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/new/log')]
class AdminLogController extends AbstractController
{
    #[Route('/uu', name: 'adminLogUu', methods: ['GET'])]
    public function overviewSupportNumbers(SupportNumbersRepository $numbersRepository): Response
    {
        return $this->render(
            'admin/new/log/overview_suppport_numbers.html.twig',
            [
                'numbers' => $numbersRepository->findAll(),
            ]
        );
    }

    #[Route('/uu/detail/{id}', name: 'adminLogUuDetail', methods: ['GET'])]
    public function detailSupportNumbers(SupportNumbersLandesliste $supportNumber): Response
    {
        return $this->render(
            'admin/new/log/detail_suppport_numbers.html.twig',
            [
                'entry' => $supportNumber,
            ]
        );
    }
}
