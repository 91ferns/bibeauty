<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Entity\Attachment;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('serviceCategory', 'choice', array(
                'label' => 'Category',
                'attr' => array(
                   'placeholder' => 'Category'
                ),
                'choices' => $options['categories'],
                'multiple' => false,
                'expanded' => false,
            ))
            ->add('label', 'text', array(
                 'label' => 'Treatment',
                 'attr' => array(
                    'placeholder' => 'Treatment Name'
                )
            ))
            ->add('description', 'textarea', array(
                 'label' => 'Description',
                 'attr' => array(
                    'placeholder' => 'Description'
                )
            ));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Service',
            'categories' => array()
        ));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'validation_groups' => array('service'),
            'categories' => array()
        );
    }

    public function getName()
    {
        return 'service';
    }
}
