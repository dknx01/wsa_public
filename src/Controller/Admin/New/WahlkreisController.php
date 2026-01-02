<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller\Admin\New;

use App\Entity\User;
use App\Entity\Wahlkreis;
use App\Form\WahlkreisFormType;
use App\Repository\UserRepository;
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
    #[Route('/new', name: 'admin_wahlkreis_new')]
    public function new(Request $request, WahlkreisRepository $wahlkreisRepository, UserRepository $userRepository): Response
    {
        $form = $this->createForm(WahlkreisFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $wahlkreis = new Wahlkreis();
            $wahlkreis->setYear((int) $data['year'])
                ->setName($data['name'])
                ->setState($data['state'])
                ->setNumber($data['number'] ?? null)
                ->setThreshold($data['threshold'] ?? 200)
                ->setWahlkreistyp($data['wahlkreistyp']);
            $wahlkreisRepository->save($wahlkreis);
            $this->addFlash('success', 'Wahlkreis gespeichert');
            /** @var User $user */
            $user = $this->getUser();
            $user->addWahlkreisPermission($wahlkreis);
            $userRepository->save($user);

            return $this->redirectToRoute('admin_wahlkreis_new');
        }

        return $this->render('admin/wahlkreis/new.html.twig',
            [
                'form' => $form,
            ]
        );
    }

    #[Route('/', name: 'admin_wahlkreis_list')]
    public function overview(WahlkreisRepository $repository): Response
    {
        return $this->render('admin/wahlkreis/list.html.twig', ['wahlkreise' => $repository->findAllOrdered()]);
    }

    #[Route('/{id}', name: 'admin_wahlkreis_edit')]
    public function edit(Request $request, Wahlkreis $wahlkreis, WahlkreisRepository $repository): Response
    {
        $form = $this->createForm(WahlkreisFormType::class, $wahlkreis);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($wahlkreis);
            $this->addFlash('success', 'Wahlkreis gespeichert');

            return $this->redirectToRoute('admin_wahlkreis_edit', ['id' => $wahlkreis->getId()]);
        }

        return $this->render('admin/wahlkreis/edit.html.twig', ['form' => $form]);
    }
}
