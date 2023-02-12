<?php

namespace App\DataFixtures;

use App\Entity\Sandwich;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
class SandwichFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $MAPS = new Sandwich();
        $MAPS->setName('MAPS');
        //$MAPS->addSandwichIngredient(Ingredient $ciabatta);
        //$MAPS->addSandwichIngredient(Ingredient $chorizo);
        //$MAPS->addSandwichIngredient(Ingredient $guacamole);
        //$MAPS->addSandwichIngredient(Ingredient $parmesan);
        $manager->persist($MAPS);

        $royal = new Sandwich();
        $royal->setName('Le royal');
        $manager->persist($royal);

        $manager->flush();
    }
}