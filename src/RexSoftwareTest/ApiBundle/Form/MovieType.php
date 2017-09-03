<?php

namespace RexSoftwareTest\ApiBundle\Form;


use Doctrine\Common\Collections\ArrayCollection;
use RexSoftwareTest\ApiBundle\Entity\Movie;
use RexSoftwareTest\ApiBundle\Entity\Role;
use RexSoftwareTest\ApiBundle\Form\EventListener\CollectionChangeSubscriber;
use RexSoftwareTest\ApiBundle\Repository\RoleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('image', TextType::class, [
                'required' => false,
            ])
            ->add('rating', NumberType::class, [
                'required' => false,
            ])
            ->add('role_ids', CollectionType::class, [
                'entry_type' => IntegerType::class,
                'required' => false,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ]);

        // validate the input
        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) {
                if (true === $event->getForm()->has('rating')) {
                    $localForm = $event->getForm()->get('rating');
                    if (null !== $localForm->getData()) {
                        try {
                            Movie::validateRating($localForm->getData());
                        } catch (\InvalidArgumentException $e) {
                            $localForm->addError(new FormError($e->getMessage()));
                        }
                    }
                }
            }
        );

        // hydrate the roles
        $builder->addEventSubscriber(new CollectionChangeSubscriber(
            $this->roleRepository,
            'id',
            'getId',
            Role::class,
            'getId',
            Movie::class,
            'getRoleIds',
            'setRoleIds',
            'getRoles',
            'setRoles',
            'getMovie',
            'setMovie',
            'role_ids'
        ));
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
