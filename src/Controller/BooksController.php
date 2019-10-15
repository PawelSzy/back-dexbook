<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BooksController extends AbstractController
{
  /**
   * @Route("/books", name="books")
   */
  public function index(BookRepository $books)
  {
    $response = new Response($books->getAllBooks('json'));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }
}
