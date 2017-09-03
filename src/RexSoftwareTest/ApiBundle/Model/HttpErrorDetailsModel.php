<?php

namespace RexSoftwareTest\ApiBundle\Model;


use JMS\Serializer\Annotation as JMS;

class HttpErrorDetailsModel
{
    /**
     * The http code.
     *
     * @JMS\Type("integer")
     *
     * @var int
     */
    public $code;

    /**
     * An error message.
     *
     * @JMS\Type("string")
     *
     * @var string
     */
    public $message;
}
