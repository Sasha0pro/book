<?php

namespace App\Controller;

use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class MainController extends AbstractController
{
    #[Route('/main', name: 'app_main',methods: ['GET'])]
    public function main(ManagerRegistry $managerRegistry): Response
    {
        $books = $managerRegistry->getRepository(Book::class)->findAll();

        return $this->render('main/index.html.twig', [
            'books' => $books
        ]);
    }
}
