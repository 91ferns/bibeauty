<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Entity\Therapist;

class TherapistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'First and last name',
                'attr' => array(
                   'placeholder' => 'Name'
               ),
               'required' => true
            ))
            ->add('title', 'text', array(
                 'label' => 'Job Title',
                 'attr' => array(
                    'placeholder' => 'title'
                ),
                'required' => false
            ))
            ->add('phone', 'text', array(
                 'label' => 'Phone number',
                 'attr' => array(
                    'placeholder' => 'Phone number'
                ),
                'required' => false
            ))
            ->add('email', 'email', array(
                 'label' => 'Email address',
                 'attr' => array(
                    'placeholder' => 'Email address'
                 ),
                 'required' => false
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Therapist'
        ));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'validation_groups' => array('therapists')
        );
    }

    public function getName()
    {
        return 'therapist';
    }
}
