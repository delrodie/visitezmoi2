<?php

namespace App\Controller;

use App\Entity\Bien;
use App\Form\BienType;
use App\Repository\BienRepository;
use App\Utilities\GestionLog;
use App\Utilities\GestionMedia;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/bien")
 */
class BienController extends AbstractController
{
    private $gestionMedia;
    private $log;

    public function __construct(GestionMedia $gestionMedia, GestionLog $log)
    {
        $this->gestionMedia = $gestionMedia;
        $this->log = $log;
    }

    /**
     * @Route("/", name="bien_index", methods={"GET"})
     */
    public function index(BienRepository $bienRepository): Response
    {
        //Enregistrerment du log
        $action = $this->getUser()->getUsername()." a affiché la liste des biens";
        $this->log->addLog($action);

        return $this->render('bien/index.html.twig', [
            'biens' => $bienRepository->findBy([],['id'=>'DESC']),
        ]);
    }

    /**
     * @Route("/new", name="bien_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $bien = new Bien();
        $form = $this->createForm(BienType::class, $bien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Creation du slug du parteniaire
            $slugify = new Slugify();
            $slug = $slugify->slugify($bien->getTitre());
            $bien->setSlug($slug);

            // Gestion des fichiers
            $mediaFile = $form->get('media')->getData();

            // Traitement du fichier s'il a été telechargé
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'media');

                $bien->setMedia($media);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($bien);
            $entityManager->flush();

            //Enregistrerment du log
            $action = $this->getUser()->getUsername()." a enregistré le bien ".$bien->getTitre();
            $this->log->addLog($action);

            return $this->redirectToRoute('bien_index');
        }
        //Enregistrerment du log
        $action = $this->getUser()->getUsername()." a tenté d'enregistrer un nouveau bien";
        $this->log->addLog($action);

        return $this->render('bien/new.html.twig', [
            'bien' => $bien,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="bien_show", methods={"GET"})
     */
    public function show(Bien $bien): Response
    {
        //Enregistrerment du log
        $action = $this->getUser()->getUsername()." a affiché le bien ".$bien->getTitre();
        $this->log->addLog($action);

        return $this->render('bien/show.html.twig', [
            'bien' => $bien,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="bien_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Bien $bien): Response
    {
        $form = $this->createForm(BienType::class, $bien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Creation du slug du parteniaire
            $slugify = new Slugify();
            $slug = $slugify->slugify($bien->getTitre());
            $bien->setSlug($slug);

            // Gestion des fichiers
            $mediaFile = $form->get('media')->getData();
            $ancienMedia = $request->get('ancien_media');

            // Traitement du fichier s'il a été telechargé
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'media');

                $bien->setMedia($media);
                $this->gestionMedia->removeUpload($ancienMedia, 'media');
            }
            $this->getDoctrine()->getManager()->flush();

            //Enregistrerment du log
            $action = $this->getUser()->getUsername()." a modifié le bien ".$bien->getTitre();
            $this->log->addLog($action);

            return $this->redirectToRoute('bien_index');
        }

        //Enregistrerment du log
        $action = $this->getUser()->getUsername()." a tenté de modifier le bien ".$bien->getTitre();
        $this->log->addLog($action);

        return $this->render('bien/edit.html.twig', [
            'bien' => $bien,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="bien_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Bien $bien): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bien->getId(), $request->request->get('_token'))) {

            // Verification d'absence de produit
            if ($bien->getProduitMagasins()){
                $this->addFlash('danger', "Echec! vous ne pouvez pas supprimer ce bien car il est associé à des produits");
                return $this->redirectToRoute('bien_show',['id'=>$bien->getId()]);
            }
            // Traitement post-suppression
            $action = $this->getUser()->getUsername()." a supprimé le bien ".$bien->getTitre();
            $ancienMedia = $bien->getMedia();
            $bienSupprime = $bien;

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($bien);
            $entityManager->flush();

            //Enregistrerment du log
            $this->log->addLog($action);
            $this->gestionMedia->removeUpload($ancienMedia, 'media');
            $this->addFlash('succes', "Le bien".$bienSupprime->getTitre()." a bien été supprimé.");
        }

        return $this->redirectToRoute('bien_index');
    }
}
