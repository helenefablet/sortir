<?php

namespace App;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

class Service
{
    protected $em;
    protected $etatRepository;
    protected $sortierepository;

    public function __construct(EntityManagerInterface $em, EtatRepository $etatRepository, SortieRepository $sortieRepository){

        $this->em = $em;
        $this->etatRepository = $etatRepository;
        $this->sortieRepository = $sortieRepository;

    }

    public function changeEtatSortieAuto()
    {

        $sorties = $this->sortieRepository->findAll();
        $currentDate = new \DateTime();

        if (!empty($sorties)) {

            foreach ($sorties as $value) {

                $dureeSortie = $value->getDuree();
                dump($dureeSortie);

                //Etat "Clôturée"(date limite d'inscription atteinte ou nombreMax atteint)
                if (($currentDate > $value->getDateLimiteInscription()) || ($value->getNbInscriptionsMax() == $value->getParticipants()->count())) {
                    $value->setEtat($this->etatRepository->find(3));

                    //Etat "en cours" (date et hre actuelles >= date et heure de début de la sortie)
                } elseif ($currentDate >= $value->getDateHeureDebut()) {
                    $value->setEtat($this->etatRepository->find(4));

                    //Etat "Passée"(date et hre actuelles > (date et heure de début de la sortie + durée de la sortie en integer convertie en temps)
                } elseif ($currentDate > (($value->getDateHeureDebut())->add(new \DateInterval('PT' . $dureeSortie . 'M')))) {
                    $value->setEtat($this->etatRepository->find(5));

                }

            }

            //Enregistrement des modifications effectuées
            $this->em->persist($value);
            $this->em->flush();

        }
    }

    public function annulerSortie(Sortie $sortie){

        $sortie->setEtat($this->etatRepository->find(6));
        $this->em->persist($sortie);
        $this->em->flush();

    }


}