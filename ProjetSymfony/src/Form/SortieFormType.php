<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomSortie')
            ->add('dateHeureDebut')
            ->add('dateLimiteInscription')
            ->add('nbInscriptionMax')
            ->add('duree')
            ->add('infoSortie')
           // ->add('site',TextType::class)


         /*    ->add('ville',EntityType::class,[ 'class'=>Ville::class,
                 'choice_label'=>function($ville){
                     return $ville->getNomVille();
                 }])*/
             /*   ->add('lieu',EntityType::class,[ 'class'=>Lieu::class,
                    'choice_label'=>function($lieu){
                        return $lieu->getNomLieu();
                }])*/

            //->add('etatSortie')
            /*->add('lieu',EntityType::class,[
                            'class'=>Lieu::class,
                            'choice_label'=>function($lieu){
                    return $lieu->getNomLieu();

                }])*/
          /*  ->add('',EntityType::class,[ 'class'=>Lieu::class,
                 'choice_label'=>function($lieu){
                     return $lieu->getLongitude();

                 }])*/
           /*-
             ->add('lieu',EntityType::class,[ 'class'=>Lieu::class,
                 'choice_label'=>function($lieu){
                     return $lieu->getVille();

                 }])*/
            //->add('urlPhoto')

            /*->add('etat',EntityType::class,[ 'class'=>Etat::class,
                'choice_label'=>function($etat){
                    return $etat->getLibelle();
                }])*/

            //->add('organisateur')
            //->add('participant')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
