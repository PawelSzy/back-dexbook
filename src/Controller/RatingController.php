<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\BookRating;
use App\Entity\User;
use App\Form\RatingType;
use App\Repository\BookRatingRepository;
use App\Traits\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Task;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/rating")
 */
class RatingController extends AbstractController
{
  use ControllerTrait;

  function __construct(SerializerInterface $serializer) {
    $this->serializer = $serializer;
  }

  /**
   * @Route("/add-new-rating", name="add_rating")
   */
  public function index(Request $request, BookRatingRepository $bookRatingRepository)
  {
    if ($request->query->get('format') == 'json') {
      $requestContent = $request->getContent();
      $rating = $this->serializer->deserialize($requestContent, BookRating::class, 'json');

      $requestContent = json_decode($requestContent);
      $book = $this->getDoctrine()
        ->getRepository(Book::class)
        ->find($requestContent->book_id);

      $user = $this->getDoctrine()
        ->getRepository(User::class)
        ->find($requestContent->user_id);
      $rating->setUser($user);
      $rating->setBook($book);

      $em = $this->getDoctrine()->getManager();

      try {
        $em->persist($rating);
        $em->flush();
      } catch (\Exception $e) {
        if ($e->getMessage() == 'Rating exist') {
          return new JsonResponse(['error' => 'Rating exist'], Response::HTTP_CONFLICT);
        } else {
          throw $e;
        }
      }

      return $this->_object_to_json_response($rating);
    }

    $bookRating = new BookRating();
    $form = $this->createForm(RatingType::class, $bookRating);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $bookRating = $form->getData();
      $bookRatingRepository->setRating($bookRating, $bookRating->getRating());
    }

    return $this->render('rating/index.html.twig', [
      'form' => $form->createView(),
      'controller_name' => 'RatingController',
    ]);
  }

  /**
   * @Route("/{id}", name="rating_show", methods={"GET"})
   */
  public function show(BookRating $rating, Request $request)
  {
    return $this->_object_to_json_response($rating);
  }
}
