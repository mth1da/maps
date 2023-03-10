<?php

namespace App\Controller\Admin;

use App\Entity\Ingredient;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class IngredientCrudController extends AbstractCrudController
{
    public const INGREDIENTS_BASE_PATH ='upload/images/ingredients';
    public const INGREDIENTS_UPLOAD_DIR ='public/upload/images/ingredients';
    public static function getEntityFqcn(): string
    {
        return Ingredient::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex(),
            TextField::new('name'),
            AssociationField::new('types'),
            TextEditorField::new('description'),
            MoneyField::new('price')
                ->setCurrency('EUR'),

            ImageField::new('photo')
                ->setBasePath(self::INGREDIENTS_BASE_PATH)
                ->setUploadDir(self::INGREDIENTS_UPLOAD_DIR),

            DateTimeField::new('created_at')->hideOnForm(),
            DateTimeField::new('updated_at')->hideOnForm(),
            DateTimeField::new('deleted_at')->hideOnForm(),

        ];
    }

    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if(!$entityInstance instanceof Ingredient) return;

        $entityInstance->setCreatedAt(new \DateTimeImmutable);


        parent::persistEntity($em, $entityInstance);

    }
    public function updateEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if(!$entityInstance instanceof Ingredient) return;

        $entityInstance->setUpdatedAt(new \DateTimeImmutable);


        parent::UpdateEntity($em, $entityInstance);
    }


}
