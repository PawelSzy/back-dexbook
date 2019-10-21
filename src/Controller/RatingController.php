<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\BookRating;
use App\Form\RatingType;
use App\Repository\BookRatingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Task;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;


class RatingController extends AbstractController
{
  /**
   * @Route("/add-new-rating", name="add_rating")
   */
  public function index(Request $request, BookRatingRepository $bookRatingRepository)
  {
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
}
