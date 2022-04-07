<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use App\Util\Censurator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/wishes",  name="wish_")
 */
class WishController extends AbstractController
{

    /**
     * @Route("/", name="list")
     */
    public function list(WishRepository $wishRepository): Response
    {
        // récupère les Wish publiés, du plus récent au plus ancien
        $wishes = $wishRepository->findBy(['isPublished' => true], ['dateCreated' => 'DESC']);
        return $this->render('wish/list.html.twig', [
            // les passe à Twig
            "wishes" => $wishes
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request,EntityManagerInterface $entityManager, Censurator $censurator): Response
    {
        $wish = new Wish();

        $wishForm= $this ->createForm(WishType::class, $wish);
        $wishForm->handleRequest($request);

        if ($wishForm->isSubmitted()) {

            $wish->setDateCreated(new \DateTime());
            $wish->setIsPublished(true);

            //cenzura
            $wish->setDescription($censurator->purify($wish->getDescription()));

            //eregistrement
            $entityManager->persist($wish);
            $entityManager->flush();
            $this->addFlash('success', 'Wish added!');
            return $this->redirectToRoute('wish_details', ['id'=>$wish->getId()]);
        }


        return $this->render('wish/create.html.twig', [ 'wishForm'=>$wishForm->createView()]);
    }

    /**
     * @Route("/list/details/{id}", name="details")
     */
    public function details(int $id , WishRepository $wishRepository): Response
    {
        $wish=$wishRepository->find($id);
        return $this->render('wish/details.html.twig',['wish'=>$wish]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Wish $wish, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($wish);
        $entityManager->flush();
        return $this->redirectToRoute('main_home');
    }

}

