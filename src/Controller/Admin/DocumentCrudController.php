<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller\Admin;

use App\Entity\Document;
use App\Security\ActiveUserVoter;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('//admin/verwaltung/')]
#[IsGranted(ActiveUserVoter::ACTIVE_USER)]
class DocumentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Document::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('state'),
            TextField::new('type')->setDisabled(),
            AssociationField::new('wahlkreis')->renderAsHtml(),
            TextField::new('wkName')->setDisabled(),
            IntegerField::new('wkNr')->setDisabled(),
            TextField::new('name'),
            TextEditorField::new('description'),
        ];
    }
}
