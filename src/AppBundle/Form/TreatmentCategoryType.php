<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Doctrine\ORM\EntityRepository;

use AppBundle\Entity\Attachment;

class TreatmentCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', 'entity', array(
                'label' => 'Treatment Categories',
                'attr' => array(
                   'placeholder' => 'Treatment Categories'
                ),
                'class' => 'AppBundle:TreatmentCategory',
                'query_builder' => function (EntityRepository $er) use ($options){
                    return $er->createQueryBuilder('sc')
                        ->leftJoin('AppBundle:Business','b')
                        ->where('b.id != :businessId')
                        ->setParameter('businessId',$options['business'])
                        ->orderBy('sc.label', 'DESC');
                },
                'choice_label' => 'label',
                'multiple' => 'multiple',
                'expanded' => false,
            ))
            /*->add('label', 'text', array(
                 'label' => 'Name',
                 'attr' => array(
                    'placeholder' => 'Category Name'
                )
            ));*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\TreatmentCategory',
            'business'   => '',
        ));
    }

    public function getDefaultOptions(array $options) {
        return array(
            //'validation_groups' => array('service_category'),
            'business'=>'',
        );
    }

    public function getName()
    {
        return 'treatment_category';
    }
}
