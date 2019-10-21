<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Tricks;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CommentController extends AbstractController
{
    /**
     * @Route("/createcomment/{id}", name="createComment")
     * @param Request $request
     * @param ObjectManager $manager
     * @param Security $security
     * @param $id
     * @return Response
     * @throws \Exception
     */
    public function createComment(Request $request, ObjectManager $manager, Security $security, $id)
    {
        $comment = new Comment();

        if($request->isMethod('post')){

            $content = $request->get("content");
            $user = $this->getUser();
            $repoTricks = $this->getDoctrine()->getRepository(Tricks::class);
            $trick = $repoTricks->find($id);

            $comment->setContent($content)
                ->setTrickId($trick)
                ->setUser($user)
                ->setCreatedAt(new \Datetime());
            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('trick',['id' => $id]);

        }
            return $this->render('home/home.html.twig');
        }
}
