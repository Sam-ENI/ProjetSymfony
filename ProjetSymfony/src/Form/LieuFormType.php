<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
         ->add('ville',EntityType::class,[ 'class'=>Ville::class,
                'placeholder'=>'Choisir une ville',
                'choice_label'=>function($ville){
                    return $ville->getNomVille();
                }])

          /*  ->add('rue', EntityType::class,[
                'class'=>Lieu::class,
                'choice_label'=>function($lieu) {
                    return $lieu->getRue();
                }])
            ->add('latitude', EntityType::class,[
                'class'=>Lieu::class,
                'choice_label'=>function($lieu) {
                    return $lieu->getLatitude();
                }])
            ->add('longitude', EntityType::class,[
                'class'=>Lieu::class,
                'choice_label'=>function($lieu) {
                    return $lieu->getLongitude();
                }])*/

            //->add('ville')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
