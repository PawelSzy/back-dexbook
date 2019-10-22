<?php

namespace App\Tests;

use App\Entity\Book;
use App\Entity\BookRating;
use App\Entity\User;
use App\Traits\BookAuthorTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RatingTest extends WebTestCase
{
  use BookAuthorTestTrait;

  private $client;
  private $userId;
  private $bookId;

  private $book;
  private $user;
  private $rating;


  public function setUp()
  {
    $this->client = self::createClient();

    [$user, $book] = $this->bookAuthorProvider()->current();
    $this->user = $this->writeData($user, $this->client, '/user/new');
    $this->book = $this->writeData($book, $this->client, '/book/new');

    $this->userId = $this->user->id;
    $this->bookId = $this->book->id;
    $this->bookRating = 4;

    $this->rating = [
      'book_id' => $this->bookId,
      'user_id' => $this->userId,
      'rating' => $this->bookRating,
    ];
  }

  /**
   * @dataProvider bookAuthorProvider
   */
  public function testNewRating($user, $book) {
    $writtenRating = $this->writeData($this->rating, $this->client, '/rating/add-new-rating');
    $this->assertResponseIsSuccessful();
    $this->assertNotNull($writtenRating->id);
    $this->assertEquals($writtenRating->rating, $this->bookRating);
  }

  /**
   * @dataProvider bookAuthorProvider
   */
  public function testReadRating($user, $book) {
    $writtenRating = $this->writeData($this->rating, $this->client, '/rating/add-new-rating');
    $ratingId = $writtenRating->id;
    $this->client->xmlHttpRequest('GET', "/rating/{$ratingId}?format=json");
    $readedRating= $this->getDataFromClient($this->client);
    $this->assertResponseIsSuccessful();

    $this->assertNotNull($readedRating->id);
    $this->assertEquals($readedRating->rating, $this->bookRating);
  }

  public function  testChangeRating() {
    $writtenRating = $this->writeData($this->rating, $this->client, '/rating/add-new-rating');
    $ratingId = $writtenRating->id;

    $writtenRating->rating = 3;

    $this->client->xmlHttpRequest('GET', "/rating/{$ratingId}?format=json");
    $readedRating= $this->getDataFromClient($this->client);
    $this->assertResponseIsSuccessful();

    $this->assertNotNull($readedRating->id);
    $this->assertEquals($readedRating->rating, $this->bookRating);
  }

  public function bookAuthorProvider() {
    yield [
      [
        'username' => 'test_username',
        'password' => '123test',
        'email' => 'test@test.com',
        'active' => 'true',
      ],
      [
        "authors" => [["firstName" => "Stanislaw", "surname" => "Lem"]],
        "title" => "Eden",
        "price" => 10
      ],
    ];
  }
}
