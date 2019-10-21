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
    $writtenUser = $this->writeNewUser($user, $client);
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
    $writtenUser = $this->writeNewUser($user, $client);
    $this->assertResponseIsSuccessful();
    $newId = $writtenUser->id;

    $client->xmlHttpRequest('GET', "/user/{$newId}?format=json");
    $readedUser= $this->getUserFromClient($client);
    $this->assertResponseIsSuccessful();
    $this->assertEquals($readedUser->id, $newId);
    $this->assertEquals($readedUser->username, $username);
  }

  /**
   * @dataProvider userProvider
   */
  public function testUserChange($user) {
    $client = self::createClient();
    $writtenUser = $this->writeNewUser($user, $client);

    $id = $writtenUser->id;
    $user['username'] = 'test_username';
    $user['id'] = $id;

    $client->xmlHttpRequest('POST', "/user/{$id}/edit?format=json",
      [],
      [], [],
      json_encode($user));
    $changedUser = $this->getUserFromClient($client);
    $this->assertResponseIsSuccessful();
    $this->assertEquals($changedUser->username, $user['username']);
    $this->assertEquals($changedUser->id, $user['id']);
  }

  /**
   * @dataProvider userProvider
   */
  public function testDeleteUser($user) {
    $client = self::createClient();
    $writtenUser = $this->writeNewUser($user, $client);
    $id = $writtenUser->id;
    $client->xmlHttpRequest('DELETE', "/user/{$id}?format=json");
    $client->xmlHttpRequest('GET', "/user/{$id}?format=json");
    $this->assertEquals(404, $client->getResponse()->getStatusCode());
  }
  
  public function userProvider() {
    yield [[
      'username' => 'test_username',
      'password' => '123test',
      'email' => 'test@test.com',
      'active' => 'true',
    ]];
  }

  public function writeNewUser($user, $client)
  {
    $client->xmlHttpRequest('POST', '/user/new?XDEBUG_SESSION_START=PHPSTORM&format=json',
      [],
      [], [],
      json_encode($user));
    $writtenUser = $this->getUserFromClient($client);
    return $writtenUser;
  }

  public function getUserFromClient($client)
  {
    $response = $client->getResponse();
    return json_decode($response->getContent());
  }

}
