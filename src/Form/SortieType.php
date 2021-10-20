<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',null,[

                "label"=> "Nom de la sortie :"
            ])
            ->add('dateHeureDebut',DateTimeType::class, [
                'widget' => 'single_text',
                "label"=> "Date et heure de la sortie :",
                'attr'   => ['min' => ( new \DateTime() )->format('d-m-Y H:i')]
            ])
            ->add('dateLimiteInscription',DateType::class, [
                'widget' => 'single_text',
                "label"=> "Date limite d'inscription :"
            ])
            ->add('nbInscriptionsMax',null,[
                'attr'   => ['min' => "0"],
                "label"=> "Nombre de places :"
            ])
            ->add('duree',null,[
                'attr'   => ['min' => "0"],
                "label"=> "Durée :"
            ])


            ->add('infosSortie',null,[

                "label"=> "Description et infos :"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
