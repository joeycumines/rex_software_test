<?php

namespace RexSoftwareTest\ApiBundle\Model;


use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Response for form validation errors.
 */
class HttpValidationErrorModel extends HttpErrorResponseModel
{
    /**
     * The form and errors that caused this response.
     *
     * @JMS\Type("RexSoftwareTest\ApiBundle\Model\FormValidationErrorModel")
     *
     * @var FormValidationErrorModel
     */
    public $form;

    public static function build(FormInterface $form): HttpValidationErrorModel
    {
        $result = new HttpValidationErrorModel();
        $result->form = FormValidationErrorModel::build($form);
        $result->error = new HttpErrorDetailsModel();
        $result->error->message = sprintf('Bad Request: Form validation error(s) occurred.');
        $result->error->code = Response::HTTP_BAD_REQUEST;
        return $result;
    }
}
