<?php

namespace App\Controller;

use App\Entity\Fond;
use App\Form\FondType;
use App\Repository\FondRepository;
use App\Utilities\GestionLog;
use App\Utilities\GestionMedia;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/fond")
 */
class FondController extends AbstractController
{
    private $log;
    private $gestionMedia;
    private $paginator;

    public function __construct(GestionMedia $gestionMedia, GestionLog $log, PaginatorInterface $paginator)
    {
        $this->gestionMedia = $gestionMedia;
        $this->log = $log;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/", name="fond_index", methods={"GET","POST"})
     */
    public function index(Request $request, FondRepository $fondRepository): Response
    {
        $fond = new Fond();
        $form = $this->createForm(FondType::class, $fond);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Verification d'existence du slide
            $verif = $fondRepository->findOneBy(['titre'=>$fond->getTitre()]);
            if ($verif){
                $this->addFlash('danger', "Oups! Ce fichier existe déjà. Merci d'en enregistrer un autre ou de changer de titre");

                return $this->redirectToRoute("fond_index");
            }

            // Gestion des fichiers
            $mediaFile = $form->get('media')->getData();

            // Traitement du fichier s'il a été telechargé
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'fond_media');

                $fond->setMedia($media);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($fond);
            $entityManager->flush();

            //Enregistrerment du log
            $action = $this->getUser()->getUsername()." a enregistré le fond de catégorie ".$fond->getTitre();
            $this->log->addLog($action);

            // Message flash
            $this->addFlash('success', "Le fond de catégorie: ".$fond->getTitre()." a bien été enregistré");

            return $this->redirectToRoute('fond_index');
        }

        $fondListe = $fondRepository->findBy([],['id'=>'DESC']);
        $fonds = $this->paginator->paginate(
            $fondListe,
            $request->query->getInt('page', 1), 4
        );

        //Enregistrerment du log
        $action = $this->getUser()->getUsername()." a consulter la liste des fonds de catégories ";
        $this->log->addLog($action);

        return $this->render('fond/index.html.twig', [
            'fonds' => $fonds,
            'fond' => $fond,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="fond_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $fond = new Fond();
        $form = $this->createForm(FondType::class, $fond);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($fond);
            $entityManager->flush();

            return $this->redirectToRoute('fond_index');
        }

        return $this->render('fond/new.html.twig', [
            'fond' => $fond,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="fond_show", methods={"GET"})
     */
    public function show(Fond $fond): Response
    {
        return $this->render('fond/show.html.twig', [
            'fond' => $fond,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="fond_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Fond $fond, FondRepository $fondRepository): Response
    {
        $form = $this->createForm(FondType::class, $fond);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Gestion des fichiers
            $mediaFile = $form->get('media')->getData();
            $ancienMedia = $request->get('ancien_media');

            // Traitement du fichier s'il a été telechargé
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'fond_media');

                $fond->setMedia($media);
                $this->gestionMedia->removeUpload($ancienMedia, 'fond_media');
            }

            $this->getDoctrine()->getManager()->flush();

            //Enregistrerment du log
            $action = $this->getUser()->getUsername()." a modifié le fond ".$fond->getTitre();
            $this->log->addLog($action);

            // Message flash
            $this->addFlash('success', "Le fond ".$fond->getTitre()." a bien été modifié");

            return $this->redirectToRoute('fond_index');
        }

        $fondListe = $fondRepository->findBy([],['id'=>'DESC']);
        $fonds = $this->paginator->paginate(
            $fondListe,
            $request->query->getInt('page', 1), 4
        );

        //Enregistrerment du log
        $action = $this->getUser()->getUsername()." a tenté de modifier le fond ".$fond->getTitre();
        $this->log->addLog($action);

        return $this->render('fond/edit.html.twig', [
            'fonds' => $fonds,
            'fond' => $fond,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="fond_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Fond $fond): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fond->getId(), $request->request->get('_token'))) {

            $ancienMedia = $request->get('ancien_media');

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($fond);
            $entityManager->flush();

            // Suppression du fichier
            if ($ancienMedia){
                $this->gestionMedia->removeUpload($ancienMedia, 'fond_media');
            }

            //Enregistrerment du log
            $action = $this->getUser()->getUsername()." a supprimé le fond ".$fond->getTitre();
            $this->log->addLog($action);
        }

        return $this->redirectToRoute('fond_index');
    }
}
