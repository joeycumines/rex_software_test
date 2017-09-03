<?php

namespace RexSoftwareTest\ApiBundle\Model;


use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Form\FormInterface;

/**
 * Models a (potentially nested) validation error, for a symfony form.
 */
class FormValidationErrorModel
{
    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    public $name;

    /**
     * @JMS\Type("array<string>")
     *
     * @var string[]
     */
    public $errors;

    /**
     * Any child forms that also contain errors.
     *
     * @JMS\Type("array<RexSoftwareTest\ApiBundle\Model\FormValidationErrorModel>")
     *
     * @var FormValidationErrorModel[]
     */
    public $children;

    /**
     * @param FormInterface $form
     *
     * @return FormValidationErrorModel
     */
    public static function build(FormInterface $form): FormValidationErrorModel
    {
        $result = new FormValidationErrorModel();
        $result->name = $form->getName();
        $result->errors = [];
        $result->children = [];

        foreach ($form->getErrors() as $error) {
            $result->errors[] = $error->getMessage();
        }

        foreach ($form as $child) {
            if (!$form instanceof FormInterface || true === $child->isValid()) {
                continue;
            }
            $result->children[] = FormValidationErrorModel::build($child);
        }

        return $result;
    }
}
