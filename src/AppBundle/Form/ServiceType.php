<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Doctrine\ORM\EntityRepository;

use AppBundle\Entity\Attachment;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('serviceCategory', 'entity', array(
                'label' => 'Category',
                'attr' => array(
                   'placeholder' => 'Category'
                ),
                'class' => 'AppBundle:ServiceCategory',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('c')
                        ->where('c.business = :business')
                        ->setParameter('business', $options['business'])
                        ->orderBy('c.updated', 'DESC');
                },
                'choice_label' => 'label',
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
            ))
            ->add('hours', 'integer', array(
                 'label' => 'Hours',
                 'attr' => array(
                    'placeholder' => 'Hours'
                )
            ))
            ->add('minutes', 'integer', array(
                 'label' => 'Minutes',
                 'attr' => array(
                    'placeholder' => 'Minutes'
                )
            ))
            ->add('currentPrice', 'money', array(
                 'label' => 'Discounted Price',
                 'attr' => array(
                    'placeholder' => '0.00'
                ),
                'currency' => 'USD'
            ))
            ->add('originalPrice', 'money', array(
                 'label' => 'Full Price',
                 'attr' => array(
                    'placeholder' => '0.00'
                ),
                'currency' => 'USD'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Service',
            'business' => null
        ));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'validation_groups' => array('service'),
            'business' => null
        );
    }

    public function getName()
    {
        return 'service';
    }
}
