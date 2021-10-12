<?php

namespace App\Controller;


use App\Entity\Participant;
use App\Form\ParticipantFormType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantController extends AbstractController
{

    /**
     * @Route("/Participant/update/{id}", name="participant_update")
     */

    public function update(EntityManagerInterface $entityManager,
    Request $request,
    Participant $participant): \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {

        $formulaire = $this->createForm(ParticipantFormType::class, $participant);

        $formulaire->handleRequest($request);

        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute("accueil", compact("formulaire"));
        }
        return $this->renderForm("participant/modifierProfil.html.twig", compact("formulaire"));
    } }


