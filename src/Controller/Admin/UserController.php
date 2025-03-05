<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Repository\UserRepository;
use App\Security\ActiveUserVoter;
use App\User\EditDvo;
use App\User\Roles;
use App\User\UserAdminService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/users')]
class UserController extends AbstractController
{
    #[Route('', name: 'users_overview')]
    public function overview(UserRepository $userRepo): Response
    {
        $this->denyAccessUnlessGranted(ActiveUserVoter::ACTIVE_USER);
        $this->denyAccessUnlessGranted(Roles::ROLE_ADMIN->name);

        return $this->render(
            'admin/user/overview.html.twig',
            [
                'users' => $userRepo->findAll(),
            ]
        );
    }

    #[Route('/{id}', name: 'user_edit', methods: ['POST', 'GET'])]
    public function edit(
        User $user,
        Request $request,
        UserRepository $userRepo,
        UserAdminService $userAdmin,
    ): Response {
        $this->denyAccessUnlessGranted(ActiveUserVoter::ACTIVE_USER);
        $this->denyAccessUnlessGranted(Roles::ROLE_ADMIN->name);

        $errors = null;
        if ($request->isMethod('POST')) {
            if ($this->isCsrfTokenValid('admin_user_edit', $request->get('csrf_token'))) {
                $dvo = EditDvo::fromRequest($request);
                $userAdmin->saveUser($user, $dvo);
            } else {
                $errors[] = 'Fehler im Fomular';
            }
        }

        return $this->render(
            'admin/user/edit.html.twig',
            [
                'user' => $user,
                'errors' => $errors,
                'wahlkreise' => $userAdmin->getPermissionsByUser($user),
            ]
        );
    }

    #[Route('/password/{id}', name: 'user_password')]
    public function password(Request $request, User $user, UserRepository $repository, UserPasswordHasherInterface $hasher): Response
    {
        $errors = null;
        $form = $this->createForm(UserPasswordType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid() && $hasher->isPasswordValid($user, $form->get('password')->getData())) {
                $user->setPassword($hasher->hashPassword($user, $form->get('passwordNew')->getData()));
                $repository->save($user);
                $this->addFlash('success', 'Passwort geändert');
            } else {
                $errors = $form->getErrors();
                $this->addFlash('error', 'Fehler im Formular');
            }
        }

        return $this->render(
            'admin/user/password.html.twig',
            [
                'form' => $form,
                'errors' => $errors,
            ]
        );
    }
}
