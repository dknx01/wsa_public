<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller;

use App\Configuration\Configuration;
use App\Dto\Api\DocumentResponse;
use App\Entity\DocumentLandesliste;
use App\Repository\DocumentsRepository;
use App\Result\ResultPrinter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

class WsaController extends AbstractController
{
    public function __construct(private readonly string $documentPath)
    {
    }

    #[Route('/', 'home')]
    public function home(DocumentsRepository $repository, Configuration $configuration): Response
    {
        if ($configuration->resultsAsStart()) {
            return $this->redirectToRoute('results');
        }
        $allBundeslaender = $repository->findAllBundeslaender();
        foreach ($allBundeslaender as $key => $bundeslaender) {
            $newKey = match ($bundeslaender) {
                'Nordrhein-Westfalen' => 'NRW',
                'Rheinland-Pfalz' => 'RLP',
                default => $bundeslaender,
            };
            $allBundeslaender[$newKey] = $bundeslaender;
            unset($allBundeslaender[$key]);
        }

        return $this->render('wsa/index.html.twig', [
            'states' => $allBundeslaender,
        ]);
    }

    #[Route('/results', 'results')]
    public function results(ResultPrinter $resultPrinter): Response
    {
        return $this->render('wsa/index_2.html.twig', [
            'results' => $resultPrinter->getResults(),
        ]);
    }

    #[Route('/documents/bundesland/{state}', 'documentStateList')]
    public function getByState(string $state, DocumentsRepository $repository, Packages $packages): JsonResponse
    {
        $data = [];

        foreach ($repository->findAllByState($state) as $document) {
            $documentResponse = new DocumentResponse(
                $document->getName(),
                $packages->getUrl('images/sticker.png'),
                nl2br((string) $document->getDescription()),
                $this->generateUrl('documentDownload', ['id' => $document->getId()]),
                $state
            );
            if ($document instanceof DocumentLandesliste) {
                array_unshift($data, $documentResponse);
            } else {
                $data[] = $documentResponse;
            }
        }

        return $this->json($data, Response::HTTP_OK);
    }

    #[Route('/documents/download/{id}', 'documentDownload')]
    public function download(string $id, DocumentsRepository $repository): BinaryFileResponse|JsonResponse
    {
        $document = $repository->find($id);
        if (!$document) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }
        $file = new File(\sprintf('%s/%s', $this->documentPath, \sprintf('%s.pdf', $document->getId()->toString())));

        return $this->file($file, $document->getFileName(), ResponseHeaderBag::DISPOSITION_INLINE);
    }

    #[Route('/documents/help', 'help')]
    public function help(): Response
    {
        return $this->render('wsa/help.html.twig');
    }

    #[Route('/documents/search/{search}', 'documentsSearch')]
    public function search(string $search, DocumentsRepository $repository, Packages $packages): JsonResponse
    {
        $data = [];
        $documents = '-' === $search ? $repository->findAll() : $repository->findByName($search);
        foreach ($documents as $document) {
            $data[] = new DocumentResponse(
                $document->getName(),
                $packages->getUrl('images/sticker.png'),
                (string) $document->getDescription(),
                $this->generateUrl('documentDownload', ['id' => $document->getId()]),
                ''
            );
        }

        return $this->json($data, Response::HTTP_OK);
    }
}
