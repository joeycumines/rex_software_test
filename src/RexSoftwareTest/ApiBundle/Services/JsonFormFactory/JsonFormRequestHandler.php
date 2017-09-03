<?php

namespace RexSoftwareTest\ApiBundle\Services\JsonFormFactory;


use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Adds the ability to submit forms in POST requests with JSON content.
 */
class JsonFormRequestHandler extends HttpFoundationRequestHandler
{
    protected $checkNested = false;

    /**
     * Attempt to deserialize JSON content if the request "Content-Type" is "application/json", and if it is an object
     * then accept it as a form.
     *
     * {@inheritdoc}
     */
    public function handleRequest(FormInterface $form, $request = null)
    {
        if ($request instanceof Request && $request->headers->contains('Content-Type', 'application/json')) {
            $method = $form->getConfig()->getMethod();

            // the body as an array, as needed by the form submission
            $data = json_decode($request->getContent(), true);

            if (true === is_array($data) && $method === $request->getMethod()) {
                // handle the magic form way of doing things; step into a form submission of the same type
                if (
                    true === $this->checkNested &&
                    true === is_string($form->getName()) &&
                    '' !== $form->getName() &&
                    true === array_key_exists($form->getName(), $data)
                ) {
                    $data = $data[$form->getName()];
                }

                $form->submit($data, 'PATCH' !== $method);

                return;
            }
        }

        // fall back to the HttpFoundationRequestHandler implementation
        parent::handleRequest($form, $request);
    }

    /**
     * @return bool
     */
    public function isCheckNested(): bool
    {
        return $this->checkNested;
    }

    /**
     * @param bool $checkNested
     *
     * @return JsonFormRequestHandler
     */
    public function setCheckNested(bool $checkNested): JsonFormRequestHandler
    {
        $this->checkNested = $checkNested;
        return $this;
    }
}
