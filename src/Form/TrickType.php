<?php

namespace App\Form;

use App\Entity\Tricks;
use App\Entity\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre'
            ])
            ->add('content', TextType::class, [
                'label' => 'Description'
            ])
            ->add('img', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false
            ])
            ->add('video', TextType::class, [
                'label' => 'Lien youtube'
            ])
            ->add('typeId', EntityType::class, [
                'class' => Type::class,
                'choice_label' => 'name',
                'label' => 'Type'
            ])
            ->add('send', SubmitType::class, [
                'label' => 'Ajouter'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tricks::class,
        ]);
    }
}