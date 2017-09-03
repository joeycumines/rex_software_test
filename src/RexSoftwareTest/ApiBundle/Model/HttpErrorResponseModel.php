<?php

namespace RexSoftwareTest\ApiBundle\Model;


use JMS\Serializer\Annotation as JMS;

/**
 * A model that has been setup to document the response sent by FOSRestBundle on http errors.
 *
 * @see HttpErrorDetailsModel
 */
class HttpErrorResponseModel
{
    /**
     * The error details.
     *
     * @JMS\Type("RexSoftwareTest\ApiBundle\Model\HttpErrorDetailsModel")
     *
     * @var HttpErrorDetailsModel
     */
    public $error;
}
