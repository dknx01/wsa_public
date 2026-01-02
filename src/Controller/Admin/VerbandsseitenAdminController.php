<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller\Admin;

use App\Entity\Verbandsseiten;
use App\Form\VerbandsseitenType;
use App\Repository\VerbandsseitenRepository;
use App\Security\ActiveUserVoter;
use App\User\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/verbandsseiten')]
#[IsGranted(ActiveUserVoter::ACTIVE_USER)]
class VerbandsseitenAdminController extends AbstractController
{
    #[Route('/neu', 'admin_verbandseiten_neu')]
    public function new(
        Request $request,
        VerbandsseitenRepository $verbandsseitenRepository,
    ): Response {
        $this->denyAccessUnlessGranted(Roles::ROLE_USER->name);

        $form = $this->createForm(VerbandsseitenType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $verbandsseitenRepository->save($form->getData());
            $this->addFlash('success', 'Verbandsseite erfolgreich angelegt.');
        }

        return $this->render(
            'admin/verbandsseiten/new.html.twig',
            [
                'form' => $form,
            ]
        );
    }

    #[Route('/edit/{id}', 'admin_verbandseiten_edit')]
    public function edit(
        Verbandsseiten $verbandsseite,
        Request $request,
        VerbandsseitenRepository $verbandsseitenRepository,
    ): Response {
        $this->denyAccessUnlessGranted(Roles::ROLE_USER->name);

        $form = $this->createForm(VerbandsseitenType::class, $verbandsseite);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $verbandsseitenRepository->save($form->getData());
            $this->addFlash('success', 'Verbandsseite erfolgreich geändert.');
        }

        return $this->render(
            'admin/verbandsseiten/edit.html.twig',
            [
                'form' => $form,
            ]
        );
    }

    #[Route('/', 'admin_verbandseiten_overview')]
    public function overview(
        VerbandsseitenRepository $verbandsseitenRepository,
    ): Response {
        $this->denyAccessUnlessGranted(Roles::ROLE_USER->name);

        return $this->render(
            'admin/verbandsseiten/overview.html.twig',
            [
                'seiten' => $verbandsseitenRepository->findAll(),
            ]
        );
    }
}
