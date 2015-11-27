<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BusinessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('headerAttachment', new AttachmentType(), array(
                'required' => false,
                'label' => 'Upload a pic of your work station / salon'
            ))
            ->add('logoAttachment', new AttachmentType(), array(
                'required' => false,
                'label' => 'Upload a profile pic of you (must be a pic of you)'
            ))
            ->add('name', 'text', array(
                'label' => false,
                'attr' => array(
                   'placeholder' => 'Business Name'
                )
             ))
              ->add('address', new AddressType(), array(
                  'label' => false
              ))
              ->add('landline', 'text', array(
                   'label' => false,
                   'attr' => array(
                      'placeholder' => 'Landline Phone Number'
                  ),
                  'required' => false
              ))
              ->add('mobile', 'text', array(
                   'label' => false,
                   'attr' => array(
                      'placeholder' => 'Mobile Phone Number'
                  ),
                  'required' => false
              ))
               ->add('description', 'textarea', array(
                   'label' => false,
                   'attr' => array(
                      'placeholder' => 'Business Overview: Tell customers a little about what your business does and what makes it stand out :)'
                   )
                ))
                ->add('yelpLink', 'url', array(
                    'label' => false,
                    'attr' => array(
                        'placeholder' => 'Yelp Link'
                    ),
                    'required' => true
                ))
              ->add('email', 'email', array(
                  'label' => false,
                  'attr' => array(
                      'placeholder' => 'Email Address'
                  )
              ))
              ->add('website', 'url', array(
                  'label' => false,
                  'attr' => array(
                      'placeholder' => 'Website'
                  ),
                  'required' => false
              ))
              /*->add('operation', new OperatingScheduleType(), array(
                  'label' => 'Opening Times'
              ))*/
              ->add('acceptsCredit', 'checkbox', array(
                  'label' => 'Accepts credit',
                  'required' => true
              ))
              ->add('acceptsCash', 'checkbox', array(
                  'label' => 'Accepts cash',
                  'required' => true
              ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Business'
        ));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'validation_groups' => array('businesses')
        );
    }

    public function getName()
    {
        return 'signup';
    }
}
