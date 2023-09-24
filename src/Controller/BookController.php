<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\User;
use App\Form\BookType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class BookController extends AbstractController
{
    #[Route('/books', name: 'user_book', methods: ['GET'])]
    public function Books(): Response
    {
        /** @var  $user User*/

        $user = $this->getUser();

        return $this->render('book/index.html.twig', [
            'books' => $user->getBooks()->toArray(),
        ]);
    }
    #[Route('/create', name: 'create_book', methods:['POST','GET'] )]
    public function create(Request $request, ManagerRegistry $managerRegistry ): Response
    {
        /** @var  $user User */

        $user = $this->getUser();
        $book = new Book();
        $form = $this->createForm(BookType::class,$book)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $em = $managerRegistry->getManager();
            $em->persist($book->addUser($user));
            $em->flush();

            return $this->redirectToRoute('app_main');
        }

        return $this->render('book/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/book/{book_id}/content', name: 'content_book', methods: ['GET'])]
    #[ParamConverter('book', Book::class, options: ["id" => "book_id"])]
    public function content(Book $book): Response
    {

        return $this->render('book/content.html.twig', [
            'content' => $book->getContent(),
        ]);
    }

    #[Route('/book/{book_id}/update', name: 'update_book', methods: ['POST','GET'])]
    #[ParamConverter('book', Book::class, options: ["id" => "book_id"])]
    #[IsGranted('EDIT','book')]
    public function update(Book $book,Request $request, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createForm(BookType::class,$book)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $em = $managerRegistry->getManager();
            $em->persist($book);
            $em->flush();

            return $this->redirectToRoute('app_main');
        }

        return $this->render('book/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/book/{book_id}/delete', name: 'delete_book', methods: ['GET'])] // DELETE почему-то не работает
    #[ParamConverter('book', Book::class, options: ['id' => 'book_id'])]
    #[IsGranted('DELETE','book')]
    public function delete(Book $book, ManagerRegistry $managerRegistry ): Response
    {
            $em = $managerRegistry->getManager();
            $em->remove($book);
            $em->flush();
            return $this->redirectToRoute('app_main');
    }
}
