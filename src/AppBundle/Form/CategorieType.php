<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parent',null,['label'=>'Parent: '])
            ->add('title',null,['label'=>'Titre: '])
            ->add('description',null,['label'=>'Description: '])
            ->add('link',null,['label'=>'Lien: '])
            ->add('icon',null,['label'=>'Icon: '])
            ->add('image', new ImageType(), ['required'=>false, 'label'=>'Choisissez une image'])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Categorie'
        ));
    }
}
