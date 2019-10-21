<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait BookAuthorTestTrait {
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