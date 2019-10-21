<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthorTest extends WebTestCase
{

  /**
   * @dataProvider authorProvider
   */
  public function testCreateAuthor($author)
  {
    $client = self::createClient();

    $firstName = $author['firstName'];
    $writtenAuthor = $this->writeNewAuthor($author, $client);
    $this->assertResponseIsSuccessful();
    $this->assertNotNull($writtenAuthor->id);
    $this->assertEquals($writtenAuthor->firstName, $firstName);
  }

  /**
   * @dataProvider authorProvider
   */
  public function testCreateAndReadAuthor($author)
  {
    $client = self::createClient();

    $firstName = $author['firstName'];
    $writtenAuthor = $this->writeNewAuthor($author, $client);
    $this->assertResponseIsSuccessful();
    $newId = $writtenAuthor->id;

    $client->xmlHttpRequest('GET', "/author/{$newId}?format=json");
    $readedAuthor= $this->getAuthorFromClient($client);
    $this->assertResponseIsSuccessful();
    $this->assertEquals($readedAuthor->id, $newId);
    $this->assertEquals($readedAuthor->firstName, $firstName);
  }

  /**
   * @dataProvider authorProvider 
   */
  public function testBookChange($author) {
    $client = self::createClient();
    $writtenAuthor = $this->writeNewAuthor($author, $client);

    $id = $writtenAuthor->id;
    $author['firstName'] = 'test_name';
    $author['id'] = $id;

    $client->xmlHttpRequest('POST', "/author/{$id}/edit?format=json",
      [],
      [], [],
      json_encode($author));
    $changedBook = $this->getAuthorFromClient($client);
    $this->assertResponseIsSuccessful();
    $this->assertEquals($changedBook->firstName, $author['firstName']);
    $this->assertEquals($changedBook->id, $author['id']);
  }

  /**
   * @dataProvider authorProvider
   */
  public function testDeleteauthor($author) {
    $client = self::createClient();
    $writtenauthor = $this->writeNewAuthor($author, $client);
    $id = $writtenauthor->id;
    $client->xmlHttpRequest('DELETE', "/author/{$id}?format=json");
    $client->xmlHttpRequest('GET', "/author/{$id}?format=json");
    $this->assertEquals(404, $client->getResponse()->getStatusCode());
  }

  public function authorProvider() {
    yield [["firstName" => "John", "surname" => "TestGalt"]];
  }

  public function writeNewAuthor($author, $client)
  {
    $client->xmlHttpRequest('POST', '/author/new?XDEBUG_SESSION_START=PHPSTORM&format=json',
      [],
      [], [],
      json_encode($author));
    $writtenAuthor = $this->getAuthorFromClient($client);
    return $writtenAuthor;
  }

  public function getAuthorFromClient($client)
  {
    $response = $client->getResponse();
    $author = json_decode($response->getContent());
    return $author;
  }
}
