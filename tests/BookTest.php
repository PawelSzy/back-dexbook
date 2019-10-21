<?php

namespace App\Tests;

use App\Traits\BookAuthorTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookTest extends WebTestCase
{
  use BookAuthorTestTrait;

  /**
   * @dataProvider bookProvider
   */
  public function testCreateBook($book)
  {
    $client = self::createClient();
    $title = $book['title'];
    $writtenBook = $this->writeData($book, $client, '/book/new');
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
    $writtenBook = $this->writeData($book, $client, '/book/new');

    $id = $writtenBook->id;
    $client->xmlHttpRequest('GET', "/book/{$id}?format=json");
    $readedBook = $this->getDataFromClient($client);
    $this->assertResponseIsSuccessful();
    $this->assertNotNull($readedBook->id);
    $this->assertEquals($readedBook->title, $title);
  }

  /**
   * @dataProvider bookProvider
   */
  public function testBookChange($book) {
    $client = self::createClient();
    $writtenBook = $this->writeData($book, $client, '/book/new');

    $id = $writtenBook->id;
    $book['title'] = 'test_title';
    $book['id'] = $id;

    $client->xmlHttpRequest('POST', "/book/{$id}/edit?format=json",
      [],
      [], [],
      json_encode($book));
    $changedBook = $this->getDataFromClient($client);
    $this->assertResponseIsSuccessful();
    $this->assertEquals($changedBook->title, $book['title']);
    $this->assertEquals($changedBook->id, $book['id']);
  }

  /**
   * @dataProvider bookProvider
   */
  public function testDeleteBook($book) {
    $client = self::createClient();
    $writtenBook = $this->writeData($book, $client, '/book/new');
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
}
