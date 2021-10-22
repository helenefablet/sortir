<?php

namespace App\Form;

use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',

            ])
            ->add('nom',null,[
                "label"=> "nom de la sortie",
                'required' => false,
            ])
            ->add('dateHeureDebut',DateType::class, [
                'widget' => 'single_text',
                "label"=> "Entre",
                'attr' => ['min' => ( new \DateTime() )->format('d-m-Y H:i')],
                'required' => false,
            ])
            ->add('dateHeureFin',DateType::class, [
                'widget' => 'single_text',
                "label"=> "et",
                'attr' => ['min' => ( new \DateTime() )->format('d-m-Y H:i')],
                'required' => false,
            ])
            ->add('organisateur', CheckboxType::class, [
                'label'    => 'Sorties dont je suis l organisateur/trice',
                'required' => false,
            ])
            ->add('inscrit', CheckboxType::class, [
            'label'    => 'Sorties auxquelles je suis inscrit/e',
            'required' => false,
            ])
            ->add('NonInscrit', CheckboxType::class, [
                'label'    => 'Sorties auxquelles je ne suis pas inscrit/e',
                'required' => false,
            ])
            ->add('passee', CheckboxType::class, [
                'label'    => 'Sorties passées',
                'required' => false,
            ])

        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
