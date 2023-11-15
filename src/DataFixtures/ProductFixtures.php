<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $product = new Product();
        $name = "Product ";
        $currentDate = date('Y-m-d H:i:s');
        for ($i = 0; $i <= 2; $i++) {
            $product->setName($name . $i);
            $product->setPrice(12);
            $product->setDescription("Lorem ipsum dolor sit amet consectetur adipisicing elit. Praesentium at dolorem quidem modi. Nam sequi consequatur obcaecati excepturi alias magni, accusamus");
            $product->setQuantity(5);
            $product->setOnline(true);
            $product->setImage("");
            $product->setCreatedAt($currentDate);
            $manager->persist($product);
            $manager->flush();
        }
        
        
    }
}
