<?php

namespace App\Controller;

use App\Entity\Type;
use App\Form\TypeType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TypeController extends AbstractController
{
    /**
     * @Route("/nouveauType", name="createType")
     * @param Request $request
     * @param ObjectManager $manager
     * @return RedirectResponse | Response
     */
    public function createType(Request $request, ObjectManager $manager)
    {
        $type = new Type();

        $typeForm = $this->createForm(TypeType::class);
        $typeForm->handleRequest($request);

        if ($typeForm->isSubmitted() && $typeForm->isValid()){

            $type = $typeForm->getData();

            $manager->persist($type);
            $manager->flush();

            $this->addFlash('success', 'Type ajouté avec succes !');

            return $this->redirectToRoute('createTrick');
        }

        return $this->render('type/createType.html.twig', [
            'form' => $typeForm->createView()
        ]);
    }

    /**
     * @route("/supType/{id}", name="deleteType")
     * @param Type $type
     * @return RedirectResponse
     */
    public function deleteType( Type $type)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($type);
        $manager->flush();

        return $this->redirectToRoute('admin', ['_fragment' => 'types']);

    }
}
