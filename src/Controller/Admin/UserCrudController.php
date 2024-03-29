<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('first_name'),
            TextField::new('last_name'),
            TextField::new('user_name'),
            TextField::new('email'),
            ChoiceField::new('roles')->setChoices([
                'ROLE_ADMIN' => 'ROLE_ADMIN',
                // le dernier role de cette liste sera selectionnée par default
                'ROLE_USER' => 'ROLE_USER',
            ])
                ->allowMultipleChoices(),
            DateField::new('birth_date'),
            DateTimeField::new('created_at')->hideOnForm(),
            DateTimeField::new('updated_at')->hideOnForm(),

        ];
    }

    public function updateEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if(!$entityInstance instanceof User) return;

        $entityInstance->setUpdatedAt(new \DateTimeImmutable);

        parent::UpdateEntity($em, $entityInstance);
    }

}
