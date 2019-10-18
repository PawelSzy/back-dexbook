<?php

namespace App\Repository;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, SerializerInterface $serializer)
    {
        parent::__construct($registry, Book::class);
        $this->serializer = $serializer;
    }

    public function getAllBooks(string $format = 'html') {
      if ($format == 'json') {
        $books = $this->findAll();
        $json = $this->serializer->serialize(
          $books,
          'json', [
          'circular_reference_handler' => function ($object) {
            return $object->getId();
          }
        ]);
        return $json;
      }

      return $this->findAll();
    }

    public function checkIfAuthorExistAndReplaceWithExisting(Book $book) {
      $authors = $book->getAuthors();
      $em = $this->getEntityManager();
      $repo = $em->getRepository(Author::class);
      foreach($authors->getIterator() as $i => $author) {
        $existingAuthor = $repo->findOneBy([
          'firstName' => $author->getFirstName(),
          'surname' => $author->getSurname(),
        ]);
        if($existingAuthor) {
            $book->removeAuthor($author);
            $book->addAuthor($existingAuthor);
        }
      }
      return $book;
    }

    // /**
    //  * @return Book[] Returns an array of Book objects
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
    public function findOneBySomeField($value): ?Book
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
