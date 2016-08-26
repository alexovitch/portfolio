<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('contenu')
            ->add('date', 'datetime')
            ->add('publication')
            ->add('image', new ImageType(), ['required'=>false, 'label'=>'Choisissez une image'])
            ->add('mini', CheckboxType::class, ['required'=>false, 'label'=>'Créer une miniature'])
            ->add('categorie',EntityType::class, [
                'label' => 'Categorie :',
                "class"=>"AppBundle:Categorie",
                "property"=>"title",
                "multiple"=>false,
                "expanded"=>false
        ])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Article'
        ));
    }
}
