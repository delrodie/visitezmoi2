<?php

namespace App\Controller;

use App\Entity\ProduitMagasin;
use App\Form\ProduitMagasinType;
use App\Repository\ProduitMagasinRepository;
use App\Utilities\GestionLog;
use App\Utilities\GestionMedia;
use App\Utilities\Utility;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/produit/magasin")
 */
class ProduitMagasinController extends AbstractController
{
    private $gestionMedia;
    private $log;
    private $utility;

    public function __construct(GestionLog $log, GestionMedia $gestionMedia, Utility $utility)
    {
        $this->log = $log;
        $this->gestionMedia = $gestionMedia;
        $this->utility =$utility;
    }

    /**
     * @Route("/", name="produit_magasin_index", methods={"GET"})
     */
    public function index(ProduitMagasinRepository $produitMagasinRepository): Response
    {
        return $this->render('produit_magasin/index.html.twig', [
            'produit_magasins' => $produitMagasinRepository->findBy([],['id'=>'DESC']),
        ]);
    }

    /**
     * @Route("/new", name="produit_magasin_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $produitMagasin = new ProduitMagasin();
        $form = $this->createForm(ProduitMagasinType::class, $produitMagasin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Creation du slug du parteniaire
            $slugify = new Slugify();
            $reference = $slugify->slugify($produitMagasin->getReference());
            $marque = $slugify->slugify($produitMagasin->getMarque());
            $produitMagasin->setSlug($marque.'-'.$reference);

            // Gestion des fichiers
            $mediaFile = $form->get('media')->getData();

            // Traitement du fichier s'il a été telechargé
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'produit_media');

                $produitMagasin->setMedia($media);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produitMagasin);
            $entityManager->flush();

            // Mise a jour du nombre de produit dans la table Bien
            $this->utility->addProduit($produitMagasin->getBien()->getId());

            //Enregistrerment du log
            $action = $this->getUser()->getUsername()." a enregistré le produit magasin ".$produitMagasin->getMarque().' '.$produitMagasin->getReference();
            $this->log->addLog($action);

            // Message flash
            $this->addFlash('success', "Le produit magasin ".$produitMagasin->getMarque().' '.$produitMagasin->getReference()." a bien été enregistré");

            return $this->redirectToRoute('produit_magasin_index');
        }

        return $this->render('produit_magasin/new.html.twig', [
            'produit_magasin' => $produitMagasin,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="produit_magasin_show", methods={"GET"})
     */
    public function show(ProduitMagasin $produitMagasin): Response
    {
        return $this->render('produit_magasin/show.html.twig', [
            'produit_magasin' => $produitMagasin,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="produit_magasin_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ProduitMagasin $produitMagasin): Response
    {
        $form = $this->createForm(ProduitMagasinType::class, $produitMagasin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Creation du slug du parteniaire
            $slugify = new Slugify();
            $reference = $slugify->slugify($produitMagasin->getReference());
            $marque = $slugify->slugify($produitMagasin->getMarque());
            $produitMagasin->setSlug($marque.'-'.$reference);

            // Gestion des fichiers
            $mediaFile = $form->get('media')->getData();
            $ancienMedia = $request->get('ancien_media');

            // Traitement du fichier s'il a été telechargé
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'produit_media');

                $produitMagasin->setMedia($media);
                $this->gestionMedia->removeUpload($ancienMedia, 'produit_media');
            }

            $this->getDoctrine()->getManager()->flush();

            //Verification de modification du bien
            $ancienBien = $request->get('ancien_bien');
            $nouveauBien = $produitMagasin->getBien()->getId();

            // Mise a jour du nombre de produit dans la table Bien
            if ($ancienBien != $nouveauBien){
                $this->utility->addProduit($produitMagasin->getBien()->getId());
                $this->utility->deleteProduit($ancienBien);
            }

            //Enregistrerment du log
            $action = $this->getUser()->getUsername()." a modifié le produit magasin ".$produitMagasin->getMarque().' '.$produitMagasin->getReference();
            $this->log->addLog($action);

            // Message flash
            $this->addFlash('success', "Le produit magasin ".$produitMagasin->getMarque().' '.$produitMagasin->getReference()." a bien été modifié");


            return $this->redirectToRoute('produit_magasin_index');
        }

        return $this->render('produit_magasin/edit.html.twig', [
            'produit_magasin' => $produitMagasin,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="produit_magasin_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ProduitMagasin $produitMagasin): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produitMagasin->getId(), $request->request->get('_token'))) {
            // Recuperation du bien concerné
            $bien = $produitMagasin->getBien()->getId();
            $produit = $produitMagasin->getMarque().' '.$produitMagasin->getReference();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($produitMagasin);
            $entityManager->flush();

            $this->utility->deleteProduit($bien);

            //Enregistrerment du log
            $action = $this->getUser()->getUsername()." a suprimé le produit magasin ".$produit;
            $this->log->addLog($action);

            // Message flash
            $this->addFlash('success', "Le produit magasin ".$produitMagasin->getMarque().' '.$produitMagasin->getReference()." a bien été supprimé");

        }

        return $this->redirectToRoute('produit_magasin_index');
    }
}
