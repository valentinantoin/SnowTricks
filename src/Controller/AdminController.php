<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Tricks;
use App\Entity\Type;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     * @return Response
     */
    public function admin()
    {
        $repoTricks = $this->getDoctrine()->getRepository(Tricks::class);
        $tricks = $repoTricks->findAll();

        $repoTypes = $this->getDoctrine()->getRepository(Type::class);
        $types = $repoTypes->findAll();

        $repoUsers = $this->getDoctrine()->getRepository(User::class);
        $users = $repoUsers->findAll();

        $repoComments = $this->getDoctrine()->getRepository(Comment::class);
        $comments = $repoComments->findBy(['status' => 'validation']);

        return $this->render('admin/admin.html.twig', [
            'tricks' => $tricks,
            'types' => $types,
            'users' => $users,
            'comments' => $comments
        ]);
    }
}