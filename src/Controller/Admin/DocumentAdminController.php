<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller\Admin;

use App\Document\DocumentType;
use App\Document\Handler;
use App\Document\UnsupportedDocumentTypeException;
use App\Dto\Api\FileUpload;
use App\Dto\Api\FileUploadDirektkandidat;
use App\Entity\Document;
use App\Form\AdminDocumentType;
use App\Form\DocumentDirektkandidatenType;
use App\Form\DocumentLandeslisteType;
use App\Repository\DocumentsRepository;
use App\Repository\WahlkreisRepository;
use App\Security\ActiveUserVoter;
use App\User\Roles;
use App\Wahlkreis\Handler as WahlkreisHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/documents')]
class DocumentAdminController extends AbstractController
{
    public function __construct(private readonly Handler $handler)
    {
    }

    #[Route('/landesliste')]
    public function landesliste(
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(ActiveUserVoter::ACTIVE_USER);
        $this->denyAccessUnlessGranted(Roles::ROLE_USER->name);

        $form = $this->createForm(DocumentLandeslisteType::class, new FileUpload());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var FileUpload $fileUpload */
            $fileUpload = $form->getData();
            try {
                $this->handler->handleUpload($fileUpload, DocumentType::LANDESLISTE);
                $this->addFlash('success', 'Datei erfolgreich hochgeladen.');

                return $this->redirectToRoute('app_admin_documentadmin_landesliste');
            } catch (FileException|UnsupportedDocumentTypeException $e) {
                $this->addFlash('error', 'Datei konnte nicht gespeichert werden.');
            }
        }

        return $this->render(
            'admin/documents/landesliste.html.twig',
            [
                'form' => $form,
            ]
        );
    }

    #[Route('/direktkandidat')]
    public function direktkandidat(Request $request): Response
    {
        $this->denyAccessUnlessGranted(ActiveUserVoter::ACTIVE_USER);
        $this->denyAccessUnlessGranted(Roles::ROLE_USER->name);

        $form = $this->createForm(DocumentDirektkandidatenType::class, new FileUploadDirektkandidat());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var FileUploadDirektkandidat $fileUpload */
            $fileUpload = $form->getData();
            try {
                $this->handler->handleUpload($fileUpload, DocumentType::DIREKTKANDIDAT_BTW);
                $this->addFlash('success', 'Datei erfolgreich hochgeladen.');

                return $this->redirectToRoute('app_admin_documentadmin_direktkandidat');
            } catch (FileException|UnsupportedDocumentTypeException $e) {
                $this->addFlash('error', 'Datei konnte nicht gespeichert werden.');
            }
        }

        return $this->render(
            'admin/documents/direktkandidaten.html.twig',
            [
                'form' => $form,
            ]
        );
    }

    #[Route('/ueberblick', 'admin_document_overview')]
    public function overview(DocumentsRepository $documentsRepos): Response
    {
        $this->denyAccessUnlessGranted(ActiveUserVoter::ACTIVE_USER);
        $this->denyAccessUnlessGranted(Roles::ROLE_ADMIN->name);

        return $this->render(
            'admin/documents/overview.html.twig',
            [
                'documents' => $documentsRepos->findAll(),
            ]
        );
    }

    #[Route('/{id}', 'admin_document_edit')]
    public function edit(
        Document $document,
        Request $request,
        WahlkreisRepository $wahlkreisRepo,
        WahlkreisHandler $handler,
    ): Response {
        $this->denyAccessUnlessGranted(ActiveUserVoter::ACTIVE_USER);

        $form = $this->createForm(AdminDocumentType::class, $document);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $fileUpload = $form->get('file')->getData();

            $this->handler->handleEdit($document, $fileUpload);
            $this->addFlash('success', 'Datei erfolgreich gespeichert.');
        }

        return $this->render(
            'admin/documents/edit.html.twig',
            [
                'form' => $form,
                'document' => $document,
                'states' => $wahlkreisRepo->getStates(),
                'areas' => $handler->getWahlkreiseFormatted(),
            ]
        );
    }

    #[Route('/wahlkreise/{state}', 'admin_document_area_by_state')]
    public function getAreaByState(string $state, WahlkreisHandler $handler): JsonResponse
    {
        $this->denyAccessUnlessGranted(ActiveUserVoter::ACTIVE_USER);

        $options = [];
        foreach ($handler->getWahlkreiseByStateFormatted($state, $this->getUser()) as $key => $value) {
            $options[] = \sprintf('<option value="%s">%s</option>', $key, $value);
        }

        return $this->json($options);
    }

    #[Route('/delete/{id}', 'admin_document_delete')]
    public function delete(
        Document $document,
        DocumentsRepository $documentsRepos,
        #[Autowire('%kernel.project_dir%/var/docs/')] string $dir,
    ): Response {
        $this->denyAccessUnlessGranted(ActiveUserVoter::ACTIVE_USER);

        $documentsRepos->delete($document);
        $fs = new Filesystem();
        $fs->remove(\sprintf('%s/%s', $dir, $document->getFileName()));
        $this->addFlash('success', 'Dokument gelöscht');

        return $this->redirectToRoute('admin_document_overview');
    }
}
