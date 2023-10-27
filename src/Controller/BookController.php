<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\User;
use App\Form\BookType;
use App\Repository\BookRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\Persistence\ManagerRegistry;

class BookController extends AbstractController
{
    #[Route('/books', name: 'user_book', methods: ['GET'])]
    public function Books(Request $request, BookRepository $bookRepository): Response
    {
        /** @var  $user User*/

        $user = $this->getUser();
        $page = $request->get('page', 1);

        return $this->render('book/index.html.twig', [
            'books' => $bookRepository->getByUser($page, $user),
        ]);
    }
    #[Route('/create', name: 'create_book', methods:['POST','GET'] )]
    public function create(Request $request, EntityManagerInterface $manager, ValidatorInterface $validator ): Response
    {
        /** @var  $user User */

        $user = $this->getUser();
        $book = new Book();
        $form = $this->createForm(BookType::class,$book)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $manager->persist($book->addUser($user));
            $manager->flush();

            $this->addFlash('success','Create book');

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
    public function update(Book $book,Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(BookType::class,$book)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $manager->persist($book);
            $manager->flush();

            $this->addFlash('success','Update book');

            return $this->redirectToRoute('app_main');
        }

        return $this->render('book/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/book/{book_id}/delete', name: 'delete_book', methods: ['GET'])] // DELETE почему-то не работает
    #[ParamConverter('book', Book::class, options: ['id' => 'book_id'])]
    #[IsGranted('DELETE','book')]
    public function delete(Book $book, EntityManagerInterface $manager ): Response
    {
            $manager->remove($book);
            $manager->flush();
            $this->addFlash('success','Delete book');
            return $this->redirectToRoute('app_main');
    }

    #[Route('/book/repository', name: 'repository', methods: ['GET'])]
    public function repository(ManagerRegistry $managerRegistry, Request $request): Response
    {
        $page = $request->get('page', 1);
        $books = $managerRegistry->getRepository(Book::class)->findBookTwoAuthorAndN($page);

        return $this->render('book/repository.html.twig', [
            'books' => $books,
        ]);
    }
}
