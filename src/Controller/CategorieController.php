<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use App\Utilities\GestionLog;
use App\Utilities\Utility;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/categorie")
 */
class CategorieController extends AbstractController
{

    private $utility;
    private $log;
    private $user;

    public function __construct(Utility $utility, GestionLog $log)
    {
        $this->utility = $utility;
        $this->log = $log;
    }

    /**
     * @Route("/", name="categorie_index", methods={"GET","POST"})
     */
    public function index(Request $request, CategorieRepository $categorieRepository): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slugify = new Slugify();
            $slug = $slugify->slugify($categorie->getLibelle());

            $categorie->setSlug($slug);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();

            $this->utility->addCategorie($categorie->getDomaine()->getId());

            return $this->redirectToRoute('categorie_index');
        }

        //Enregistrerment du log
        $action = $this->getUser()->getUsername()." a affiché la liste des sous catégories";
        $this->log->addLog($action);

        return $this->render('categorie/index.html.twig', [
            'categories' => $categorieRepository->findBy([],['libelle'=>'ASC']),
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="categorie_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('categorie_index');
        }

        return $this->render('categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="categorie_show", methods={"GET"})
     */
    public function show(Categorie $categorie): Response
    {
        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="categorie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Categorie $categorie, CategorieRepository $categorieRepository): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slugify = new Slugify();
            $slug = $slugify->slugify($categorie->getLibelle());

            $categorie->setSlug($slug);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('categorie_index');
        }

        return $this->render('categorie/edit.html.twig', [
            'categories' => $categorieRepository->findBy([],['libelle'=>'ASC']),
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="categorie_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Categorie $categorie): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
            $domaineID = $categorie->getDomaine()->getId();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($categorie);
            $entityManager->flush();

            $this->utility->deleteCategorie($domaineID);
        }

        return $this->redirectToRoute('categorie_index');
    }
}
