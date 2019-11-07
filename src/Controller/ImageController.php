<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Tricks;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    /**
     * @Route("/addImg/{id}", name="addImg")
     * @param Request $request
     * @param ObjectManager $manager
     * @param $id
     * @return RedirectResponse
     */
    public function addImage(Request $request, ObjectManager $manager, $id)
    {
        $img = new Image();

        if($request->isMethod('post')){
            $url = $request->get("url");

            $repotrick = $this->getDoctrine()->getRepository(Tricks::class);
            $trick = $repotrick->find($id);

            $img->setTrickId($trick)
                ->setUrl($url);

            $manager->persist($img);
            $manager->flush();

            return $this->redirectToRoute('trick', ['id' => $id, '_fragment' => 'media']);
        }

        return $this->redirectToRoute('trick', [ 'id' => $id]);
    }

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