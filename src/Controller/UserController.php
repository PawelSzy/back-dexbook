<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Traits\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    use ControllerTrait;

    function __construct(SerializerInterface $serializer)
    {
      $this->serializer = $serializer;
    }

    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
      if ($request->isMethod('post') && $request->query->get('format') == 'json') {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $em = $this->getDoctrine()->getManager();

        try {
          $em->persist($user);
          $em->flush();
        } catch (\Exception $e) {
          if ($e->getMessage() == 'User exist') {
            return new JsonResponse(['error' => 'User exist'], Response::HTTP_CONFLICT);
          } else {
            throw $e;
          }
        }
        return $this->_object_to_json_response($user);
      }

      $user = new User();
      $form = $this->createForm(UserType::class, $user);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->persist($user);
          $entityManager->flush();

          return $this->redirectToRoute('user_index');
      }

      return $this->render('user/new.html.twig', [
          'user' => $user,
          'form' => $form->createView(),
      ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user, Request $request): Response
    {
      if ($request->query->get('format') == 'json') {
        return $this->_object_to_json_response($user);
      }

      return $this->render('user/show.html.twig', [
          'user' => $user,
      ]);
    }

  /**
   * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
   */
  public function edit(Request $request, User $user): Response
  {
    if ($request->isMethod('post') && $request->query->get('format') == 'json') {
      $jsonUser = $this->serializer->deserialize(
        $request->getContent(),
        User::class,
        'json',
        ['object_to_populate' => $user]
      );
      $em = $this->getDoctrine()->getManager();
      $em->persist($jsonUser);
      $em->flush();

      return $this->_object_to_json_response($jsonUser);
    } 
    
    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('user_index');
    }

    return $this->render('user/edit.html.twig', [
        'user' => $user,
        'form' => $form->createView(),
    ]);
}

  /**
   * @Route("/{id}", name="user_delete", methods={"DELETE"})
   */
  public function delete(Request $request, User $user): Response
  {
    if ($request->query->get('format') == 'json') {
      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->remove($user);
      $entityManager->flush();
      return new JsonResponse(['message' => 'User deleted'], Response::HTTP_NO_CONTENT);
    }
    
    if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();
    }

    return $this->redirectToRoute('user_index');
  }
}
