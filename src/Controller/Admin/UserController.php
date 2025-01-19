<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Security\ActiveUserVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/users')]
class UserController extends AbstractController
{
    #[Route('', name: 'users_overview')]
    public function overview(UserRepository $userRepo): Response
    {
        $this->denyAccessUnlessGranted(ActiveUserVoter::ACTIVE_USER);

        return $this->render(
            'admin/user/overview.html.twig',
            [
                'users' => $userRepo->findAll(),
            ]
        );
    }

    #[Route('/{id}', name: 'user_edit')]
    public function edit(User $user, Request $request, UserRepository $userRepo): Response
    {
        $this->denyAccessUnlessGranted(ActiveUserVoter::ACTIVE_USER);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $errors = null;
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $userRepo->save($form->getData());

                $this->addFlash('success', 'Gespeichert');
            } else {
                $errors = $form->getErrors();
            }
        }

        return $this->render(
            'admin/user/edit.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
                'errors' => $errors,
            ]
        );
    }
}
