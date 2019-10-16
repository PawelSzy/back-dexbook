<?php

namespace App\Repository;

use App\Entity\BookRating;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BookRating|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookRating|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookRating[]    findAll()
 * @method BookRating[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRatingRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, BookRating::class);
  }

  public function setRating(BookRating $bookRating, int $rating)
  {
    $em = $this->getEntityManager();
    $bookRatingExist = $this->findBy([
      'book' => $bookRating->getBook(),
      'user' => $bookRating->getUser()
    ]);
    if ($bookRatingExist) {
      /** @var App\Entity\BookRating */
      $bookRating = $bookRatingExist[0];
      $book = $bookRating->getBook();
      $book->setScore($book->getScore() - $bookRating->getRating() + $rating);
      $bookRating->setRating($rating);
    }
    else {
      /** @var App\Entity\Book $book*/
      $book = $bookRating->getBook();
      $book->setPeople($book->getPeople() + 1);
      $book->setScore($book->getScore() + $rating);
    }

    $rating = (float) $book->getScore() / $book->getPeople();
    $book->setRating($rating);
    $bookRating->setBook($book);
    $em->persist($book);
    $em->persist($bookRating);
    $em->flush();
  }

  // /**
  //  * @return BookRating[] Returns an array of BookRating objects
  //  */
  /*
  public function findByExampleField($value)
  {
      return $this->createQueryBuilder('b')
          ->andWhere('b.exampleField = :val')
          ->setParameter('val', $value)
          ->orderBy('b.id', 'ASC')
          ->setMaxResults(10)
          ->getQuery()
          ->getResult()
      ;
  }
  */

  /*
  public function findOneBySomeField($value): ?BookRating
  {
      return $this->createQueryBuilder('b')
          ->andWhere('b.exampleField = :val')
          ->setParameter('val', $value)
          ->getQuery()
          ->getOneOrNullResult()
      ;
  }
  */
}
