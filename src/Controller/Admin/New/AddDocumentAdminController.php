<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller\Admin\New;

use App\Document\Handler;
use App\Dto\Document\Document;
use App\Form\Admin\Document\AddDocumentType;
use App\Security\ActiveUserVoter;
use App\User\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Flow\FormFlowInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(ActiveUserVoter::ACTIVE_USER)]
class AddDocumentAdminController extends AbstractController
{
    public function __construct(
        private Handler $handler,
    ) {
    }

    #[Route('/admin/document/new', 'admin_document_add')]
    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessGranted(Roles::ROLE_USER->name);

        /** @var FormFlowInterface $flow */
        $flow = $this->createForm(AddDocumentType::class, new Document())
            ->handleRequest($request);

        if ($flow->isSubmitted() && $flow->isValid() && $flow->isFinished()) {
            /** @var Document $data */
            $data = $flow->getData();
            $this->handler->handleUploadDocument($data);
            $this->addFlash('success', 'Datei erfolgreich hochgeladen.');

            return $this->redirectToRoute('admin_document_add');
        }

        return $this->render('admin/documents/add.html.twig', [
            'form' => $flow->getStepForm(),
        ]);
    }
}
