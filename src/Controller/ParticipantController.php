<?php

namespace App\Controller;


use App\Entity\Image;
use App\Entity\Participant;
use App\Form\ChangePasswordFormType;
use App\Form\ParticipantFormType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

            //TRAITEMENT IMAGES

            //récupération des images
            $images = $formulaire->get('images')->getData();

            //Attribut d'un nom de fichier
            foreach ($images as $image) {
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                //Copie dans le fichier uploads
                $image->move(
                    $this->getParameter('images_directory')
                );


                //Stockage en base de données
                $img = new Image();
                $img->setNom($fichier);
                $participant->addImage($img);

            }

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

    #[Route('/participant/delete/{id}', name: 'participant_delete')]
    public function delete(Request $request, Participant $participant): Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($participant);
        $entityManager->flush();


        return $this->redirectToRoute("participant_afficherProfil", [

        ], Response::HTTP_SEE_OTHER);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/Participant/modifierMDP/{id}", name="participant_modifierMDP")
     */

    public function modifierMDP(EntityManagerInterface $entityManager,
                           Request                $request,
                           Participant            $participant): \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {

        $formulaire = $this->createForm(ChangePasswordFormType::class, $participant);
        $formulaire->handleRequest($request);

        if ($formulaire->isSubmitted() && $formulaire->isValid() && $participant->getPassword() == $request->get('AncienMDP')) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute("accueil");
        }
        return $this->renderForm("participant/modifierMotDePasse.html.twig", compact("formulaire"));

    }

    }

