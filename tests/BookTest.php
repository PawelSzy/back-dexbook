<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookTest extends WebTestCase
{
  /**
   * @dataProvider bookProvider
   */
  public function testCreateReadBook($book)
  {
    $client = self::createClient();
    $title = $book['title'];
    $writtenBook = $this->writeNewBook($book, $client);
    $this->assertResponseIsSuccessful();
    $this->assertNotNull($writtenBook->id);
    $this->assertEquals($writtenBook->title, $title);
  }

  /**
   * @dataProvider bookProvider
   */
  public function testCreateAndReadBook($book) {
    $client = self::createClient();
    $title = $book['title'];
    $writtenBook = $this->writeNewBook($book, $client);

    $id = $writtenBook->id;
    $client->xmlHttpRequest('GET', "/book/{$id}?format=json");
    $readedBook = $this->getBookFromClient($client);
    $this->assertResponseIsSuccessful();
    $this->assertNotNull($readedBook->id);
    $this->assertEquals($readedBook->title, $title);
  }

  /**
   * @dataProvider bookProvider
   */
  public function testBookChange($book) {
    $client = self::createClient();
    $writtenBook = $this->writeNewBook($book, $client);

    $id = $writtenBook->id;
    $book['title'] = 'test_title';
    $book['id'] = $id;

    $client->xmlHttpRequest('POST', "/book/{$id}/edit?format=json",
      [],
      [], [],
      json_encode($book));
    $changedBook = $this->getBookFromClient($client);
    $this->assertResponseIsSuccessful();
    $this->assertEquals($changedBook->title, $book['title']);
    $this->assertEquals($changedBook->id, $book['id']);
  }

  /**
   * @dataProvider bookProvider
   */
  public function testDeleteBook($book) {
    $client = self::createClient();
    $writtenBook = $this->writeNewBook($book, $client);
    $id = $writtenBook->id;
    $client->xmlHttpRequest('DELETE', "/book/{$id}?format=json");
    $client->xmlHttpRequest('GET', "/book/{$id}?format=json");
    $this->assertEquals(404, $client->getResponse()->getStatusCode());
  }

  public function bookProvider() {
    yield [[
      "authors" => [["firstName" => "Stanislaw", "surname" => "Lem"]],
      "title" => "Eden",
      "price" => 10
    ]];
  }

  public function getBookFromClient($client)
  {
    $response = $client->getResponse();
    $readedBook = json_decode($response->getContent());
    return $readedBook;
  }

  public function writeNewBook($book, $client)
  {
    $client->xmlHttpRequest('POST', '/book/new?XDEBUG_SESSION_START=PHPSTORM&format=json',
      [],
      [], [],
      json_encode($book));
    $writtenBook = $this->getBookFromClient($client);
    return $writtenBook;
  }
}
