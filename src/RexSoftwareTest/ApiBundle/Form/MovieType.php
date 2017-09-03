<?php

namespace RexSoftwareTest\ApiBundle\Form;


use Doctrine\Common\Collections\ArrayCollection;
use RexSoftwareTest\ApiBundle\Entity\Movie;
use RexSoftwareTest\ApiBundle\Entity\Role;
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

        // validate the input and hydrate the roles
        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) {
                $movie = $event->getData();

                if (!$movie instanceof Movie) {
                    throw new \RuntimeException('unexpected state - $movie should always be a Movie');
                }

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

                $newRoleList = $this->roleRepository->findBy(['id' => $movie->getRoleIds()]);
                $oldRoleList = $movie->getRoles();

                $missingRoleIds = $movie->getRoleIds();
                foreach ($newRoleList as $role) {
                    /** @var Role $role */
                    $ind = array_search($role->getId(), $missingRoleIds, true);
                    if (false === $ind) {
                        continue;
                    }
                    unset($missingRoleIds[$ind]);
                }
                $missingRoleIds = array_values($missingRoleIds);

                if (0 !== count($missingRoleIds)) {
                    $event->getForm()->addError(new FormError(sprintf(
                        'One or more role ids could not be found: %s.',
                        json_encode($missingRoleIds)
                    )));
                }

                // generate an array index for each role changed in the format [id => list($new, $old)]
                $roleMap = [];
                foreach ($newRoleList as $role) {
                    if (!$role instanceof Role) {
                        continue;
                    }
                    if (false === array_key_exists($role->getId(), $roleMap)) {
                        $roleMap[$role->getId()] = [null, null];
                    }
                    list($newRole, $oldRole) = $roleMap[$role->getId()];
                    $newRole = $role;
                    $roleMap[$role->getId()] = [$newRole, $oldRole];
                }
                foreach ($oldRoleList as $role) {
                    if (!$role instanceof Role) {
                        continue;
                    }
                    if (false === array_key_exists($role->getId(), $roleMap)) {
                        $roleMap[$role->getId()] = [null, null];
                    }
                    list($newRole, $oldRole) = $roleMap[$role->getId()];
                    $oldRole = $role;
                    $roleMap[$role->getId()] = [$newRole, $oldRole];
                }

                // handle setting the property on the roles which actually links them
                foreach ($roleMap as $rolePair) {
                    list($newRole, $oldRole) = $rolePair;
                    /** @var Role $newRole */
                    /** @var Role $oldRole */
                    if (null !== $newRole && null !== $oldRole) {
                        // no change
                        continue;
                    }
                    if (null === $newRole) {
                        // deleted
                        $oldRole->setMovie(null);
                        continue;
                    }
                    // and the tricky case, added
                    if ($newRole->getMovie() instanceof Movie) {
                        // if the role is part of another movie it cannot be added
                        $event->getForm()->addError(new FormError(sprintf(
                            'Role id %d could not be added, as it is already added to Movie id %d "%s".',
                            $newRole->getId(),
                            $newRole->getMovie()->getId(),
                            $newRole->getMovie()->getName()
                        )));
                        continue;
                    }
                    $newRole->setMovie($movie);
                }

                $movie->setRoles(new ArrayCollection($newRoleList));
                $movie->setRoleIds(null);
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
