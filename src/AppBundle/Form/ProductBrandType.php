<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Doctrine\ORM\EntityRepository;

class ProductBrandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', 'text', array(
                'label' => false,
                'attr' => array(
                   'placeholder' => 'Label'
                )
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ProductBrand'
        ));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'validation_groups' => array('products-brand')
        );
    }

    public function getName()
    {
        return 'product_brand';
    }
}
