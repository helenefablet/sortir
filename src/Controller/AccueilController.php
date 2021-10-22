<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use App\Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile')]
class AccueilController extends AbstractController
{
    #[Route('/accueil', name: 'accueil')]
    public function index(SortieRepository $sortieRepository, Service $service): Response
    {
        $sorties = $sortieRepository->findAll();
        #Gestion du changement des états des sorties au chargement de la page
        $service->changeEtatSortieAuto();
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
            "sorties" => $sorties,
        ]);
    }
}
