<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('en');

        for($i = 1; $i < 5; $i++){
            $category = new Category();
            $category -> setName("field $i");
            $category -> setDescription("description $i");
 
            $manager -> persist($category);
 
            for($j = 1; $j <= 2; $j++){
                 $article = new Article();
                 $article -> setTitle("title $j");
                 $article -> setContent("The database i using has multiple tables the ones we need are called order_products , client , orders, the main goal is to create a table on the webpage route
                 can I create a table with columns of different length             
                 the column status does not show any values
                 I used the following code to get the values from different tables in the database ");
                 $article -> setCreatedDate($faker -> dateTimeBetween('-2 months'));
                 $article -> setImage("https://picsum.photos/id/237/200/300");
                 $article -> setCategory($category);
 
                 $manager -> persist($article);
 
                    $today = new DateTime();
                    $difference = $today -> diff($article -> getCreatedDate());
                    $days = $difference -> days;
                    $commentMax= '-'.$days.'days';

                 for ($k = 0; $k <= mt_rand(4,6); $k++){
                     $comment = new Comment();
                     $comment -> setDevelopper($faker -> name);
                     $comment -> setContent("test comment .................");
                     $comment -> setCreatedDate($faker -> dateTimeBetween($commentMax));
                     $comment -> setArticle($article);

                $manager -> persist($comment);

                 }
            }
        }
 
         $manager->flush();
     }



       
    }

