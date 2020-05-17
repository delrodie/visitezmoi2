<?php

namespace App\Controller;

use App\Entity\Partenaire;
use App\Form\PartenaireType;
use App\Repository\PartenaireRepository;
use App\Utilities\GestionLog;
use App\Utilities\GestionMedia;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/partenaire")
 */
class PartenaireController extends AbstractController
{
    private $log;
    private $gestionMedia;

    public function __construct(GestionLog $log, GestionMedia $gestionMedia)
    {
        $this->log = $log;
        $this->gestionMedia = $gestionMedia;
    }

    /**
     * @Route("/", name="partenaire_index", methods={"GET","POST"})
     */
    public function index(Request $request, PartenaireRepository $partenaireRepository): Response
    {
        $partenaire = new Partenaire();
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Creation du slug du parteniaire
            $slugify = new Slugify();
            $slug = $slugify->slugify($partenaire->getNom());
            $partenaire->setSlug($slug);

            // Gestion des fichiers
            $mediaFile = $form->get('logo')->getData();

            // Traitement du fichier s'il a été telechargé
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'logo');

                $partenaire->setLogo($media);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($partenaire);
            $entityManager->flush();

            //Enregistrerment du log
            $action = $this->getUser()->getUsername()." a enregistré le partenaire ".$partenaire->getNom();
            $this->log->addLog($action);

            return $this->redirectToRoute('partenaire_index');
        }

        //Enregistrerment du log
        $action = $this->getUser()->getUsername()." a affiché la liste des partenaires";
        $this->log->addLog($action);

        return $this->render('partenaire/index.html.twig', [
            'partenaires' => $partenaireRepository->findBy([],['nom'=>'ASC']),
            'partenaire' => $partenaire,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="partenaire_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $partenaire = new Partenaire();
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($partenaire);
            $entityManager->flush();

            return $this->redirectToRoute('partenaire_index');
        }

        return $this->render('partenaire/new.html.twig', [
            'partenaire' => $partenaire,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="partenaire_show", methods={"GET"})
     */
    public function show(Partenaire $partenaire): Response
    {
        return $this->render('partenaire/show.html.twig', [
            'partenaire' => $partenaire,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="partenaire_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Partenaire $partenaire, PartenaireRepository $partenaireRepository): Response
    {
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Creation du slug du parteniaire
            $slugify = new Slugify();
            $slug = $slugify->slugify($partenaire->getNom());
            $partenaire->setSlug($slug);

            // Gestion des fichiers
            $mediaFile = $form->get('logo')->getData();

            // Traitement du fichier s'il a été telechargé
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'logo');

                $partenaire->setLogo($media);
            }

            $this->getDoctrine()->getManager()->flush();

            //Enregistrerment du log
            $action = $this->getUser()->getUsername()." a modifié le partenaire ".$partenaire->getNom();
            $this->log->addLog($action);

            return $this->redirectToRoute('partenaire_index');
        }

        //Enregistrerment du log
        $action = $this->getUser()->getUsername()." a tenté de modifier le partenaire ".$partenaire->getNom();
        $this->log->addLog($action);

        return $this->render('partenaire/edit.html.twig', [
            'partenaires' => $partenaireRepository->findBy([],['nom'=>'ASC']),
            'partenaire' => $partenaire,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="partenaire_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Partenaire $partenaire): Response
    {
        if ($this->isCsrfTokenValid('delete'.$partenaire->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($partenaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('partenaire_index');
    }
}
