<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ApiResource(
 *  normalizationContext={"groups"={"book:read"}},
 *  denormalizationContext={"groups"={"book:write"}},
 * )
 */
class Book
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"book:read", "book_rating:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"book:read", "book:write", "author:read", "book_rating:read"})
     */
    private $title;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"book:read", "book:write"})
     */
    private $price;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $people;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"book:read", "book:write"})
     */
    private $score;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"book:read", "book:write"})
     */
    private $rating;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"book:read", "book:write"})
     */
    private $image;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Author", mappedBy="books", cascade={"persist"})
     * @ORM\JoinTable(name="authors")
     * @Groups({"book:read", "book:write"})
     */
    private $authors;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="toRead")
     * @Groups({"book:read", "book:write"})
     */
    private $userWantsToRead;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="readed")
     * @Groups({"book:read", "book:write"})
     */
    private $usersWhoReaded;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BookRating", mappedBy="book", orphanRemoval=true)
     * @Groups({"book:read", "book:write"})
     */
    private $bookRatings;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->userWantsToRead = new ArrayCollection();
        $this->usersWhoReaded = new ArrayCollection();
        $this->bookRatings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPeople(): ?int
    {
        return $this->people;
    }

    public function setPeople(?int $people): self
    {
        $this->people = $people;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|Author[]
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Author $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
            $author->addBook($this);
        }

        return $this;
    }

    public function removeAuthor(Author $author): self
    {
        if ($this->authors->contains($author)) {
            $this->authors->removeElement($author);
            $author->removeBook($this);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUserWantsToRead(): Collection
    {
        return $this->userWantsToRead;
    }

    public function addUserWantsToRead(User $userWantsToRead): self
    {
        if (!$this->userWantsToRead->contains($userWantsToRead)) {
            $this->userWantsToRead[] = $userWantsToRead;
            $userWantsToRead->addToRead($this);
        }

        return $this;
    }

    public function removeUserWantsToRead(User $userWantsToRead): self
    {
        if ($this->userWantsToRead->contains($userWantsToRead)) {
            $this->userWantsToRead->removeElement($userWantsToRead);
            $userWantsToRead->removeToRead($this);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsersWhoReaded(): Collection
    {
        return $this->usersWhoReaded;
    }

    public function addUsersWhoReaded(User $usersWhoReaded): self
    {
        if (!$this->usersWhoReaded->contains($usersWhoReaded)) {
            $this->usersWhoReaded[] = $usersWhoReaded;
            $usersWhoReaded->addReaded($this);
        }

        return $this;
    }

    public function removeUsersWhoReaded(User $usersWhoReaded): self
    {
        if ($this->usersWhoReaded->contains($usersWhoReaded)) {
            $this->usersWhoReaded->removeElement($usersWhoReaded);
            $usersWhoReaded->removeReaded($this);
        }

        return $this;
    }

    /**
     * @return Collection|BookRating[]
     */
    public function getBookRatings(): Collection
    {
        return $this->bookRatings;
    }

    public function addBookRating(BookRating $bookRating): self
    {
        if (!$this->bookRatings->contains($bookRating)) {
            $this->bookRatings[] = $bookRating;
            $bookRating->setBook($this);
        }

        return $this;
    }

    public function removeBookRating(BookRating $bookRating): self
    {
        if ($this->bookRatings->contains($bookRating)) {
            $this->bookRatings->removeElement($bookRating);
            // set the owning side to null (unless already changed)
            if ($bookRating->getBook() === $this) {
                $bookRating->setBook(null);
            }
        }

        return $this;
    }

    public function __toString() {
      return $this->getTitle();
  }

  /**
   * @ORM\PrePersist
   */
  public function checkIfAuthorExistAndReplaceWithExisting(LifecycleEventArgs $args)
  {
    $em = $args->getEntityManager();
    $authors = $this->getAuthors();
    $repo = $em->getRepository(Author::class);
    foreach ($authors->getIterator() as $i => $author) {
      $existingAuthor = $repo->findOneBy([
        'firstName' => $author->getFirstName(),
        'surname' => $author->getSurname(),
      ]);
      if ($existingAuthor) {
        $this->removeAuthor($author);
        $this->addAuthor($existingAuthor);
      }
    }
  }

  /**
   * @ORM\PrePersist
   *
   * Check if Book is in database
   * If is in: return book existing in db
   */
  public function checkIfBookExist(LifecycleEventArgs $args)
  {
    $em = $args->getEntityManager();
    $repo = $em->getRepository(Book::class);

    $authors = $this->getAuthors();
    $finded = $repo->findBooksWithTitleAndAuthors($this, $authors);
    if (count($finded) != 0) {
      throw new \Exception('Book exist');
    }
  }
}
