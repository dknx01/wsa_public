<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller\Admin;

use App\Entity\Verbandsseiten;
use App\Security\ActiveUserVoter;
use App\User\Roles;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(ActiveUserVoter::ACTIVE_USER)]
#[IsGranted(Roles::ROLE_ADMIN->name)]
class VerbandsseitenCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Verbandsseiten::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name');
        yield TextField::new('link');
        yield TextField::new('link_name');
    }
}
