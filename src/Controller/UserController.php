<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
{
    /**
     * @Route("/inscription", name="registration")
     * @param Request $request
     * @param ObjectManager $objectManager
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @return Response
     * @throws \Exception
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
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function connection(AuthenticationUtils $authenticationUtils)
    {
        $this->addFlash('connected', 'Vous êtes bien connecté !');

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/connection.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @Route("/deconnexion", name="deconnection")
     * @param Session $session
     */
    public function deconnection(Session $session)
    {
        $session->getFlashBag()->clear();
    }

    /**
     * @Route("/compte", name="account")
     * @return Response
     */
    public function accountLoad()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        return $this->render('user/account.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/userAccount/{id}", name="userAccount")
     * @param $id
     * @return Response
     */
    public function userAccount($id)
    {
        if($this->isGranted('ROLE_ADMIN')) {

            $repoUser = $this->getDoctrine()->getRepository(User::class);
            $user = $repoUser->find($id);

            return $this->render('user/account.html.twig', [
                'user' => $user
            ]);
        }
        $this->addFlash('admin', 'vous n\'avez pas dit le mot magique');

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/deinscription", name="deleteAccount")
     * @param Session $session
     * @return Response
     */
    public function deleteAccount( Session $session)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $session = new Session();
        $session->invalidate();

        $user = $this->getUser();

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($user);
        $manager->flush();

        $this->addFlash('delete', 'Votre compte est supprimé !');

        return $this->redirectToRoute('home');
    }
}