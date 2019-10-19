<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/inscription", name="registration")
     */
    public function registration()
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        return $this->render('user/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
