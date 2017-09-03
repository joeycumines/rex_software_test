<?php

namespace RexSoftwareTest\ApiBundle\Form\EventListener;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use RexSoftwareTest\ApiBundle\Exception\Form\EventListener\CollectionChangeSubscriberException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * This event subscriber is designed to allow entities that have collection, with a doctrine relationship of one to
 * many, to be updated via a serialization-only id field, which is an alias for the actual field.
 *
 * Basically, this allows serialization and deserialization to be as symmetrical as possible, though it also means
 * that the foreign key on the many side must be nullable, else it won't play nice since it potentially orphans them.
 *
 * Notes:
 * - Getters and setters are all on the one side, excepting the $oneGetter and $oneSetter, which from the many.
 * - The classes are used at every stage to validate the info.
 * - Only int or string ids are supported.
 * - Regardless of updates, the id setter will always receive a null value once the process completes.
 */
class CollectionChangeSubscriber implements EventSubscriberInterface
{
    protected $manyRepository;
    protected $manyIdField;
    protected $manyIdGetter;
    protected $oneIdGetter;
    protected $manyClass;
    protected $oneClass;
    protected $idsGetter;
    protected $idsSetter;
    protected $collectionGetter;
    protected $collectionSetter;
    protected $oneGetter;
    protected $oneSetter;
    protected $targetChildForm;

    public function __construct(
        EntityRepository $manyRepository,
        string $manyIdField,
        string $manyIdGetter,
        string $manyClass,
        string $oneIdGetter,
        string $oneClass,
        string $idsGetter,
        string $idsSetter,
        string $collectionGetter,
        string $collectionSetter,
        string $oneGetter,
        string $oneSetter,
        string $targetChildForm
    ) {
        $this->manyRepository = $manyRepository;
        $this->manyIdField = $manyIdField;
        $this->manyIdGetter = $manyIdGetter;
        $this->oneIdGetter =$oneIdGetter;
        $this->manyClass = $manyClass;
        $this->oneClass = $oneClass;
        $this->idsGetter = $idsGetter;
        $this->idsSetter = $idsSetter;
        $this->collectionGetter = $collectionGetter;
        $this->collectionSetter = $collectionSetter;
        $this->oneGetter = $oneGetter;
        $this->oneSetter = $oneSetter;
        $this->targetChildForm = $targetChildForm;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [FormEvents::SUBMIT => 'updateNestedCollection'];
    }

    protected function getManyShortName(): string
    {
        return (new \ReflectionClass($this->manyClass))->getShortName();
    }

    protected function getOneShortName(): string
    {
        return (new \ReflectionClass($this->oneClass))->getShortName();
    }

    public function updateNestedCollection(FormEvent $event)
    {
        $oneEntity = $event->getData();

        if (!$oneEntity instanceof $this->oneClass) {
            throw new CollectionChangeSubscriberException(sprintf('the form data was expected to be of class %s', $this->oneClass));
        }

        $errorForm = $event->getForm();
        if (true === $errorForm->has($this->targetChildForm)) {
            $errorForm = $errorForm->get($this->targetChildForm);
        }

        // load the existing collection
        $oldManyList = call_user_func([$oneEntity, $this->collectionGetter]);

        // load the ids, which have been set via the form
        $idList = call_user_func([$oneEntity, $this->idsGetter]);

        // find new many entities via the id's passed through the repository
        $newManyList = $this->manyRepository->findBy([$this->manyIdField => $idList]);

        // validate both old any new lists
        foreach ($newManyList as $newMany) {
            if (!$newMany instanceof $this->manyClass) {
                throw new CollectionChangeSubscriberException(sprintf('the repositories returned values were expected to be of class %s', $this->manyClass));
            }
        }
        foreach ($oldManyList as $oldMany) {
            if (!$oldMany instanceof $this->manyClass) {
                throw new CollectionChangeSubscriberException(sprintf('the collection getter returned values were expected to be of class %s', $this->manyClass));
            }
        }

        // identify any missing ids
        $missingManyIds = $idList;
        foreach ($newManyList as $newMany) {
            $manyId = call_user_func([$newMany, $this->manyIdGetter]);
            $ind = array_search($manyId, $missingManyIds, true);
            if (false === $ind) {
                continue;
            }
            unset($missingManyIds[$ind]);
        }
        $missingManyIds = array_values($missingManyIds);

        if (0 !== count($missingManyIds)) {
            $errorForm->addError(new FormError(sprintf(
                'One or more %s ids could not be found: %s.',
                $this->getManyShortName(),
                implode(', ', $missingManyIds)
            )));
        }

        // generate an array index for each many changed in the format [id => list($new, $old)]
        $manyMap = [];
        foreach ($newManyList as $many) {
            $manyId = call_user_func([$many, $this->manyIdGetter]);
            if (false === array_key_exists($manyId, $manyMap)) {
                $manyMap[$manyId] = [null, null];
            }
            list($newMany, $oldMany) = $manyMap[$manyId];
            $newMany = $many;
            $manyMap[$manyId] = [$newMany, $oldMany];
        }
        foreach ($oldManyList as $many) {
            $manyId = call_user_func([$many, $this->manyIdGetter]);
            if (false === array_key_exists($manyId, $manyMap)) {
                $manyMap[$manyId] = [null, null];
            }
            list($newMany, $oldMany) = $manyMap[$manyId];
            $oldMany = $many;
            $manyMap[$manyId] = [$newMany, $oldMany];
        }

        // handle the changes on the many entities
        foreach ($manyMap as $manyPair) {
            list($newMany, $oldMany) = $manyPair;
            if (null !== $newMany && null !== $oldMany) {
                // no change
                continue;
            }
            if (null === $newMany) {
                // deleted
                call_user_func([$oldMany, $this->oneSetter], null);
                continue;
            }
            // and the tricky case, added
            $newManyOne = call_user_func([$newMany, $this->oneGetter]);
            if ($newManyOne instanceof $this->oneClass) {
                $errorForm->addError(new FormError(sprintf(
                    '%s id %d could not be added, as it is already added to %s id %d.',
                    $this->getManyShortName(),
                    call_user_func([$newMany, $this->manyIdGetter]),
                    $this->getOneShortName(),
                    call_user_func([$newManyOne, $this->oneIdGetter])
                )));
                continue;
            }
            call_user_func([$newMany, $this->oneSetter], $oneEntity);
        }

        // update the one
        call_user_func([$oneEntity, $this->collectionSetter], new ArrayCollection($newManyList));
        call_user_func([$oneEntity, $this->idsSetter], null);
    }
}
