<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/book")
 */
class BookController extends AbstractController
{

  function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
  {
    $this->serializer = $serializer;
    $this->validator = $validator;
  }
    /**
     * @Route("/", name="book_index", methods={"GET"})
     */
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('book/index.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="book_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
      if ($request->isMethod('post') && $request->query->get('format') == 'json') {
        $book = $this->serializer->deserialize($request->getContent(), Book::class, 'json');
        $em = $this->getDoctrine()->getManager();
        if (false) {
          $em->merge($existingBook);
          $book = $existingBook;
        }
        else {
          try {
            $em->persist($book);
            $em->flush();
          } catch (\Exception $e) {
            if ($e->getMessage() == 'Book exist') {
              return new JsonResponse(['error' => 'Book exist'], Response::HTTP_CONFLICT);
            } else {
              throw $e;
            }
          }
        }

        return $this->_book_to_json_response($book);
      }

      $book = new Book();
      $form = $this->createForm(BookType::class, $book);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($book);
        $entityManager->flush();

        return $this->redirectToRoute('book_index');
      }

      return $this->render('book/new.html.twig', [
        'book' => $book,
        'form' => $form->createView(),
      ]);
    }

    /**
     * @Route("/{id}", name="book_show", methods={"GET"})
     */
    public function show(Book $book, Request $request)
    {
      $format = $request->query->get('format', 'html');
      if ($format == 'html') {
        return $this->render('book/show.html.twig', [
          'book' => $book,
        ]);
      }

      return $this->_book_to_json_response($book);
    }

    /**
     * @Route("/{id}/edit", name="book_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Book $book): Response
    {
      if ($request->isMethod('post') && $request->query->get('format') == 'json') {
        $jsonBook = $this->serializer->deserialize(
          $request->getContent(),
          Book::class,
          'json',
          ['object_to_populate' => $book]
        );
        $em = $this->getDoctrine()->getManager();
        $em->persist($jsonBook);
        $em->flush();
        
        return $this->_book_to_json_response($jsonBook);
      }

      $form = $this->createForm(BookType::class, $book);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $this->getDoctrine()->getManager()->flush();

          return $this->redirectToRoute('book_index');
      }

      return $this->render('book/edit.html.twig', [
          'book' => $book,
          'form' => $form->createView(),
      ]);
    }

    /**
     * @Route("/{id}", name="book_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Book $book): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('book_index');
    }

    private function _book_to_json_response($book)
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
