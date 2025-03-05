<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller\Admin;

use App\Entity\Document;
use App\Entity\Statistic;
use App\Entity\Verbandsseiten;
use App\Entity\Wahlkreis;
use App\Security\ActiveUserVoter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/verwaltung')]
#[IsGranted(ActiveUserVoter::ACTIVE_USER)]
class AdminDashboardController extends AbstractDashboardController
{
    #[Route('/', name: 'data_admin')]
    public function index(): Response
    {
        return $this->render('@EasyAdmin/layout.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Weltsozialamt Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Dokumente', 'fa fa-list', Document::class);
        yield MenuItem::linkToCrud('UU-Zahlen', 'fa fa-list', Statistic::class);
        yield MenuItem::linkToCrud('Wahlkreise', 'fa fa-list', Wahlkreis::class);
        yield MenuItem::linkToCrud('Verbandsseiten', 'fa fa-link', Verbandsseiten::class);
        yield MenuItem::linkToUrl('Home', 'fa fa-home', $this->generateUrl('home', [], UrlGeneratorInterface::ABSOLUTE_URL));
        yield MenuItem::linkToUrl('UU-Zahlen erfassen', 'fa fa-plus-square', $this->generateUrl('app_admin_statisticadmin_index', [], UrlGeneratorInterface::ABSOLUTE_URL));
        yield MenuItem::linkToUrl('UU-Dokument Direktkandidat hinzufügen', 'fa fa-plus-square', $this->generateUrl('app_admin_documentadmin_direktkandidat', [], UrlGeneratorInterface::ABSOLUTE_URL));
        yield MenuItem::linkToUrl('UU-Dokument Landesliste hinzufügen', 'fa fa-plus-square', $this->generateUrl('app_admin_documentadmin_landesliste', [], UrlGeneratorInterface::ABSOLUTE_URL));
    }

    public function configureActions(): Actions
    {
        return Actions::new()
            ->addBatchAction(Action::BATCH_DELETE)
            ->add(Crud::PAGE_INDEX, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DELETE)
            ->add(Crud::PAGE_DETAIL, Action::EDIT)
            ->add(Crud::PAGE_DETAIL, Action::INDEX)
            ->add(Crud::PAGE_DETAIL, Action::DELETE)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE);
    }
}
