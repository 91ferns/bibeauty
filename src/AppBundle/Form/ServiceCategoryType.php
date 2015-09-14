<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', 'text', array(
                 'label' => 'Name',
                 'attr' => array(
                    'placeholder' => 'Category Name'
                )
            ));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ServiceCategory'
        ));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'validation_groups' => array('service_category')
        );
    }

    public function getName()
    {
        return 'service_category';
    }
}
