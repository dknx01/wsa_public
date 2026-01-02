<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller\Admin\New;

use App\Entity\Wahlkreistyp;
use App\Form\WahlkreistypeType;
use App\Repository\WahlkreistypRepository;
use App\User\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/wahlkreistype')]
#[IsGranted(Roles::ROLE_USER->name)]
class WahlkreisTypeController extends AbstractController
{
    #[Route('/new', name: 'admin_wahlkreistype_new')]
    public function new(Request $request, WahlkreistypRepository $wahlkreistypRepository): Response
    {
        $form = $this->createForm(WahlkreistypeType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $wahlkreistypRepository->save($form->getData());
            $this->addFlash('success', 'Wahlkreistyp gespeichert');

            return $this->redirectToRoute('admin_wahlkreistype_new');
        }

        return $this->render('admin/wahlkreistype/new.html.twig',
            [
                'form' => $form,
            ]
        );
    }

    #[Route('/', name: 'admin_wahlkreistyp_list')]
    public function overview(WahlkreistypRepository $repository): Response
    {
        return $this->render('admin/wahlkreistype/list.html.twig', ['wahlkreistypes' => $repository->findAllOrdered()]);
    }

    #[Route('/{id}', name: 'admin_wahlkreistype_edit')]
    public function edit(Request $request, Wahlkreistyp $wahlkreistyp, WahlkreistypRepository $repository): Response
    {
        $form = $this->createForm(WahlkreistypeType::class, $wahlkreistyp);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($wahlkreistyp);
            $this->addFlash('success', 'Wahlkreistyp gespeichert');

            return $this->redirectToRoute('admin_wahlkreistype_edit', ['id' => $wahlkreistyp->getId()]);
        }

        return $this->render('admin/wahlkreistype/edit.html.twig', ['form' => $form]);
    }
}
