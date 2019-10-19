<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/inscription", name="registration")
     */
    public function registration( Request $request, ObjectManager $objectManager, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $user = new User();
        $user->setCreatedAt(new \DateTime());

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $hash = $userPasswordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $objectManager->persist($user);
            $objectManager->flush();
            $this->addFlash('register', 'Votre compte est bien créé !');
            $this->redirectToRoute('connection');
        }

        return $this->render('user/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/connexion", name="connection")
     */
    public function connection()
    {
        return $this->render('user/connection.html.twig');
    }

    /**
     * @Route("/deconnexion", name="deconnection")
     */
    public function deconnection() {}
}
