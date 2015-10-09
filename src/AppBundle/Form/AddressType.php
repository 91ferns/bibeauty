<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Entity\Address;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('street', 'text', array(
                'label' => false,
                'attr' => array(
                   'placeholder' => 'Street Address'
                )
            ))
            ->add('line2', 'text', array(
                 'label' => false,
                 'attr' => array(
                    'placeholder' => 'Apartment Number'
                ),
                'required' => false
            ))
            ->add('city', 'text', array(
                 'label' => false,
                 'attr' => array(
                    'placeholder' => 'City'
                ),
                'data' => 'Los Angeles',
                'disabled' => true,
            ))
            ->add('state', 'choice', array(
                 'label' => false,
                 'attr' => array(
                    'placeholder' => 'State'
                 ),
                 'choices' => Address::getStates(),
                 'data' => 'CA',
                 'disabled' => true,
                 'multiple' => false,
                 'expanded' => false,
            ))
            ->add('zip', 'text', array(
                 'label' => false,
                 'attr' => array(
                    'placeholder' => 'Zip'
                 )
            ))
            ->add('country', 'choice', array(
                 'label' => false,
                 'attr' => array(
                    'placeholder' => 'Country'
                 ),
                 'choices' => Address::getCountries(),
                 'multiple' => false,
                 'expanded' => false,
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Address'
        ));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'validation_groups' => array('addresses')
        );
    }

    public function getName()
    {
        return 'address';
    }
}
