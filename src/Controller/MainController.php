<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class MainController extends AbstractController
{
    #[Route('/main', name: 'app_main',methods: ['GET'])]
    public function main(BookRepository $bookRepository, Request $request): Response
    {
        $page = $request->get('page',1);
        return $this->render('main/index.html.twig', [
            'books' => $bookRepository->getList($page)
        ]);
    }
}
