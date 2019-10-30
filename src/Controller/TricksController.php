<?php

namespace App\Controller;

use App\Entity\Tricks;
use App\Entity\Type;
use App\Form\TrickType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

        $repoType = $this->getDoctrine()->getRepository(Type::class);
        $types = $repoType->findAll();

        return $this->render('tricks/tricks.html.twig', [
            'tricks' => $tricks,
            'types' => $types
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
     * @Route("/tricks/{typeId}", name="typeTricks")
     * @param $typeId
     * @return Response
     */
    public function typeTricks($typeId)
    {
        $repoTricks = $this->getDoctrine()->getRepository(Tricks::class);
        $tricks = $repoTricks->findAll();

        $typeTricks = $repoTricks->findBy(['typeId'=> $typeId]);

        $repoType = $this->getDoctrine()->getRepository(Type::class);
        $type = $repoType->find($typeId);
        $types = $repoType->findAll();

        return $this->render('tricks/typeTricks.html.twig', [
            'tricks' => $tricks,
            'typeTricks' => $typeTricks,
            'type' => $type,
            'types' => $types
        ]);
    }

    /**
     * @route("/removeTrick/{id}", name="removeTrick")
     * @param ObjectManager $manager
     * @param $id
     * @return RedirectResponse
     */
    public function removeTrick( ObjectManager $manager, $id)
    {
        $repoTricks = $this->getDoctrine()->getRepository(Tricks::class);
        $trick = $repoTricks->find($id);

        $manager->remove($trick);
        $manager->flush();

        if($this->isGranted('ROLE_ADMIN')) {

            return $this->redirectToRoute('admin', ['_fragment' => 'tricks']);
        }

        return $this->redirectToRoute('tricks');
    }

    /**
     * @Route("/createTrick", name="createTrick")
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     * @throws \Exception
     */
    public function createTrick(Request $request, ObjectManager $manager)
    {
        $trick = new Tricks();
        $trick->setCreatedAt(new \DateTime());
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $img = $form['img']->getData();

            $originalImg = pathinfo($img->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeImg = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalImg);
            $newImg = $safeImg.'.'.$img->guessExtension();

            try {
                $img->move(
                    $this->getParameter('trick_directory'),
                    $newImg
                );
            } catch (FileException $e) {
                throw new \Exception('Une erreur s\'est produite');
            }

            $trick->setImg($newImg);
            $manager->persist($trick);
            $manager->flush();

            $this->addFlash('add', 'Trick bien ajouté !');

            return $this->redirectToRoute('tricks');
        }

        return $this->render('tricks/createTrick.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/modification/{id}", name="update")
     * @param Request $request
     * @param ObjectManager $manager
     * @param Tricks $trick
     * @return RedirectResponse | Response
     */
    public function updateTrick(Request $request,ObjectManager $manager,Tricks $trick)
    {
        $trick->setImg(
            new File($this->getParameter('trick_directory').'/'.$trick->getImg()));

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $manager->flush();

            $this->addFlash('update', 'Le trick est bien modifié !');

            return $this->redirectToRoute('tricks');
        }

        return $this->render('tricks/update.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick
        ]);
    }
}