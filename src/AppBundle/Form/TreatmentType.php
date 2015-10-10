<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Doctrine\ORM\EntityRepository;

use AppBundle\Entity\Attachment;

class TreatmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('treatmentCategory', 'entity', array(
                'label' => 'Treatment',
                'attr' => array(
                   'placeholder' => 'Treatment'
                ),
                'class' => 'AppBundle:TreatmentCategory',
                'group_by' => 'categoryName',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('t')
                        ->innerJoin('AppBundle:TreatmentCategory', 'tc')
                        ->andWhere('t.parent IS NOT NULL')
                        ->orderBy('tc.updated', 'DESC');
                },
                'choice_label' => 'label',
                'multiple' => false,
                'expanded' => false,
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
            'data_class' => 'AppBundle\Entity\Treatment',
            'business' => null
        ));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'validation_groups' => array('treatment'),
            'business' => null
        );
    }

    public function getName()
    {
        return 'treatment';
    }
}
