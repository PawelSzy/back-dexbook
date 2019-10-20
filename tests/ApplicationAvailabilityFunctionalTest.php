<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{

  /**
   * @dataProvider urlProvider
   */
  public function testPageIsSuccessful($url)
  {
    $client = self::createClient();
    $client->request('GET', $url);

    $this->assertResponseIsSuccessful();
  }

  public function urlProvider()
  {
    yield ['/books'];
    yield ['/book/new'];
    yield ['/author/'];
    yield ['/author/new'];
  }
}
