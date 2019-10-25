<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuthorRepository")
 * @ApiResource(
 *  normalizationContext={"groups"={"author:read"}},
 *  denormalizationContext={"groups"={"author:write"}},
 * )
 */
class Author
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"author:read", "book:read", "book_rating:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"author:read", "author:write", "book:read", "book:write", "book_rating:read"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"author:read", "author:write", "book:read", "book:write", "book_rating:read"})
     */
    private $surname;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Book", inversedBy="authors")
     * @Groups({"author:read", "author:write", "book:write"})
     */
    private $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * @return Collection|book[]
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
        }

        return $this;
    }

    public function removeBook(book $book): self
    {
        if ($this->books->contains($book)) {
            $this->books->removeElement($book);
        }

        return $this;
    }

    public function __toString() {
      return $this->getFirstName() . ' ' . $this->getSurname();
    }
}
