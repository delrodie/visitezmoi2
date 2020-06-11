<?php

namespace App\Controller;

use App\Entity\Famille;
use App\Form\FamilleType;
use App\Repository\FamilleRepository;
use App\Utilities\GestionLog;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/famille")
 */
class FamilleController extends AbstractController
{
    private $log;

    public function __construct(GestionLog $log)
    {
        $this->log = $log;
    }

    /**
     * @Route("/", name="famille_index", methods={"GET","POST"})
     */
    public function index(Request $request, FamilleRepository $familleRepository): Response
    {
        $famille = new Famille();
        $form = $this->createForm(FamilleType::class, $famille);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Creation du slug du parteniaire
            $slugify = new Slugify();
            $slug = $slugify->slugify($famille->getLibelle());
            $famille->setSlug($slug);

            //Verification d'existence du slide
            $verif = $familleRepository->findOneBy(['libelle'=>$famille->getLibelle()]);
            if ($verif){
                $this->addFlash('danger', "Oups! Cette famille existe déjà. Merci d'en enregistrer une autre");

                return $this->redirectToRoute("famille_index");
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($famille);
            $entityManager->flush();

            //Enregistrerment du log
            $action = $this->getUser()->getUsername()." a enregistré la famille ".$famille->getLibelle();
            $this->log->addLog($action);

            // Message flash
            $this->addFlash('success', "La famille ".$famille->getLibelle()." a bien été enregistrée");

            return $this->redirectToRoute('famille_index');
        }

        return $this->render('famille/index.html.twig', [
            'familles' => $familleRepository->findBy([], ['id'=>'ASC']),
            'famille' => $famille,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="famille_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $famille = new Famille();
        $form = $this->createForm(FamilleType::class, $famille);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($famille);
            $entityManager->flush();

            return $this->redirectToRoute('famille_index');
        }

        return $this->render('famille/new.html.twig', [
            'famille' => $famille,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="famille_show", methods={"GET"})
     */
    public function show(Famille $famille): Response
    {
        return $this->render('famille/show.html.twig', [
            'famille' => $famille,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="famille_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Famille $famille, FamilleRepository $familleRepository): Response
    {
        $form = $this->createForm(FamilleType::class, $famille);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Creation du slug du parteniaire
            $slugify = new Slugify();
            $slug = $slugify->slugify($famille->getLibelle());
            $famille->setSlug($slug);

            $this->getDoctrine()->getManager()->flush();

            //Enregistrerment du log
            $action = $this->getUser()->getUsername()." a modifié la famille ".$famille->getLibelle();
            $this->log->addLog($action);

            // Message flash
            $this->addFlash('success', "La famille ".$famille->getLibelle()." a bien été modifiée");

            return $this->redirectToRoute('famille_index');
        }

        //Enregistrerment du log
        $action = $this->getUser()->getUsername()." a tenté de modifier la famille ".$famille->getLibelle();
        $this->log->addLog($action);

        return $this->render('famille/edit.html.twig', [
            'familles' => $familleRepository->findBy([], ['id'=>'ASC']),
            'famille' => $famille,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="famille_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Famille $famille): Response
    {
        if ($this->isCsrfTokenValid('delete'.$famille->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($famille);
            $entityManager->flush();
        }

        return $this->redirectToRoute('famille_index');
    }
}
