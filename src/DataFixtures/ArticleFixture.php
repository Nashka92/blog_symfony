<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class ArticleFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = \Faker\Factory::create('fr_FR');

        //créer 3 catégories faké

        for ($i=1; $i <=3 ; $i++) { 
            $category = new Category();
            $category->setTitle($faker->sentence())
                     ->setDescription($faker->paragraph(3));
            

            $manager->persist($category);

            //créer entre 4 et 6 articles
            for ($j=1; $j <=mt_rand(4, 6) ; $j++) { 
                $article = new Article();

                $content = '<p>'.join('</p><p>', $faker->paragraphs(5)).'</p>';
            
                $article->setTitle($faker->sentence())
                        ->setContent($content)
                        ->setImage($faker->imageUrl())
                        ->setCreateAt($faker->dateTimeBetween('- 6 months'))
                        ->setCategory($category);

                
                $manager->persist($article);     

                //On donne des commentaires à l'article
                for ($k=0; $k <mt_rand(4, 10) ; $k++) { 
                    $comment= new Comment();
                    $content = '<p>'.join('</p><p>', $faker->paragraphs(2)).'</p>';

                    $now = new \DateTime();
                    $interval = $now->diff($article->getCreateAt());
                    $days = $interval->days;
                    $minimum = '-'.$days.'days'; // -100 days

                    $comment->setAuthor($faker->name)
                            ->setContent($content)
                            ->setCreatedAt($faker->dateTimeBetween($minimum))
                            ->setArticle($article);

                    //on demande a la doctrine de persister
                    $manager->persist($comment);
                            

                }
            }
        }

        //on fait un flush a la fin pour qu'il balance tout et optimise tout
        $manager->flush();
    }
}
