<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ApiFilter(
 *  SearchFilter::class, properties={"username": "exact", "email": "exact" }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ApiResource(
 *  collectionOperations={
 *  "post",
 *  "get"
 * },
 *  itemOperations={
 *     "get"={"access_control"="is_granted('USER_AUTHENTICATED')"},
 *     "delete"={"access_control"="is_granted('USER_AUTHENTICATED')"},
 *     "put"={"access_control"="is_granted('USER_AUTHENTICATED')"},
 *     "patch"
 *  },
 *  normalizationContext={"groups"={"user:read", "book_rating:read"}},
 *  denormalizationContext={"groups"={"user:write"}},
 * )
 * @UniqueEntity(fields={"username"})
 * @UniqueEntity(fields={"email"})
 */
class User implements UserInterface
{
  /**
   * @ORM\Id()
   * @ORM\GeneratedValue(strategy="AUTO")
   * @ORM\Column(type="integer")
   * @Groups({"book_rating:read", "user:read", "book:read"})
   */
  private $id;

  /**
   * @ORM\Column(type="string", length=25, unique=true)
   * @Groups({"book_rating:read", "user:read", "user:write"})
   * @Assert\NotBlank
   */
  private $username;

  /**
   * @ORM\Column(type="string", length=180, unique=true)
   * @Assert\NotBlank
   * @Groups({"user:read", "user:write"})
   * @Assert\Email(
   *     message = "The email '{{ value }}' is not a valid email.",
   *     checkMX = true
   * )
   */
  private $email;

  /**
   * @ORM\Column(type="json")
   */
  private $roles = [];

  /**
   * @var string The hashed password
   * @ORM\Column(type="string")
   * @Groups({"user:write"})
   * @Assert\NotBlank
   */
  private $password;

  /**
   * @ORM\Column(name="is_active", type="boolean")
   */
  private $isActive;

  /**
   * @ORM\ManyToMany(targetEntity="App\Entity\Book", inversedBy="userWantsToRead")
   * @ApiSubresource()
   */
  private $toRead;

  /**
   * @ORM\ManyToMany(targetEntity="App\Entity\Book", inversedBy="usersWhoReaded")
   * @ORM\JoinTable(name="users_phonenumbers")
   * @ApiSubresource()
   */
  private $readed;

  /**
   * @ORM\OneToMany(targetEntity="App\Entity\BookRating", mappedBy="user", orphanRemoval=true)
   * @ApiSubresource()
   */
  private $bookRatings;

  public function __construct()
  {
    $this->isActive = true;
    $this->salt = md5(uniqid(null, true));
    $this->toRead = new ArrayCollection();
    $this->readed = new ArrayCollection();
    $this->bookRatings = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getEmail(): ?string
  {
    return $this->email;
  }

    public function setEmail(string $email): self
    {
      $this->email = $email;

      return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername()
    {
      return $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
      $roles = $this->roles;
      // guarantee every user at least has ROLE_USER
      $roles[] = 'ROLE_USER';

      return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
      $this->roles = $roles;

      return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
      return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
      $this->password = $password;

      return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
      // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
    // If you store any temporary, sensitive data on the user, clear it here
    // $this->plainPassword = null;
  }

 /**
  * @inheritDoc
  */
  public function equals(UserInterface $user)
  {
    return $this->id === $user->getId();
  }

  /**
   * @see \Serializable::serialize()
   */
  public function serialize()
  {
    return serialize(array(
      $this->id,
    ));
  }

  /**
   * @see \Serializable::unserialize()
   */
  public function unserialize($serialized)
  {
    list (
      $this->id,
      ) = unserialize($serialized);
  }

  /**
   * @return Collection|book[]
   */
  public function getToRead(): Collection
  {
    return $this->toRead;
  }

  public function addToRead(book $toRead): self
  {
    if (!$this->toRead->contains($toRead)) {
      $this->toRead[] = $toRead;
    }

    return $this;
  }

  public function removeToRead(book $toRead): self
  {
    if ($this->toRead->contains($toRead)) {
      $this->toRead->removeElement($toRead);
    }

    return $this;
  }

  /**
   * @return Collection|book[]
   */
  public function getReaded(): Collection
  {
    return $this->readed;
  }

  public function addReaded(book $readed): self
  {
    if (!$this->readed->contains($readed)) {
      $this->readed[] = $readed;
    }

    return $this;
  }

  public function removeReaded(book $readed): self
  {
    if ($this->readed->contains($readed)) {
      $this->readed->removeElement($readed);
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
      $bookRating->setUser($this);
    }

    return $this;
  }

  public function removeBookRating(BookRating $bookRating): self
  {
    if ($this->bookRatings->contains($bookRating)) {
      $this->bookRatings->removeElement($bookRating);
      // set the owning side to null (unless already changed)
      if ($bookRating->getUser() === $this) {
        $bookRating->setUser(null);
      }
    }

    return $this;
  }

  public function __toString()
  {
    return $this->getUsername();
  }

  public function getIsActive(): ?bool
  {
    return $this->isActive;
  }

  public function setIsActive(bool $isActive): self
  {
    $this->isActive = $isActive;

    return $this;
  }

  public function setUsername(string $username): self
  {
    $this->username = $username;

    return $this;
  }
}