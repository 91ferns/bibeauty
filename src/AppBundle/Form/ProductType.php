<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Doctrine\ORM\EntityRepository;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => false,
                'attr' => array(
                   'placeholder' => 'Item Name'
                )
            ))
            ->add('url', 'url', array(
                'label' => false,
                'attr' => array(
                    'placeholder' => 'Redirection URL'
                )
            ))
            ->add('brand', 'entity', array(
                'label' => false,
                'attr' => array(
                    'placeholder' => 'Brand'
                ),
                'required' => true,
                'property' => 'label',
                'class' => 'AppBundle:ProductBrand',
                'query_builder' => function(EntityRepository $repository) {
                    // This will return a query builder selecting all universities
                    return $repository->createQueryBuilder('b');
                }
            ))
            ->add('thumbnail', new AttachmentType(), array(
                'required' => false,
                'label' => 'Upload a pic of the item'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Product'
        ));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'validation_groups' => array('products')
        );
    }

    public function getName()
    {
        return 'product';
    }
}
