<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Entity\Review;

class OperatingScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mondayStart', 'choice', array(
                'label' => 'Monday',
                'multiple' => false,
                'expanded' => false,
                'choices' => OperatingSchedule::getTimes(),
            ))
            ->add('mondayEnd', 'choice', array(
                'label' => false,
                'multiple' => false,
                'expanded' => false,
                'choices' => OperatingSchedule::getTimes(),
            ));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\OperatingSchedule'
        ));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'validation_groups' => array('operation')
        );
    }

    public function getName()
    {
        return 'operating_schedule';
    }
}
