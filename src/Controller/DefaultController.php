<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller;

use App\Dto\UuListe;
use App\Entity\Document;
use App\Entity\DocumentDirektkandidat;
use App\Entity\DocumentLandesliste;
use App\Repository\DocumentsRepository;
use App\Repository\VerbandsseitenRepository;
use App\Repository\WahlkreisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends AbstractController
{
    #[Route('/impressum', name: 'impressum')]
    public function impressum(): Response
    {
        return $this->render('defaults/impressum.html.twig');
    }

    #[Route('/faq', name: 'faq')]
    public function faq(): Response
    {
        return $this->render('defaults/faq.html.twig');
    }

    #[Route('/uu-liste', name: 'uu-liste')]
    public function uuListe(
        VerbandsseitenRepository $verbandsseitenRepo,
        DocumentsRepository $documentsRepo,
        WahlkreisRepository $wahlkreisRepo,
    ): Response {
        $verbaende = $verbandsseitenRepo->findAll();
        $documents = new ArrayCollection($documentsRepo->findAll());

        $data = [];
        foreach ($verbaende as $value) {
            $liste = new UuListe($wahlkreisRepo->findBy(['state' => $value->getName()]));
            $liste->name = $value->getName();
            $liste->link = $value->getLink();
            $liste->linkName = $value->getLinkName();
            $docs = $documents->filter(fn (Document $document) => $document->getState() === $value->getName());
            /** @var ArrayCollection<int,DocumentLandesliste|DocumentDirektkandidat> $docs */
            foreach ($docs as $doc) {
                if ('Landesliste' === $doc->getType()) {
                    $liste->sendTo = $doc->getDescription();
                    $liste->linkLandesliste = $this->generateUrl('documentDownload', ['id' => $doc->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
                    continue;
                }
                $liste->setWahlkreisLink(
                    $doc->getWkName(),
                    $this->generateUrl('documentDownload', ['id' => $doc->getId()], UrlGeneratorInterface::ABSOLUTE_URL)
                );
            }
            $data[] = $liste;
        }

        return $this->render(
            'defaults/uu-liste.html.twig',
            [
                'overview' => $data,
            ]
        );
    }
}
