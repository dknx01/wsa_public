<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller\Admin\New;

use App\Entity\SupportNumbersDirektkandidat;
use App\Entity\SupportNumbersDirektkandidatKommunal;
use App\Entity\SupportNumbersDirektkandidatLandtag;
use App\Entity\SupportNumbersLandesliste;
use App\Entity\SupportNumbersLandeslisteKommunal;
use App\Entity\SupportNumbersLandeslisteLandtag;
use App\Form\Admin\StatisticEditType;
use App\Form\Admin\StatisticType;
use App\Repository\SupportNumbersRepository;
use App\Repository\UserRepository;
use App\Security\ActiveUserVoter;
use App\SupportNumbers\SupportNumbersService;
use App\SupportNumbers\Type;
use App\User\Roles;
use App\Wahlkreis\Handler;
use Doctrine\ORM\Exception\ORMException;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/new/statistic')]
#[IsGranted(Roles::ROLE_USER->name)]
class AdminStatisticController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    #[Route('/', name: 'statistic_new_overview', methods: ['GET'])]
    public function overview(SupportNumbersRepository $repository): Response
    {
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

        return $this->render('admin/new/statistic/overview.html.twig', ['states' => $allBundeslaender]);
    }

    #[Route(path: '/new', name: 'admin_new_statistic_index_new')]
    public function new(Request $request, SupportNumbersRepository $repository, UserRepository $userRepo, SupportNumbersService $supportNumbersService): Response
    {
        $this->denyAccessUnlessGranted(ActiveUserVoter::ACTIVE_USER);
        $dataClass = SupportNumbersLandesliste::class;
        if ('POST' === $request->getMethod()) {
            $dataClass = match ($request->request->all()['statistic']['type']) {
                Type::DK_KW->name => SupportNumbersDirektkandidatKommunal::class,
                Type::DK_LTW->name => SupportNumbersDirektkandidatLandtag::class,
                Type::LL_KW->name => SupportNumbersLandeslisteKommunal::class,
                Type::LL_LTW->name => SupportNumbersLandeslisteLandtag::class,
                Type::DK_BTW->name => SupportNumbersDirektkandidat::class,
                default => $dataClass,
            };
        }
        $form = $this->createForm(
            type: StatisticType::class,
            options: [
                'data_class' => $dataClass,
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SupportNumbersLandesliste|SupportNumbersDirektkandidat $supportNumber */
            $supportNumber = $form->getData();
            try {
                $supportNumbersService->save($supportNumber, $userRepo->find(1));
                $this->addFlash('success', 'Erfolgreich gespeichert.');
            } catch (ORMException|InvalidArgumentException $e) {
                $this->addFlash('error', 'Fehler beim Speichern der Daten');
                $this->logger->error($e->getMessage(), ['exception' => $e::class, 'action' => \sprintf('%s::%s', self::class, __FUNCTION__)]);
            }

            return $this->redirectToRoute('statistic_new_overview');
        }

        return $this->render(
            'admin/new/statistic/index.html.twig',
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
    public function delete(string $id, SupportNumbersRepository $repository, SupportNumbersService $supportNumbers): Response
    {
        $entry = $repository->find($id);
        if ($entry instanceof SupportNumbersLandesliste) {
            try {
                $supportNumbers->delete($entry);
                $this->addFlash('success', 'Eintrag gelöscht');
            } catch (InvalidArgumentException $e) {
                $this->addFlash('error', 'Fehler beim Löschen des Eintrags');
                $this->logger->error($e->getMessage(), ['exception' => $e::class, 'action' => \sprintf('%s::%s', self::class, __FUNCTION__)]);
            }
        } else {
            $this->addFlash('error', 'Konnte Eintrag nicht löschen');
        }

        return $this->redirectToRoute('statistic_new_overview');
    }

    #[Route('/edit/{id}', name: 'admin_new_edit_statistic')]
    public function edit(SupportNumbersLandesliste $supportNumber, Request $request, SupportNumbersService $supportNumbersService): Response
    {
        $form = $this->createForm(StatisticEditType::class, $supportNumber);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $supportNumbersService->save($supportNumber, $this->getUser());
                $this->addFlash('success', 'Eintrag gespeichert.');
            } catch (ORMException|InvalidArgumentException $e) {
                $this->addFlash('error', 'Fehler beim Speichern der Daten');
                $this->logger->error($e->getMessage(), ['exception' => $e::class, 'action' => \sprintf('%s::%s', self::class, __FUNCTION__)]);
            }

            return $this->redirectToRoute('statistic_new_overview');
        }

        return $this->render(
            'admin/new/statistic/index.html.twig',
            [
                'form' => $form->createView(),
                'errors' => $form->getErrors(),
                'disableWahlkreis' => true,
                'id' => $supportNumber->getId()?->toString(),
                'update' => true,
            ]
        );
    }
}
