<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller\Admin;

use App\Dto\WahlkreisType;
use App\Entity\Wahlkreis;
use App\Form\WahlkreisFormType;
use App\Repository\WahlkreisRepository;
use App\User\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/wahlkreis')]
#[IsGranted(Roles::ROLE_USER->name)]
class WahlkreisController extends AbstractController
{
    #[Route('/new', name: 'wahlkreis_new')]
    public function new(Request $request, WahlkreisRepository $wahlkreisRepository): Response
    {
        $form = $this->createForm(WahlkreisFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $wahlkreis = new Wahlkreis();
            $wahlkreis->setYear((int) $data['year'])
                ->setName($data['name'])
                ->setState($data['state'])
                ->setNumber($data['number'] ?? 0)
                ->setThreshold($data['threshold'] ?? 200)
                ->setType(WahlkreisType::from($data['type']));
            $wahlkreisRepository->save($wahlkreis);
            $this->addFlash('success', 'Wahlkreis gespeichert');

            return $this->redirectToRoute('wahlkreis_new');
        }

        return $this->render('admin/wahlkreis/new.html.twig',
            [
                'form' => $form,
            ]
        );
    }
}
