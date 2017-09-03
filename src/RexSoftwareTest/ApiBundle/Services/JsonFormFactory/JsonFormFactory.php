<?php

namespace RexSoftwareTest\ApiBundle\Services\JsonFormFactory;


use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;

/**
 * This factory can be used as a service to create forms that better support json requests (additionally).
 */
class JsonFormFactory
{
    protected $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * Create a form, with a request handler that works with JSON requests, as well as normal form data.
     *
     * @param string|FormTypeInterface $type        The built type of the form
     * @param mixed                    $data        The initial data for the form
     * @param array                    $options     Options for the form
     * @param bool                     $checkNested If true nested any top level props with the form name will be used.
     *
     * @return FormInterface
     */
    public function createJsonForm($type, $data = null, array $options = [], bool $checkNested = false)
    {
        // disable the csrf protection by default; we are working with API keys anyway
        $options = array_merge(
            [
                'csrf_protection' => false
            ],
            $options
        );

        return $this->formFactory->createBuilder($type, $data, $options)
            ->setRequestHandler(
                (new JsonFormRequestHandler())->setCheckNested($checkNested)
            )
            ->getForm();
    }
}
