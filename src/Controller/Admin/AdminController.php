<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller\Admin;

use App\Security\ActiveUserVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted(ActiveUserVoter::ACTIVE_USER)]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin')]
    public function __invoke(): Response
    {
        $this->denyAccessUnlessGranted(ActiveUserVoter::ACTIVE_USER);

        return $this->render(
            'admin/index.html.twig',
            [
            ]
        );
    }
}
