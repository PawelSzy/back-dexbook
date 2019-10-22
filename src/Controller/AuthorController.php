<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use App\Traits\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/author")
 */
class AuthorController extends AbstractController
{
  use ControllerTrait;

  function __construct(SerializerInterface $serializer)
  {
    $this->serializer = $serializer;
  }

  /**
   * @Route("/", name="author_index", methods={"GET"})
   */
  public function index(AuthorRepository $authorRepository): Response
  {
      return $this->render('author/index.html.twig', [
          'authors' => $authorRepository->findAll(),
      ]);
  }

    /**
     * @Route("/new", name="author_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
      if ($request->isMethod('post') && $request->query->get('format') == 'json') {
        $author = $this->serializer->deserialize($request->getContent(), Author::class, 'json');
        $em = $this->getDoctrine()->getManager();

        try {
          $em->persist($author);
          $em->flush();
        } catch (\Exception $e) {
          if ($e->getMessage() == 'Author exist') {
            return new JsonResponse(['error' => 'Author exist'], Response::HTTP_CONFLICT);
          } else {
            throw $e;
          }
        }
        return $this->_object_to_json_response($author);
      }

        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($author);
            $entityManager->flush();

            return $this->redirectToRoute('author_index');
        }

        return $this->render('author/new.html.twig', [
            'author' => $author,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="author_show", methods={"GET"})
     */
    public function show(Author $author, Request $request): Response
    {
      if ($request->query->get('format') == 'json') {
        return $this->_object_to_json_response($author);
      }
        return $this->render('author/show.html.twig', [
            'author' => $author,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="author_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Author $author): Response
    {
      if ($request->isMethod('post') && $request->query->get('format') == 'json') {
        $jsonAuthor = $this->serializer->deserialize(
          $request->getContent(),
          Author::class,
          'json',
          ['object_to_populate' => $author]
        );
        $em = $this->getDoctrine()->getManager();
        $em->persist($jsonAuthor);
        $em->flush();

        return $this->_object_to_json_response($jsonAuthor);
      }
      
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('author_index');
        }

        return $this->render('author/edit.html.twig', [
            'author' => $author,
            'form' => $form->createView(),
        ]);
    }

   /**
    * @Route("/{id}", name="author_delete", methods={"DELETE"})
    */
    public function delete(Request $request, Author $author): Response
    {
      if ($request->query->get('format') == 'json') {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($author);
        $entityManager->flush();
        return new JsonResponse(['message' => 'author deleted'], Response::HTTP_NO_CONTENT);
      }
      
      if ($this->isCsrfTokenValid('delete'.$author->getId(), $request->request->get('_token'))) {
          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->remove($author);
          $entityManager->flush();
      }

      return $this->redirectToRoute('author_index');
    }
}
