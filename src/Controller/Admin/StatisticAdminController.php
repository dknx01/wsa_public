<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller\Admin;

use App\Dto\Api\AdminStatistic;
use App\Entity\Statistic;
use App\Entity\Wahlkreis;
use App\Form\StatisticType;
use App\Repository\StatisticRepository;
use App\Repository\WahlkreisRepository;
use App\Security\ActiveUserVoter;
use App\Statistic\Handler as StatisticHandler;
use App\User\Roles;
use App\Wahlkreis\Handler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/statistic')]
class StatisticAdminController extends AbstractController
{
    #[Route('/')]
    public function index(Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ActiveUserVoter::ACTIVE_USER);
        $this->denyAccessUnlessGranted(Roles::ROLE_USER->name);

        return $this->render(
            'admin/statistic/index.html.twig',
            [
                'states' => $handler->getStates($this->getUser()),
                'areas' => array_flip($handler->getWahlkreiseFormatted($this->getUser())),
            ]
        );
    }

    #[Route('/save', methods: ['POST'], format: 'json')]
    public function save(
        #[MapRequestPayload(acceptFormat: 'json', )] AdminStatistic $statistic,
        StatisticRepository $statisticRepository,
        WahlkreisRepository $wahlkreisRepo,
    ): JsonResponse {
        $this->denyAccessUnlessGranted(ActiveUserVoter::ACTIVE_USER);

        $id = ('Direktkandidaten' === $statistic->type) ? 'uus_add' : 'uus_add_ll';
        if ($this->isCsrfTokenValid($id, $statistic->csrftoken)) {
            $wahlkreis = match ($id) {
                'uus_add_ll' => 'Landesliste',
                default => $wahlkreisRepo->find($statistic->area),
            };
            if ($wahlkreis instanceof Wahlkreis) {
                $wahlkreis = \sprintf('%s (Nr. %d)', $wahlkreis->getName(), $wahlkreis->getNumber());
            }
            $entity = $statisticRepository->findByStateAndArea($statistic->state, $wahlkreis);
            $wahlkreis = match ($statistic->type) {
                'Direktkandidaten' => $wahlkreisRepo->find($statistic->area),
                default => $statistic->area,
            };
            $name = match ($statistic->type) {
                'Direktkandidaten' => \sprintf('%s (Nr. %d)', $wahlkreis?->getName(), $wahlkreis?->getNumber()),
                default => $wahlkreis,
            };
            if (!$entity) {
                $entity = new Statistic();
                $entity->setName($name)
                    ->setBundesland($statistic->state)
                    ->setType($statistic->type)
                    ->setApproved((int) $statistic->uus)
                    ->setUnapproved((int) $statistic->uusUnapproved)
                    ->setUpdatedAt(new \DateTimeImmutable());
            } else {
                $entity->setName($name);
                $entity->setApproved($entity->getApproved() + (int) $statistic->uus)
                    ->setUnapproved($entity->getUnapproved() + (int) $statistic->uusUnapproved)
                    ->setUpdatedAt(new \DateTimeImmutable());
            }
            $statisticRepository->save($entity);

            return $this->json(['approved' => $entity->getApproved(), 'unApproved' => $entity->getUnapproved()]);
        }

        return $this->json([]);
    }

    #[Route('/ueberblick')]
    public function overview(StatisticHandler $handler): Response
    {
        $this->denyAccessUnlessGranted(ActiveUserVoter::ACTIVE_USER);

        return $this->render(
            'admin/statistic/overview.html.twig',
            [
                'statistics' => $handler->findAll($this->getUser()),
            ]
        );
    }

    #[Route('/edit/{id}')]
    public function edit(Statistic $statistic, Request $request, StatisticRepository $repo): Response
    {
        $this->denyAccessUnlessGranted(ActiveUserVoter::ACTIVE_USER);
        /** @todo check if user can edit */
        $form = $this->createForm(StatisticType::class, $statistic);
        $form->handleRequest($request);

        $errors = null;
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $repo->save($form->getData());

                $this->addFlash('success', 'Gespeichert');
            } else {
                $errors = $form->getErrors();
            }
        }

        return $this->render(
            'admin/statistic/edit.html.twig',
            [
                'form' => $form,
                'errors' => $errors,
            ]
        );
    }
}
