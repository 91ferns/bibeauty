<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BusinessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => false,
                'attr' => array(
                   'placeholder' => 'Name'
                )
             ))
             ->add('description', 'text', array(
                 'label' => false,
                 'attr' => array(
                    'placeholder' => 'Description'
                 )
              ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Business'
        ));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'validation_groups' => array('businesses')
        );
    }

    public function getName()
    {
        return 'signup';
    }
}
