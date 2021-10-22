<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

#[Route('/sortie')]
class SortieController extends AbstractController
{
    // Fonction api tableau lieux et tableau villes
    /**
     * @Route ("/newLiaison", name="_niewLiaison")
     */
    public function api (LieuRepository $repoL, VilleRepository $repoV) : Response
    {
        $lieux = $repoL->findAll();
        $tab_lieux = [];
        foreach ($lieux as $l)
        {
            $info_l['id'] = $l->getId();
            $info_l['nom'] = $l->getNom();
            $info_l['rue'] = $l->getRue();
            $info_l['latitude'] = $l->getLatitude();
            $info_l['longitude'] = $l->getLongitude();
            $info_l['ville'] = $l->getVille()->getId();

            $tab_lieux[] = $info_l;
        }

        $villes = $repoV->findAll();
        $tab_Villes = [];
        foreach ($villes as $v)
        {
            $info_v['id'] = $v->getId();
            $info_v['nom'] = $v->getNom();
            $info_v['code_postal'] = $v->getCodePostal();

            $tab_Villes[] = $info_v;
        }
        $tab['lieu'] = $tab_lieux;
        $tab['ville'] = $tab_Villes;

        return $this->json($tab);
    }

    #[Route('/new', name: 'sortie_new')]
    public function new(Request $request, EtatRepository $etatRepository, EntityManagerInterface $entityManager, LieuRepository $lieuRepository): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $sortie->setCampus($this->getUser()->getCampus());
            $sortie->setOrganisateur($this->getUser());

            $sortie->setLieu($lieuRepository->find($request->get('lieu')));

            //Gestion de l'état à la création

            $info = $request->get("info");

            //Etat "Créée"
            if ($info == 1){
                $sortie->setEtat($etatRepository->find(1));

            //Etat "Ouverte"
            }elseif ($info == 0){
                $sortie->setEtat($etatRepository->find(2));
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

    #[Route('afficher/{id}', name: 'sortie_show')]
    public function show(Sortie $sortie): Response
    {
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    #[Route('/edit/{id}', name: 'sortie_edit')]
    public function edit(Request $request, Sortie $sortie): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('accueil', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'sortie_delete')]
    public function delete(Request $request, Sortie $sortie): Response
    {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($sortie);
            $entityManager->flush();

        return $this->redirectToRoute("accueil", [

        ], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route ("/sinscrire/{id}", name="_sinscrire")
     */
    public function sinscrire(Sortie $sortie){

        $currentDate = new \DateTime('now');
        $dateLimite= $sortie->getDateLimiteInscription();
        $nbInscritMax = $sortie->getNbInscriptionsMax();
        $nbInscrit = $sortie->getParticipants()->count();

        if (( $dateLimite >= $currentDate) &&
                ( $nbInscritMax > $nbInscrit)){
            $user = $this->getUser();
            $sortie->addParticipant($user);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute("accueil", [

            ], Response::HTTP_SEE_OTHER);
        }else
            return $this->redirectToRoute("accueil", [
                'message'=>'Le nombre de participants maximum ou la date limite a été atteint !'

            ], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route ("/seDesister/{id}", name="_seDesister")
     */
    public function seDesister(Sortie $sortie){
        $user = $this->getUser();
        $sortie->removeParticipant($user);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute("accueil", [
        ], Response::HTTP_SEE_OTHER);
    }

    #[Route('/annulerSortie/{id}', name: 'sortie_annuler')]
    public function annulerSortie(Sortie $sortie, EtatRepository $etatRepository, )
    {
        $participants = $sortie->getParticipants();
        $entityManager = $this->getDoctrine()->getManager();
        $sortie->setEtat($etatRepository->find(6));

            foreach ($participants as $participant){
                $sortie->removeParticipant($participant);
            }

        $entityManager->persist($sortie);
        $entityManager->flush();
        return $this->redirectToRoute("accueil", [

        ], Response::HTTP_SEE_OTHER);

    }

    #[Route('/publierSortie/{id}', name: 'sortie_publier')]
    public function publierSortie(Sortie $sortie, EtatRepository $etatRepository)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $sortie->setEtat($etatRepository->find(2));
        $entityManager->persist($sortie);
        $entityManager->flush();
        return $this->redirectToRoute("accueil", [

        ], Response::HTTP_SEE_OTHER);

    }

}
