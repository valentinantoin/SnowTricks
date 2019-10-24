<?php

namespace App\Controller;

use App\Entity\Tricks;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TricksController
 * @package App\Controller
 */
class TricksController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @return Response
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
     * @return Response
     */
    public function tricks()
    {
        $repoTricks = $this->getDoctrine()->getRepository(Tricks::class);
        $tricks = $repoTricks->findAll();

        return $this->render('tricks/tricks.html.twig', [
            'tricks' => $tricks
        ]);
    }

    /**
     * @Route("/trick/{id}", name="trick")
     * @param $id
     * @return Response
     */
    public function trick($id)
    {
        $repoTricks = $this->getDoctrine()->getRepository(Tricks::class);
        $trick = $repoTricks->find($id);

        return $this->render('tricks/trick.html.twig', [
            'trick' => $trick
        ]);
    }

    /**
     * @Route("/tricks/{type}", name="typeTricks")
     * @param $type
     * @return Response
     */
    public function typeTricks($type)
    {
        $repoTricks = $this->getDoctrine()->getRepository(Tricks::class);
        $tricks = $repoTricks->findAll();

        $typeTricks = $repoTricks->findBy(['type'=> $type]);

        return $this->render('tricks/typeTricks.html.twig', [
            'tricks' => $tricks,
            'typeTricks' => $typeTricks,
            'type' => $type
        ]);
    }

    /**
     * @route("/removeTrick/{id}", name="removeTrick")
     * @param tricks $trick
     * @return RedirectResponse
     */
    public function removeTrick( tricks $trick)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($trick);
        $manager->flush();

        return $this->redirectToRoute('admin', ['_fragment' => 'tricks']);
    }
}