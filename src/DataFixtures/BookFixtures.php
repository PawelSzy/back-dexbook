<?php

// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class BookFixtures extends Fixture
{
  public function load(ObjectManager $manager)
  {

    $books = [
     ['title'=> "Sandman", 'author'=> "Neil", 'surname' => "Gaiman", 'id'=> 1, 'price'=> 5, 'people'=> 1000, 'score'=> 5000, 'rating'=> 5, 'image'=> "https=>//vignette.wikia.nocookie.net/marvel_dc/'image's/c/c7/Sandman_3.jpg/revision/latest?cb=20080121190422" ],
     ['title'=> "Dune", 'author'=> "Frank", 'surname' => "Herbert", 'id'=> 2, 'price'=> 6, 'people'=> 1600, 'score'=> 8000, 'rating'=> 5, 'image'=> "https=>//res.cloudinary.com/teepublic/'image'/private/s--CL7ChMYM--/t_Preview/b_rgb=>262c3a,c_limit,f_jpg,h_630,q_90,w_630/v1556626445/production/designs/4742645_0.jpg" ],
     ['title'=> "Dracula", 'author'=> "Bram", 'surname' => "Stocker", 'id'=> 3, 'price'=> 1, 'people'=> 250, 'score'=> 1000, 'rating'=> 4, 'image'=> "https=>//'image's-na.ssl-'image's-amazon.com/'image's/I/91cKI7ntI7L._SY445_.jpg" ],
     ['title'=> "1984", 'author'=> "Goerge", 'surname' => "Orwell", 'id'=> 4, 'price'=> 7, 'people'=> 2000, 'score'=> 8000, 'rating'=> 4, 'image'=> "https://i.gr-assets.com/images/S/compressed.photo.goodreads.com/books/1532714506l/40961427._SX318_.jpg"],
     ['title'=> "Heart of Darkness", 'author'=> "Joseph", 'surname' => "Conrad", 'id'=> 5, 'price'=> 1, 'people'=> 250, 'score'=> 1000, 'rating'=> 4, 'image'=> "https=>//kb'image's1-a.akamaihd.net/9b7ea1a5-ff91-4002-be6c-9ee4c82c19fd/353/569/90/False/heart-of-darkness-262.jpg" ],
     ['title'=> "The Man in the High Castle", 'author'=> "Philip K.", 'surname' => "Dick", 'id'=> 6, 'price'=> 7, 'people'=> 1000, 'score'=> 4000, 'rating'=> 4, 'image'=> "https=>//'image's-na.ssl-'image's-amazon.com/'image's/I/51Ky9l4DYEL._AC_UL320_SR214,320_.jpg" ],
    ];

    foreach ($books as $bookData) {
      /** @var Book */
      $book = new Book();
      /** @var Author */
      $author = new Author();
      $author->setFirstName($bookData['author']);
      $author->setSurname($bookData['surname']);
      $manager->persist($author);
      $book->addAuthor($author);
      $book->setTitle($bookData['title']);
      $book->setPrice($bookData['price']);
      $book->setPeople($bookData['people']);
      $book->setScore($bookData['score']);
      $book->setRating($bookData['rating']);
      $book->setImage($bookData['image']);
      $manager->persist($book);
    }

    $manager->flush();
  }
}