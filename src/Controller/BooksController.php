<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class BooksController extends AbstractController
{
  function __construct(SerializerInterface $serializer)
  {
    $this->serializer = $serializer;
  }

  /**
   * @Route("/books", name="books")
   */
  public function index(BookRepository $books)
  {
    $response = new Response($books->getAllBooks('json'));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  /**
   * @Route("/books/{id}", name="book")
   */
  public function getBook(Book $book)
  {
    $json = $this->serializer->serialize(
      $book,
      'json', [
      'circular_reference_handler' => function ($object) {
        return $object->getId();
      }
    ]);

    $response = new Response($json);
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }
}
