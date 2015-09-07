<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Entity\Attachment;

class AttachmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('attachment', 'file', array(
                 'label' => false,
                 'attr' => array(
                    'placeholder' => 'Attachment'
                )
            ));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Attachment'
        ));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'validation_groups' => array('attachment')
        );
    }

    public function getName()
    {
        return 'attachment';
    }
}
