<?php

namespace App\Controller;

use App\Entity\Slide;
use App\Form\SlideType;
use App\Repository\SlideRepository;
use App\Utilities\GestionLog;
use App\Utilities\GestionMedia;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/slide")
 */
class SlideController extends AbstractController
{
    private $log;
    private $gestionMedia;
    private $paginator;

    public function __construct(GestionMedia $gestionMedia, GestionLog $log, PaginatorInterface $paginator)
    {
        $this->log = $log;
        $this->gestionMedia = $gestionMedia;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/", name="slide_index", methods={"GET","POST"})
     */
    public function index(Request $request, SlideRepository $slideRepository): Response
    {
        $slide = new Slide();
        $form = $this->createForm(SlideType::class, $slide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Verification d'existence du slide
            $verif = $slideRepository->findOneBy(['titre'=>$slide->getTitre()]);
            if ($verif){
                $this->addFlash('danger', "Oups! Ce fichier existe déjà. Merci d'en enregistrer un autre ou de changer de titre");
            }

            // Gestion des fichiers
            $mediaFile = $form->get('media')->getData();

            // Traitement du fichier s'il a été telechargé
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'slide_media');

                $slide->setMedia($media);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($slide);
            $entityManager->flush();

            //Enregistrerment du log
            $action = $this->getUser()->getUsername()." a enregistré le slide ".$slide->getTitre();
            $this->log->addLog($action);

            // Message flash
            $this->addFlash('success', "Le slide ".$slide->getTitre()." a bien été enregistré");

            return $this->redirectToRoute('slide_index');
        }

        //Enregistrerment du log
        $action = $this->getUser()->getUsername()." a consulter la liste des slides ";
        $this->log->addLog($action);

        $slideListe = $slideRepository->findBy([],['id'=>'DESC']);
        $slides = $this->paginator->paginate(
            $slideListe,
            $request->query->getInt('page', 1), 4
        );


        return $this->render('slide/index.html.twig', [
            'slides' => $slides,
            'slide' => $slide,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="slide_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $slide = new Slide();
        $form = $this->createForm(SlideType::class, $slide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($slide);
            $entityManager->flush();

            return $this->redirectToRoute('slide_index');
        }

        return $this->render('slide/new.html.twig', [
            'slide' => $slide,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="slide_show", methods={"GET"})
     */
    public function show(Slide $slide): Response
    {
        return $this->render('slide/show.html.twig', [
            'slide' => $slide,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="slide_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Slide $slide, SlideRepository $slideRepository): Response
    {
        $form = $this->createForm(SlideType::class, $slide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Gestion des fichiers
            $mediaFile = $form->get('media')->getData();
            $ancienMedia = $request->get('ancien_media');

            // Traitement du fichier s'il a été telechargé
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'slide_media');

                $slide->setMedia($media);
                $this->gestionMedia->removeUpload($ancienMedia, 'slide_media');
            }

            $this->getDoctrine()->getManager()->flush();

            //Enregistrerment du log
            $action = $this->getUser()->getUsername()." a modifié le slide ".$slide->getTitre();
            $this->log->addLog($action);

            // Message flash
            $this->addFlash('success', "Le slide ".$slide->getTitre()." a bien été modifié");

            return $this->redirectToRoute('slide_index');
        }

        //Enregistrerment du log
        $action = $this->getUser()->getUsername()." a tenté de modifier le slide ".$slide->getTitre();
        $this->log->addLog($action);

        $slideListe = $slideRepository->findBy([],['id'=>'DESC']);
        $slides = $this->paginator->paginate(
            $slideListe,
            $request->query->getInt('page', 1), 4
        );

        return $this->render('slide/edit.html.twig', [
            'slides' => $slides,
            'slide' => $slide,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="slide_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Slide $slide): Response
    {
        if ($this->isCsrfTokenValid('delete'.$slide->getId(), $request->request->get('_token'))) {

            $action = $this->getUser()->getUsername()." a supprimer le slide ".$slide->getTitre();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($slide);
            $entityManager->flush();

            //Enregistrerment du log
            $this->log->addLog($action);

            // Message flash
            $this->addFlash('success', "Le slide ".$slide->getTitre()." a bien été supprimé");
        }

        return $this->redirectToRoute('slide_index');
    }
}
