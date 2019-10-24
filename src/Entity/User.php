<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ApiResource()
 */
class User implements UserInterface, \Serializable
{
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\Column(type="string", length=25, unique=true)
   */
  private $username;

  /**
   * @ORM\Column(type="string", length=32)
   */
  private $salt;

  /**
   * @ORM\Column(type="string", length=40)
   */
  private $password;

  /**
   * @ORM\Column(type="string", length=60, unique=true)
   */
  private $email;

  /**
   * @ORM\Column(name="is_active", type="boolean")
   */
  private $isActive;

  /**
   * @ORM\ManyToMany(targetEntity="App\Entity\Book", inversedBy="userWantsToRead")
   */
  private $toRead;

  /**
   * @ORM\ManyToMany(targetEntity="App\Entity\Book", inversedBy="usersWhoReaded")
   * @ORM\JoinTable(name="users_phonenumbers")
   */
  private $readed;

  /**
   * @ORM\OneToMany(targetEntity="App\Entity\BookRating", mappedBy="user", orphanRemoval=true)
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

  /**
   * @inheritDoc
   */
  public function getUsername()
  {
    return $this->username;
  }

  /**
   * @inheritDoc
   */
  public function getSalt()
  {
    return $this->salt;
  }

  /**
   * @inheritDoc
   */
  public function getPassword()
  {
    return $this->password;
  }

  /**
   * @inheritDoc
   */
  public function getRoles()
  {
    return array('ROLE_USER');
  }

  /**
   * @inheritDoc
   */
  public function eraseCredentials()
  {
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

  public function __toString() {
    return $this->getUsername();
  }

  public function getId(): ?int
  {
      return $this->id;
  }

  public function setUsername(string $username): self
  {
      $this->username = $username;

      return $this;
  }

  public function setSalt(string $salt): self
  {
      $this->salt = $salt;

      return $this;
  }

  public function setPassword(string $password): self
  {
      $this->password = $password;

      return $this;
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

  public function getIsActive(): ?bool
  {
      return $this->isActive;
  }

  public function setIsActive(bool $isActive): self
  {
      $this->isActive = $isActive;

      return $this;
  }
}