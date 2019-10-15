<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Book;
use Symfony\Component\Serializer\SerializerInterface;

class BooksController extends AbstractController
{

  private function getBooks(SerializerInterface $serializer)
  {
    $repository = $this->getDoctrine()->getRepository(Book::class);

    $books = $repository->findAll();
//    var_dump($books);

    $json = $serializer->serialize(
      $books,
      'json', [
      'circular_reference_handler' => function ($object) {
        return $object->getId();
      }
    ]);

    return $json;

  }

  /**
   * @Route("/books", name="books")
   */
  public function index(SerializerInterface $serializer)
  {
    $response = new Response($this->getBooks($serializer));
    $response->headers->set('Content-Type', 'application/json');

    return $response;
    return $this->getBooks($serializer);

  }
}
