<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller\Admin;

use App\Configuration\Configuration;
use App\Entity\Document;
use App\Entity\SupportNumbersLandesliste;
use App\Entity\User;
use App\Entity\Verbandsseiten;
use App\Entity\Wahlkreis;
use App\Security\ActiveUserVoter;
use App\User\Roles;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
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

#[AdminDashboard('/admin/verwaltung', 'verwaltung_dashboard')]
#[IsGranted(ActiveUserVoter::ACTIVE_USER)]
#[IsGranted(Roles::ROLE_ADMIN->name)]
class AdminDashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly Configuration $configuration,
    ) {
    }

    #[Route('/admin/verwaltung', name: 'data_admin')]
    public function index(): Response
    {
        return $this->render('@EasyAdmin/layout.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle($this->configuration->getPageTitle().' Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToUrl('Home', 'fa fa-home', $this->generateUrl('home', [], UrlGeneratorInterface::ABSOLUTE_URL));
        yield MenuItem::subMenu('Listen', 'fa fa-list-alt')
            ->setSubItems([
                MenuItem::linkToCrud('Dokumente', 'fa fa-list', Document::class),
                MenuItem::linkToCrud('UU-Zahlen', 'fa fa-list', SupportNumbersLandesliste::class),
                MenuItem::linkToCrud('Wahlkreise', 'fa fa-list', Wahlkreis::class),
                MenuItem::linkToCrud('Verbandsseiten', 'fa fa-link', Verbandsseiten::class),
                MenuItem::linkToCrud('User', 'fa fa-users', User::class),
            ]);
        yield MenuItem::subMenu('Hinzufügen', 'fa fa-plus-square')
            ->setSubItems([
                MenuItem::linkToUrl('UU-Zahlen erfassen', 'fa fa-plus-square', $this->generateUrl('admin_new_statistic_index_new', [], UrlGeneratorInterface::ABSOLUTE_URL)),
                MenuItem::linkToUrl('UU-Dokument Direktkandidat hinzufügen', 'fa fa-plus-square', $this->generateUrl('app_admin_documentadmin_direktkandidat', [], UrlGeneratorInterface::ABSOLUTE_URL)),
                MenuItem::linkToUrl('UU-Dokument Landesliste hinzufügen', 'fa fa-plus-square', $this->generateUrl('app_admin_documentadmin_landesliste', [], UrlGeneratorInterface::ABSOLUTE_URL)),
            ]);
        yield MenuItem::subMenu('Widgets', 'fas fa-pager')
            ->setSubItems([
                MenuItem::linkToUrl('Verbandslisten Widget', 'fa fa-link', $this->generateUrl('uu-liste', [], UrlGeneratorInterface::ABSOLUTE_URL)),
                MenuItem::linkToUrl('Statistic Widget', 'fa fa-link', $this->generateUrl('statistic_all_widget', [], UrlGeneratorInterface::ABSOLUTE_URL)),
            ]);
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
