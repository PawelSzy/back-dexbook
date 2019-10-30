<?php

namespace App\EventSubscriber;

use App\Entity\BookRating;
use App\Repository\BookRatingRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SaveRatingSubscriber implements EventSubscriberInterface
{

  function __construct(BookRatingRepository $bookRatingRepository)
  {
    $this->bookRatingRepository = $bookRatingRepository;
  }

  public static function getSubscribedEvents()
  {
    return [
      KernelEvents::VIEW => ['saveRating',  EventPriorities::PRE_WRITE]
    ];
  }
  public function saveRating(GetResponseForControllerResultEvent $event)
  {
    $bookRating = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();

    if (!$bookRating instanceof BookRating || Request::METHOD_POST != $method) {
      return;
    }
//    $ratingReposotory = new BookRatingRepository;
    $bookId = $bookRating->getBook()->getId();
    $userId = $bookRating->getUser()->getId();

    $findedRating = $this->bookRatingRepository->findOneBy([
      'book' => $bookId,
      'user' => $bookRating->getUser()->getId(),
    ]);

    if ($findedRating)  {
      $bookRating->setId($findedRating->getId());
//      $findedRating->setRating((float) $bookRating->getRating());
//      $bookRating = $findedRating;
    }


//    $bookRating->setPassword(
//      $this->encoder->encodePassword($bookRating, $bookRating->getPassword())
//    );
  }
}
