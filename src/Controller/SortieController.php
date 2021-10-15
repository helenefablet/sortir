<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'sortie_index', methods: ['GET'])]
    public function index(SortieRepository $sortieRepository): Response
    {
        return $this->render('sortie/index.html.twig', [
            'sorties' => $sortieRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'sortie_new', methods: ['GET','POST'])]
    public function new(Request $request, EtatRepository $etatRepository): Response
    {
       //dd('a');
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);

        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $sortie->setCampus($this->getUser()->getCampus());
            $sortie->setOrganisateur($this->getUser());
            $sortie->setLieu($form->get("lieu")->getData());


            $newSortie = $request->get("newSortie");
            if ($newSortie==0){
                $etat = $etatRepository->findOneBy(["id"=>"1"]);

                $sortie->setEtat($etat);


            }elseif ($newSortie==1){
                $etat = $etatRepository->findOneBy(["id"=>"2"]);
                $sortie->setEtat($etat);


            }

            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('accueil', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sortie/new.html.twig',
            [
                "formS"=>$form->createView()
            ]);
    }

    #[Route('/{id}', name: 'sortie_show', methods: ['GET'])]
    public function show(Sortie $sortie): Response
    {
        return $this->render('accueil/index.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    #[Route('/{id}/edit', name: 'sortie_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Sortie $sortie): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('accueil', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('accueil/index.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'sortie_delete', methods: ['POST'])]
    public function delete(Request $request, Sortie $sortie): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($sortie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('accueil', [], Response::HTTP_SEE_OTHER);
    }
}
