<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Router;
use Doctrine\ORM\EntityRepository;

use AppBundle\Entity\Attachment;

class TreatmentType extends AbstractType
{
    /*function __construct(Router $router)
    {
        $this->router = $router;
    }*/

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
            ->add('name', 'text', array(
                 'label' => 'In a few words, what is the treatment?',
                 'attr' => array(
                    'placeholder' => 'Name'
                )
            ))
            ->add('description', 'textarea', array(
                 'label' => 'Tell your customers the details of what they would be purchasing',
                 'attr' => array(
                    'placeholder' => 'Description'
                )
            ))
            ->add('duration', 'integer', array(
                 'label' => 'Duration (in minutes)',
                 'attr' => array(
                    'placeholder' => 'Duration'
                )
            ))
            ->add('originalPrice', 'money', array(
                'label' => 'Original Price',
                'attr' => array(
                    'placeholder' => 'Original Price',
                ),
                'currency' => 'USD',
            ))
            //->setAction($this->router->generate('admin_business_treatments_edit_path'))
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
