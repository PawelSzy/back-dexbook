<?php

namespace App\Tests;

use App\Traits\BookAuthorTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReadedBookTest extends WebTestCase {
  use BookAuthorTestTrait;

  /**
   * @dataProvider userProvider
   */
  public function testWriteReadedBook($user, $book) {
    $client = self::createClient();
    $writtenUser = $this->writeData($user, $client, '/user/new');
    $writtenBook = $this->writeData($book, $client, '/book/new');

    $userId = $writtenUser->id;
    $bookId = $writtenBook->id;

    $client->xmlHttpRequest('POST', "/user/{$userId}/add-readed-book/{$bookId}");
    $this->assertResponseIsSuccessful();

    $client->xmlHttpRequest('GET', "/user/{$userId}?format=json");
    $readedUser= $this->getDataFromClient($client);
    $this->assertResponseIsSuccessful();
  }


  /**
   * @dataProvider userProvider
   */
  public function testWriteAndGetReadedBook($user, $book) {
    $client = self::createClient();
    $writtenUser = $this->writeData($user, $client, '/user/new');
    $writtenBook = $this->writeData($book, $client, '/book/new');

    $userId = $writtenUser->id;
    $bookId = $writtenBook->id;

    $client->xmlHttpRequest('POST', "/user/{$userId}/add-readed-book/{$bookId}");
    $this->assertResponseIsSuccessful();

    $client->xmlHttpRequest('GET', "/user/get-readed-books/{$userId}");
    $this->assertResponseIsSuccessful();
    $toReadBook= $this->getDataFromClient($client);
    $this->assertEquals($bookId, $toReadBook[0]->id);
  }

  /**
   * @dataProvider userProvider
   */
  public function testDeleteReadedBook($user, $book) {
    $client = self::createClient();
    $writtenUser = $this->writeData($user, $client, '/user/new');
    $writtenBook = $this->writeData($book, $client, '/book/new');

    $userId = $writtenUser->id;
    $bookId = $writtenBook->id;

    $client->xmlHttpRequest('POST', "/user/{$userId}/add-readed-book/{$bookId}");
    $client->xmlHttpRequest('DELETE', "/user/delete-readed-books/{$userId}/{$bookId}");

    $client->xmlHttpRequest('GET', "/user/get-readed-books/{$userId}");
    $readedBook= $this->getDataFromClient($client);

    $this->assertEquals([], $readedBook);
  }

  public function userProvider() {
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
