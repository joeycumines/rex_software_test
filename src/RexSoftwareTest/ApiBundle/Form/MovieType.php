<?php

namespace RexSoftwareTest\ApiBundle\Form;


use RexSoftwareTest\ApiBundle\Entity\Movie;
use RexSoftwareTest\ApiBundle\Repository\RoleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieType extends AbstractType
{
    protected $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('image')
            ->add('rating')
            ->add('role_ids');

        // validate the fields manually
        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) {
                \Monolog\Handler\error_log(json_encode($event->getData()), true);
            }
        );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                \Monolog\Handler\error_log(json_encode($event->getData()), true);
//                $movie = $event->getData();
//                if (!$movie instanceof Movie) {
//                    return;
//                }
                //$event->getForm()->get('name')->addError(new FormError(''));
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Movie::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'rexsoftwaretest_apibundle_movie';
    }
}
