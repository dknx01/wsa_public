<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller;

use App\Captcha\CaptchaGenerator;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserRepository $userRepo,
        CaptchaGenerator $captchaGenerator,
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($userRepo->emailExists($user->getEmail())) {
                $this->addFlash('error', 'Benutzer existiert bereits.');
            } else {
                // encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $user->setActive(false);
                $userRepo->save($user);

                $this->addFlash('success', 'Registrierung erfolgreich. Bitte warte auf die Freischaltung.');

                return $this->redirectToRoute('home');
            }
        }
        $captch = $captchaGenerator->generateCaptcha();
        $request->getSession()->set('captcha', $captchaGenerator->getCaptchaCode());

        return $this->render('registration/register.html.twig', [
            'captcha' => $captch,
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/register/captcha_reload', name: 'registration_reloadcaptcha')]
    public function reloadCaptcha(Request $request, CaptchaGenerator $captchaGenerator): Response
    {
        $captcha = $captchaGenerator->generateCaptcha();
        $request->getSession()->set('captcha', $captchaGenerator->getCaptchaCode());

        return $this->json(['captcha' => $captcha]);
    }
}
