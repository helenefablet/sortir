<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Participant;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/registration')]
class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $user = new Participant();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //ROLE_ADMIN
            if ($user->getAdministrateur() == 1){
                $user->setRoles(['ROLE_ADMIN']);
            }else{
                $user->setRoles(['ROLE_USER']);
            }

            // encode the plain password
            $user->setPassword(
            $userPasswordHasherInterface->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            //TRAITEMENT IMAGES

            //récupération des images
            $images = $form->get('images')->getData();

            //Attribut d'un nom de fichier
            foreach ($images as $image){

                $fichier = md5(uniqid()).'.'.$image->guessExtension();

                //Copie dans le fichier uploads
                    $image->move(
                    $this->getParameter('images_directory'),
                        $fichier
                );


                //Stockage en base de données
                $img = new Image();
                $img->setNom($fichier);
                $user->addImage($img);
            }



            $entityManager = $this->getDoctrine()->getManager();


            $entityManager->persist($user);
            $entityManager->flush();


            return $this->redirectToRoute('app_register');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
