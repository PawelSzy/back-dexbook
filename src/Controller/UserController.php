<?php

namespace App\Controller;

use App\Entity\Book;
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
   * @Route("/{id}/add-readed-book/{bookId}", name="add-readed-book", methods={"POST"},
   * requirements={"id":"\d+", "bookId":"\d+"}
   * )
   */
  public function addReaded(User $user, int $bookId) {
    $book = $this->getDoctrine()
      ->getRepository(Book::class)
      ->find($bookId);
    $em = $this->getDoctrine()->getManager();

    $user = $user->addReaded($book);
    $em->persist($user);
    $em->flush();

    return $this->_object_to_json_response($user);
  }

 /**
  * @Route("/get-readed-books/{id}", name="get_readed_books", methods={"GET"},  requirements={"id":"\d+"})
  */
  public function getReaded(User $user) {
    return $this->_object_to_json_response($user->getReaded());
  }

 /**
  * @Route("/delete-readed-books/{id}/{bookId}", name="delete_readed_books", methods={"DELETE"},
  * requirements={"id":"\d+", "bookId":"\d+"}
  * )
  */
  public function deleteReaded(User $user, int $bookId) {
    $book = $this->getDoctrine()
      ->getRepository(Book::class)
      ->find($bookId);
    $em = $this->getDoctrine()->getManager();

    $user = $user->removeReaded($book);
    $em->persist($user);
    $em->flush();

    return $this->_object_to_json_response($user);
  }

  /**
   * @Route("/{id}/add-to-read-book/{bookId}", name="add_to_read_book", methods={"POST"},
   * requirements={"id":"\d+", "bookId":"\d+"}
   * )
   */
  public function addToRead(User $user, int $bookId) {
    $book = $this->getDoctrine()
      ->getRepository(Book::class)
      ->find($bookId);
    $em = $this->getDoctrine()->getManager();

    $user = $user->addToRead($book);
    $em->persist($user);
    $em->flush();

    return $this->_object_to_json_response($user);
  }

  /**
   * @Route("/get-to-read-books/{id}", name="get_to_read_books", methods={"GET"}, requirements={"id":"\d+"})
   */
  public function getToRead(User $user) {
  return $this->_object_to_json_response($user->getToRead());
  }

  /**
   * @Route("/delete-to-read-books/{id}/{bookId}", name="delete_to_read_books", methods={"DELETE"},
   * requirements={"id":"\d+", "bookId":"\d+"}
   * )
   */
  public function deleteToRead(User $user, int $bookId) {
    $book = $this->getDoctrine()
      ->getRepository(Book::class)
      ->find($bookId);
    $em = $this->getDoctrine()->getManager();

    $user = $user->removeToRead($book);
    $em->persist($user);
    $em->flush();

    return $this->_object_to_json_response($user);
  }


  /**
   * @Route("/{id}", name="user_show", methods={"GET"}, requirements={"id":"\d+"})
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
   * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"}, requirements={"id":"\d+"})
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
   * @Route("/{id}", name="user_delete", methods={"DELETE"}, requirements={"id":"\d+"})
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
