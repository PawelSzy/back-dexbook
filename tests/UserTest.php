<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase
{

  /**
   * @dataProvider userProvider
   */
  public function testCreateUser($user)
  {
    $client = self::createClient();
    $username = $user['username'];
    $writtenUser = $this->writeData($user, $client);
    $this->assertResponseIsSuccessful();
    $this->assertNotNull($writtenUser->id);
    $this->assertEquals($writtenUser->username, $username);
  }

  /**
   * @dataProvider userProvider
   */
  public function testCreateAndReadUser($user)
  {
    $client = self::createClient();
    $username = $user['username'];
    $writtenUser = $this->writeData($user, $client);
    $this->assertResponseIsSuccessful();
    $newId = $writtenUser->id;

    $client->xmlHttpRequest('GET', "/user/{$newId}?format=json");
    $readedUser= $this->getDataFromClient($client);
    $this->assertResponseIsSuccessful();
    $this->assertEquals($readedUser->id, $newId);
    $this->assertEquals($readedUser->username, $username);
  }

  /**
   * @dataProvider userProvider
   */
  public function testUserChange($user) {
    $client = self::createClient();
    $writtenUser = $this->writeData($user, $client);

    $id = $writtenUser->id;
    $user['username'] = 'test_username';
    $user['id'] = $id;

    $client->xmlHttpRequest('POST', "/user/{$id}/edit?format=json",
      [],
      [], [],
      json_encode($user));
    $changedUser = $this->getDataFromClient($client);
    $this->assertResponseIsSuccessful();
    $this->assertEquals($changedUser->username, $user['username']);
    $this->assertEquals($changedUser->id, $user['id']);
  }

  /**
   * @dataProvider userProvider
   */
  public function testDeleteUser($user) {
    $client = self::createClient();
    $writtenUser = $this->writeData($user, $client);
    $id = $writtenUser->id;
    $client->xmlHttpRequest('DELETE', "/user/{$id}?format=json");
    $client->xmlHttpRequest('GET', "/user/{$id}?format=json");
    $this->assertEquals(404, $client->getResponse()->getStatusCode());
  }

  /**
   * @dataProvider userProvider
   */
  public function testWriteToReadBook($user, $book) {
    $client = self::createClient();
    $writtenUser = $this->writeData($user, $client, '/user/new');
    $writtenBook = $this->writeData($book, $client, '/book/new');

    $userId = $writtenUser->id;
    $bookId = $writtenBook->id;

    $client->xmlHttpRequest('POST', "/user/{$userId}/add-to-read-book/{$bookId}");
    $this->assertResponseIsSuccessful();

    $client->xmlHttpRequest('GET', "/user/{$userId}?format=json");
    $readedUser= $this->getDataFromClient($client);
    $this->assertResponseIsSuccessful();
  }

  /**
   * @dataProvider userProvider
   */
  public function testWriteAndReadToReadBook($user, $book) {
    $client = self::createClient();
    $writtenUser = $this->writeData($user, $client, '/user/new');
    $writtenBook = $this->writeData($book, $client, '/book/new');

    $userId = $writtenUser->id;
    $bookId = $writtenBook->id;

    $client->xmlHttpRequest('POST', "/user/{$userId}/add-to-read-book/{$bookId}");
    $this->assertResponseIsSuccessful();

    $client->xmlHttpRequest('GET', "/user/get-to-read-books/{$userId}");
    $this->assertResponseIsSuccessful();
    $toReadBook= $this->getDataFromClient($client);
    $this->assertEquals($bookId, $toReadBook[0]->id);
  }

  /**
   * @dataProvider userProvider
   */
  public function testDeleteReadBook($user, $book) {
    $client = self::createClient();
    $writtenUser = $this->writeData($user, $client, '/user/new');
    $writtenBook = $this->writeData($book, $client, '/book/new');

    $userId = $writtenUser->id;
    $bookId = $writtenBook->id;

    $client->xmlHttpRequest('POST', "/user/{$userId}/add-to-read-book/{$bookId}");
    $client->xmlHttpRequest('DELETE', "/user/delete-to-read-books/{$userId}/{$bookId}");

    $client->xmlHttpRequest('GET', "/user/get-to-read-books/{$userId}");
    $toReadBook= $this->getDataFromClient($client);

    $this->assertEquals([], $toReadBook);
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

  public function writeData($data, $client, $url = '/user/new')
  {
    $client->xmlHttpRequest('POST', "{$url}?XDEBUG_SESSION_START=PHPSTORM&format=json",
      [],
      [], [],
      json_encode($data));
    $writtenUser = $this->getDataFromClient($client);
    return $writtenUser;
  }

  public function getDataFromClient($client)
  {
    $response = $client->getResponse();
    return json_decode($response->getContent());
  }

}
