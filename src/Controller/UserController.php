<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;

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
     * @return RedirectResponse | Response
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

            return $this->redirectToRoute('connection');
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
     * @return Response
     */
    public function deleteAccount()
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

    /**
     * @Route("/passwordChange", name="passwordChange")
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @param TokenGeneratorInterface $tokenGenerator
     * @return Response
     */
    public function forgottenPassword(
        Request $request,
        \Swift_Mailer $mailer,
        TokenGeneratorInterface $tokenGenerator
    )
    {

        if ($request->isMethod('POST')) {

            $mail = $request->request->get('email');

            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneBy(['mail' => $mail]);
            /* @var $user User */

            if ($user === null) {
                $this->addFlash('danger', 'Email Inconnu');
                return $this->redirectToRoute('passwordChange');
            }
            $token = $tokenGenerator->generateToken();

            try{
                $user->setResetToken($token);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('home');
            }

            $url = $this->generateUrl('newPassword', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

            $message = (new \Swift_Message('Mot de passe oublié'))
                ->setFrom('valentin.antoin@gmail.com')
                ->setTo($user->getMail())
                ->setBody(
                    "Bonjour, voici le lien pour changer votre mot de passe : " . $url,
                    'text/html'
                );

            $mailer->send($message);

            $this->addFlash('notice', 'Mail envoyé');

            return $this->redirectToRoute('passwordChange');
        }

        return $this->render('user/password.html.twig');
    }

    /**
     * @Route("/newPassword/{token}", name="newPassword")
     * @param Request $request
     * @param string $token
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
     */
    public function resetPassword(
        Request $request,
        string $token,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {

        if ($request->isMethod('POST')) {
            $entityManager = $this->getDoctrine()->getManager();

            $user = $entityManager->getRepository(User::class)->findBy(['resetToken' => $token]);
            /* @var $user User */

            if ($user === null) {
                $this->addFlash('danger', 'Token Inconnu');
                return $this->redirectToRoute('newPassword');
            }

            $user->setResetToken(null);
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe mis à jour');

            return $this->redirectToRoute('home');
        }

            return $this->render('user/newPassword.html.twig', ['token' => $token]);
    }
}