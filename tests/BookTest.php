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
    $client->xmlHttpRequest('POST', '/book/new?XDEBUG_SESSION_START=PHPSTORM&format=json',
      [],
      [], [],
      json_encode($book));
    $writtenBook = $this->getBookFromClient($client);

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
    $client->xmlHttpRequest('POST', '/book/new?XDEBUG_SESSION_START=PHPSTORM&format=json',
      [],
      [], [],
      json_encode($book));
    $writtenBook = $this->getBookFromClient($client);

    $id = $writtenBook->id;
    $client->xmlHttpRequest('GET', "/book/{$id}?format=json");
    $readedBook = $this->getBookFromClient($client);

    $this->assertNotNull($readedBook->id);
    $this->assertEquals($readedBook->title, $title);
    $this->assertResponseIsSuccessful();
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
}
