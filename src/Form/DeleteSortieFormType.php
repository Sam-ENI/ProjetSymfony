<?php


namespace App\Form;


use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteSortieForm extends AbstractType

{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('nomSortie')
            ->add('dateHeureDebut')
           ->add('site',EntityType::class,[ 'class'=>Site::class,
        'choice_label'=>function($site){
            return $site->getNomSite();
        }])
            ->add('infoSortie');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}