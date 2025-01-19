<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller\Admin;

use App\Dto\Api\FileUpload;
use App\Dto\Api\FileUploadDirektkandidat;
use App\Entity\Document;
use App\Form\AdminDocumentType;
use App\Form\DocumentDirektkandidatenType;
use App\Form\DocumentLandeslisteType;
use App\Repository\DocumentsRepository;
use App\Repository\WahlkreisRepository;
use App\Security\ActiveUserVoter;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Uid\Ulid;

#[Route('/admin/documents')]
class DocumentAdminController extends AbstractController
{
    #[Route('/landesliste')]
    public function landesliste(
        Request $request,
        SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/var/docs/')] string $dir,
        DocumentsRepository $repository,
    ): Response {
        $this->denyAccessUnlessGranted(ActiveUserVoter::ACTIVE_USER);

        $form = $this->createForm(DocumentLandeslisteType::class, new FileUpload());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var FileUpload $fileUpload */
            $fileUpload = $form->getData();
            $doc = new Document();
            $doc->setId(Uuid::uuid4());
            $doc->setName(\sprintf('Landesliste %s', $fileUpload->state));
            $doc->setState($fileUpload->state);
            $doc->setType('Landesliste');
            $doc->setDescription($fileUpload->description);
            $originalFilename = pathinfo($fileUpload->file->getClientOriginalName(), \PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'.'.$fileUpload->file->guessExtension();
            $doc->setFileName($newFilename);

            try {
                $fileUpload->file->move($dir, $newFilename);
                $this->addFlash('success', 'Datei erfolgreich hochgeladen.');
                $repository->save($doc);

                return $this->redirectToRoute('app_admin_documentadmin_landesliste');
            } catch (FileException $e) {
                $this->addFlash('error', 'Datei konnte nicht gespeichert werden.');
            }
            $doc->setFileName($newFilename);
            $repository->save($doc);
        }

        return $this->render(
            'admin/documents/landesliste.html.twig',
            [
                'form' => $form,
            ]
        );
    }

    #[Route('/direktkandidat')]
    public function direktkandidat(
        Request $request,
        SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/var/docs/')] string $dir,
        DocumentsRepository $repository,
        WahlkreisRepository $wahlkreisRepository,
    ): Response {
        $this->denyAccessUnlessGranted(ActiveUserVoter::ACTIVE_USER);

        $form = $this->createForm(DocumentDirektkandidatenType::class, new FileUploadDirektkandidat());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var FileUploadDirektkandidat $fileUpload */
            $fileUpload = $form->getData();
            $doc = new Document();
            $doc->setId(Uuid::uuid4());
            $doc->setWahlkreis(
                $wahlkreisRepository->find(Ulid::fromString($fileUpload->area))
            );
            $doc->setName(\sprintf('Direktkandidat %s', $doc->getWkName()));
            $doc->setState($fileUpload->state);
            $doc->setType('Direktkandidaten');
            $doc->setDescription($fileUpload->description);
            $originalFilename = pathinfo($fileUpload->file->getClientOriginalName(), \PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'.'.$fileUpload->file->guessExtension();
            $doc->setFileName($newFilename);

            try {
                $fileUpload->file->move($dir, $newFilename);
                $this->addFlash('success', 'Datei erfolgreich hochgeladen.');
                $repository->save($doc);

                return $this->redirectToRoute('app_admin_documentadmin_direktkandidat');
            } catch (FileException $e) {
                $this->addFlash('error', 'Datei konnte nicht gespeichert werden.');
            }
            $doc->setFileName($newFilename);
            $repository->save($doc);
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
        DocumentsRepository $documentsRepos,
        SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/var/docs/')] string $dir,
        WahlkreisRepository $wahlkreisRepo,
    ): Response {
        $this->denyAccessUnlessGranted(ActiveUserVoter::ACTIVE_USER);

        $form = $this->createForm(AdminDocumentType::class, $document);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $fileUpload = $form->get('file')->getData();

            if ($fileUpload) {
                $originalFilename = pathinfo($fileUpload->getClientOriginalName(), \PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'.'.$fileUpload->guessExtension();
                $oldFileName = $document->getFileName();
                $document->setFileName($newFilename);
                $fileUpload->move($dir, $newFilename);
                $fs = new Filesystem();
                $fs->remove(\sprintf('%s/%s', $dir, $oldFileName));
            }
            $documentsRepos->save($document);
            $documentsRepos->refresh($document);
        }

        return $this->render(
            'admin/documents/edit.html.twig',
            [
                'form' => $form,
                'document' => $document,
                'states' => $wahlkreisRepo->getStates(),
                'areas' => $wahlkreisRepo->getWahlkreiseFormatted(),
            ]
        );
    }

    #[Route('/wahlkreise/{state}', 'admin_document_area_by_state')]
    public function getAreaByState(string $state, WahlkreisRepository $wahlkreisRepo): JsonResponse
    {
        $this->denyAccessUnlessGranted(ActiveUserVoter::ACTIVE_USER);

        $options = [];
        foreach ($wahlkreisRepo->getWahlkreiseByStateFormatted($state) as $key => $value) {
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
