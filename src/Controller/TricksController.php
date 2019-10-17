<?php

namespace App\Controller;

use App\Entity\Tricks;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TricksController
 * @package App\Controller
 */
class TricksController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        $lastTricks = $this->getDoctrine()->getRepository(Tricks::class)->lastTricks();

        return $this->render('tricks/home.html.twig', [
            'tricks' => $lastTricks
        ]);
    }

    /**
     * @Route("/tricks", name="tricks")
     */
    public function tricks()
    {
        $repoTricks = $this->getDoctrine()->getRepository(Tricks::class);
        $tricks = $repoTricks->findAll();

        return $this->render('tricks/tricks.html.twig', [
            'tricks' => $tricks
        ]);
    }
}
