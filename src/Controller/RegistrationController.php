<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'app_registration',methods: ['POST','GET'])]
    public function registration(Request $request, ManagerRegistry $managerRegistry,UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class,$user)->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $managerRegistry->getManager();
            $hasherPassword = $passwordHasher->hashPassword($user,$user->getPassword());

            $user->setPassword($hasherPassword);
            $em->persist($user);
            $em->flush();

            return  $this->redirectToRoute('app_login');
        }
        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
