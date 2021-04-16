<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Article;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        for ($i=1; $i <= 100 ; $i++) { 
        	$article = new Article();
        	$article->setTitre("Titre de l'article n°$i")
        			->setContenu("Lorem ipsum dolor sit amet, consectetur adipisicing elit. Repudiandae eum et vel inventore harum omnis minus. Minima neque perspiciatis, doloremque quia obcaecati harum, quae ut ipsa illo ad autem facilis beatae doloribus delectus veniam. Alias quibusdam praesentium saepe nisi repellat.
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci ab magnam dolor excepturi quidem blanditiis, consectetur reiciendis similique, voluptates incidunt odio accusantium quas.
                        Officia sed, aliquid provident sapiente odio voluptate praesentium neque nobis, culpa animi consequatur ducimus atque repudiandae incidunt aperiam sint! Cum tempora hic velit.
                        Dolorum pariatur facere vel consequuntur dignissimos a mollitia sint porro sequi, sed possimus temporibus eum. Ipsum facere quis eius harum, voluptates odit iusto.
                        Mollitia, tempore, odio. Itaque sunt ducimus earum nostrum dolorum ratione saepe pariatur ipsum, in ad atque id, corporis, cum ea, omnis hic amet?
                        Provident, iure quia nam minus praesentium velit hic placeat soluta ab recusandae, temporibus at aspernatur nisi magnam doloremque. Autem tempore inventore, unde architecto!")
        			->setDate(new \DateTime());

        			$manager->persist($article);
        }

        $manager->flush();
    }
}
