<?php

namespace App\Controller;

use App\Form\FiltreType;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use App\Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile')]
class AccueilController extends AbstractController
{
    #[Route('/accueil', name: 'accueil')]
    public function index(Request $request,SortieRepository $sortieRepository,CampusRepository $campusRepository, Service $service): Response
    {
        $campus = $campusRepository->findAll();
        $sorties = $sortieRepository->findAll();
        #Gestion du changement des états des sorties au chargement de la page
        $service->changeEtatSortieAuto();

        $form =$this->createForm(FiltreType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() ) {



            $campusR = $form->get("campus")->getData();
            $sorties= $sortieRepository->findBy(["campus"=>$campusR]);

            $dateInf = $form->get("dateHeureDebut")->getData();

            $dateFin = $form->get("dateHeureFin")->getData();
            if(isset($dateInf) AND isset($dateFin)){
                $sorties= $sortieRepository->findByDateEntre($dateInf, $dateFin);
            }

            $organisateurCKB = $form->get("organisateur")->getData();
            if ($organisateurCKB){
                $sorties = $sortieRepository->findBy(["organisateur"=>$this->getUser()]);

            }
            $inscritCKB = $form->get("inscrit")->getData();
            if ($inscritCKB){

                $sorties = $sortieRepository->findByParticipants($this->getUser()->getId());

            }
            $passeeCKB = $form->get("passee")->getData();
            if ($passeeCKB){

                $sorties = $sortieRepository->findBy(["dateLimiteInscription"=>$this->getUser()]);

            }

            return $this->render('accueil/index.html.twig', [
                'controller_name' => 'AccueilController',
                "sorties" => $sorties,
                "campus" => $campus,
                "form" => $form->createView()

            ]);
        }

        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
            "sorties" => $sorties,
            "campus" => $campus,
            "form" => $form->createView()
        ]);
    }
}
