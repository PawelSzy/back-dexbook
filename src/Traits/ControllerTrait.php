<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ControllerTrait {

  private function _object_to_json_response($object)
  {
    $json = $this->serializer->serialize(
      $object,
      'json', [
      'circular_reference_handler' => function ($object) {
        return $object->getId();
      }
    ]);
    $response = new Response($json);
    $response->headers->set('Content-Type', 'application/json');

    return $response;
  }
}