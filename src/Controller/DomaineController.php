<?php

namespace App\Controller;

use App\Entity\Domaine;
use App\Form\DomaineType;
use App\Repository\DomaineRepository;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/domaine")
 */
class DomaineController extends AbstractController
{
    /**
     * @Route("/", name="domaine_index", methods={"GET","POST"})
     */
    public function index(Request $request, DomaineRepository $domaineRepository): Response
    {
        $domaine = new Domaine();
        $form = $this->createForm(DomaineType::class, $domaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slugify = new Slugify();
            $slug = $slugify->slugify($domaine->getLibelle());
            $domaine->setSlug($slug);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($domaine);
            $entityManager->flush();

            return $this->redirectToRoute('domaine_index');
        }
        return $this->render('domaine/index.html.twig', [
            'domaines' => $domaineRepository->findBy([],['libelle'=>'ASC']),
            'domaine' => $domaine,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="domaine_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $domaine = new Domaine();
        $form = $this->createForm(DomaineType::class, $domaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($domaine);
            $entityManager->flush();

            return $this->redirectToRoute('domaine_index');
        }

        return $this->render('domaine/new.html.twig', [
            'domaine' => $domaine,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="domaine_show", methods={"GET"})
     */
    public function show(Domaine $domaine): Response
    {
        return $this->render('domaine/show.html.twig', [
            'domaine' => $domaine,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="domaine_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Domaine $domaine, DomaineRepository $domaineRepository): Response
    {
        $form = $this->createForm(DomaineType::class, $domaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slugify = new Slugify();
            $slug = $slugify->slugify($domaine->getLibelle());
            $domaine->setSlug($slug);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('domaine_index');
        }

        return $this->render('domaine/edit.html.twig', [
            'domaines' => $domaineRepository->findBy([],['libelle'=>'ASC']),
            'domaine' => $domaine,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="domaine_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Domaine $domaine): Response
    {
        if ($this->isCsrfTokenValid('delete'.$domaine->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($domaine);
            $entityManager->flush();
        }

        return $this->redirectToRoute('domaine_index');
    }
}
