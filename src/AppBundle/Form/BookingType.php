<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Entity\Booking;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['user']) {
            $user = $options['user'];

            $user = (object) array(
                'name' => $user->getFullName(),
                'email' => $user->getEmail(),
                'phone' => 'sadas'
            );

        } else {
            $user = (object) array(
                'name' => '',
                'email' => '',
                'phone' => ''
            );
        }

        $builder
            ->add('name', 'text', array(
                'label' => 'Full Name',
                'attr' => array(
                   'placeholder' => 'Jane Doe'
                ),
                'required' => true,
                'data' => $user->name,
                'attr' => array(
                    'class' => 'input-lg'
                )
            ))
            ->add('email', 'email', array(
                 'label' => 'Email',
                 'attr' => array(
                    'placeholder' => 'jane@example.com'
                ),
                'required' => true,
                'data' => $user->email,
                'attr' => array(
                    'class' => 'input-lg'
                )
            ))
            ->add('phone', 'text', array(
                 'label' => 'Phone Number',
                 'attr' => array(
                    'placeholder' => '(555) 123-4567'
                ),
                'required' => true,
                'data' => $user->phone,
                'attr' => array(
                    'class' => 'input-lg'
                )
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Booking',
            'user' => null,
            'availability' => true,
        ));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'validation_groups' => array('booking'),
            'user' => null
        );
    }

    public function getName()
    {
        return 'booking';
    }
}
