<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 *
 *
 * @ApiFilter(
 *  SearchFilter::class, properties={"book": "exact", "user": "exact" }
 * )
 * @ORM\Entity(
 *  repositoryClass="App\Repository\BookRatingRepository"
 * )
 * @ApiResource(
 *  normalizationContext={"groups"={"book_rating:read"}},
 *  denormalizationContext={"groups"={"book_rating:write"}},
 * )
 *
 */
class BookRating
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"book_rating:read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Book", inversedBy="bookRatings")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"book_rating:read", "book_rating:write"})
     */
    private $book;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookRatings")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"book_rating:read", "book_rating:write"})
     */
    private $user;

    /**
     * @ORM\Column(type="smallint")
     * @Groups({"book_rating:read", "book_rating:write"})
     */
    private $rating;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
      $this->id = $id;
    }

    public function getBook(): ?book
    {
        return $this->book;
    }

    public function setBook(?book $book): self
    {
        $this->book = $book;
        if(($user = $this->getUser()) && $this->getRating() != null) {
          $book->addUsersWhoReaded($user);
        }

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): self
    {
        $this->user = $user;
        if(($book = $this->getBook()) && $this->getRating() != null) {
          $book->addUsersWhoReaded($user);
        }

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(float $rating): self
    {
        $this->rating = $rating;
        if(($book = $this->getBook()) && ($user = $this->getUser())) {
          $book->addUsersWhoReaded($user);
        }

        return $this;
    }

    public function __toString() {
      return (string) $this->getBook() . " " .  $this->getRating();
    }
}
