<?php

namespace App\Controller;


use App\Entity\Participant;
use App\Form\ParticipantFormType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantController extends AbstractController
{

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/Participant/update/{id}", name="participant_update")
     */

    public function update(EntityManagerInterface $entityManager,
                           Request                $request,
                           Participant            $participant): \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {

        $formulaire = $this->createForm(ParticipantFormType::class, $participant);

        $formulaire->handleRequest($request);

        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute("accueil");
        }
        return $this->renderForm("participant/modifierProfil.html.twig", compact("formulaire"));
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/Participant/afficherProfil/{id}", name="participant_afficherProfil")
     */
    public function afficherProfil(Participant $participant ){

        return $this->render("participant/afficherProfil.html.twig", compact("participant"));

    }


    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route ("/Participant/listeParticipants", name="participant_listeParticipants")
     */
    public function listeParticipants(ParticipantRepository $participantRepository): \Symfony\Component\HttpFoundation\Response
    {
        $listeParticipants = $participantRepository->findAll();
        return $this->render('participant/listeParticipants.html.twig', [
            'controller_name' => 'ParticipantController',
            "listeParticipants" => $listeParticipants,
        ]);
    }

    }

