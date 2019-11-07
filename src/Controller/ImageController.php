<?php

namespace App\Controller;

use App\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    /**
     * @Route("/deleteImg/{id}", name="deleteImg")
     * @param Image $img
     * @return RedirectResponse
     */
    public function deleteImg(Image $img)
    {
        $trickId = $img->getTrickId()->getId();

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($img);
        $manager->flush();

        return $this->redirectToRoute('trick', ['id' => $trickId, '_fragment' => 'media']);
    }
}