<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller\Admin\New;

use App\Entity\WahlkreisNumber;
use App\Form\Admin\StatisticEditType;
use App\Form\Admin\StatisticType;
use App\Number\NumberDto;
use App\Number\NumbersService;
use App\Repository\WahlkreisNumberRepository;
use App\Security\ActiveUserVoter;
use App\User\Roles;
use App\Wahlkreis\Handler;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/statistic')]
#[IsGranted(Roles::ROLE_USER->name)]
class AdminStatisticController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    #[Route('/', name: 'statistic_new_overview_all', methods: ['GET'])]
    public function overviewAll(WahlkreisNumberRepository $repository): Response
    {
        return $this->render('admin/new/statistic/overview_all.html.twig', ['entries' => $repository->findAllActive()]);
    }

    #[Route('/partial/all', name: 'statistic_new_partial_all', methods: ['GET'])]
    public function partialAll(WahlkreisNumberRepository $repository): Response
    {
        return $this->render('admin/new/statistic/table.html.twig', ['entries' => $repository->findAllOrdered()]);
    }

    #[Route('/partial/all/active', name: 'statistic_new_partial_all_active', methods: ['GET'])]
    public function partialAllActive(WahlkreisNumberRepository $repository): Response
    {
        return $this->render('admin/new/statistic/table.html.twig', ['entries' => $repository->findAllActive()]);
    }

    #[Route(path: '/new', name: 'admin_new_statistic_index_new')]
    public function new(Request $request, NumbersService $numbersService): Response
    {
        $this->denyAccessUnlessGranted(ActiveUserVoter::ACTIVE_USER);
        $dataClass = NumberDto::class;
        $form = $this->createForm(
            type: StatisticType::class,
            options: [
                'data_class' => $dataClass,
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var NumberDto $number */
            $number = $form->getData();
            try {
                $numbersService->save($number, $this->getUser());
                $this->addFlash('success', 'Erfolgreich gespeichert.');
            } catch (InvalidArgumentException $e) {
                $this->addFlash('error', 'Fehler beim Speichern der Daten');
                $this->logger->error($e->getMessage(), ['exception' => $e::class, 'action' => \sprintf('%s::%s', self::class, __FUNCTION__)]);
            }

            return $this->redirectToRoute('statistic_new_overview_all');
        }

        return $this->render(
            'admin/new/statistic/new.html.twig',
            [
                'form' => $form->createView(),
                'errors' => $form->getErrors(),
                'update' => false,
            ]
        );
    }

    #[Route('/wahlkreise/{state}', name: 'admin_uu_new_area_by_state')]
    public function getAreaByState(string $state, Handler $handler): JsonResponse
    {
        $options = ['<option value="">---</option>'];
        foreach ($handler->getWahlkreiseByStateFormatted($state, $this->getUser()) as $key => $value) {
            $options[] = \sprintf('<option value="%s">%s</option>', $key, $value);
        }

        return $this->json($options);
    }

    #[Route('/data/{state}', name: 'admin_uu_new_statistic_data_state', methods: ['GET'])]
    public function getStatisticByState(string $state, \App\Statistic\Handler $handler): Response
    {
        return new JsonResponse($handler->findByState($state, $this->getUser()));
    }

    #[Route('/delete/{id}', name: 'admin_new_delete_statistic', methods: ['DELETE', 'GET'])]
    public function delete(string $id, WahlkreisNumberRepository $repository, NumbersService $numbersService): Response
    {
        $entry = $repository->find($id);
        if ($entry instanceof WahlkreisNumber) {
            try {
                $numbersService->delete($entry);
                $this->addFlash('success', 'Eintrag gelöscht');
            } catch (InvalidArgumentException $e) {
                $this->addFlash('error', 'Fehler beim Löschen des Eintrags');
                $this->logger->error($e->getMessage(), ['exception' => $e::class, 'action' => \sprintf('%s::%s', self::class, __FUNCTION__)]);
            }
        } else {
            $this->addFlash('error', 'Konnte Eintrag nicht löschen');
        }

        return $this->redirectToRoute('statistic_new_overview_all');
    }

    #[Route('/edit/{id}', name: 'admin_new_edit_statistic')]
    public function edit(WahlkreisNumber $wahlkreisNumber, Request $request, NumbersService $numbersService): Response
    {
        $form = $this->createForm(StatisticEditType::class, $wahlkreisNumber);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $numbersService->saveEntity($wahlkreisNumber, $this->getUser());
                $this->addFlash('success', 'Eintrag gespeichert.');
            } catch (InvalidArgumentException $e) {
                $this->addFlash('error', 'Fehler beim Speichern der Daten');
                $this->logger->error($e->getMessage(), ['exception' => $e::class, 'action' => \sprintf('%s::%s', self::class, __FUNCTION__)]);
            }

            return $this->redirectToRoute('statistic_new_overview_all');
        }

        return $this->render(
            'admin/new/statistic/index.html.twig',
            [
                'form' => $form->createView(),
                'errors' => $form->getErrors(),
                'disableWahlkreis' => true,
                'id' => $wahlkreisNumber->getId()?->toString(),
                'update' => true,
            ]
        );
    }
}
