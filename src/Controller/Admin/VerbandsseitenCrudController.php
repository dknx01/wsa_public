<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller\Admin;

use App\Entity\Verbandsseiten;
use App\Security\ActiveUserVoter;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(ActiveUserVoter::ACTIVE_USER)]
class VerbandsseitenCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Verbandsseiten::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            TextField::new('link'),
            TextField::new('link_name'),
        ];
    }
}
