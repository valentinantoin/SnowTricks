<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Tricks;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function admin()
    {
        $repoTricks = $this->getDoctrine()->getRepository(Tricks::class);
        $tricks = $repoTricks->findAll();

        $repoUsers = $this->getDoctrine()->getRepository(User::class);
        $users = $repoUsers->findAll();

        $repoComments = $this->getDoctrine()->getRepository(Comment::class);
        $comments = $repoComments->findBy(['status' => 'validation']);

        return $this->render('admin/admin.html.twig', [
            'tricks' => $tricks,
            'users' => $users,
            'comments' => $comments
        ]);
    }
}
