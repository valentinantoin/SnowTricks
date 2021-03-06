<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Tricks;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/createcomment/{id}", name="createComment")
     * @param Request $request
     * @param ObjectManager $manager
     * @param $id
     * @return RedirectResponse | Response
     * @throws \Exception
     */
    public function createComment(Request $request, ObjectManager $manager, $id)
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
                    ->setCreatedAt(new \Datetime())
                    ->setStatus('published');
            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('trick',['id' => $id, '_fragment' => 'comments']);
        }

        return $this->render('home/home.html.twig');
    }

    /**
     * @route("/removeComment/{id}", name="removeComment")
     * @param Comment $comment
     * @return RedirectResponse
     */
    public function removeComment( Comment $comment)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($comment);
        $manager->flush();

        if($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin', ['_fragment' => 'comments']);
        }

        return $this->redirectToRoute('account');
    }

    /**
     * @Route("/reportComment/{trickId}/{id}", name="reportComment")
     * @param ObjectManager $manager
     * @param $trickId
     * @param $id
     * @return RedirectResponse
     */
    public function reportComment(ObjectManager $manager,$trickId, $id )
    {
        $comment = $manager->getRepository(Comment::class)->find($id);
        $comment->setStatus('validation');
        $manager->flush();

        return $this->redirectToRoute('trick',['id' => $trickId, '_fragment' => 'comments']);
    }

    /**
     * @Route("/admin/validateComment/{id}", name="validateComment")
     * @param ObjectManager $manager
     * @param $id
     * @return RedirectResponse
     */
    public function validateComment(ObjectManager $manager, $id)
    {
            $comment = $manager->getRepository(Comment::class)->find($id);
            $comment->setStatus('published');
            $manager->flush();

            return $this->redirectToRoute('admin', ['_fragment' => 'comments']);
    }
}
