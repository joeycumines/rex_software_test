<?php

namespace RexSoftwareTest\ApiBundle\Controller;


use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;
use RexSoftwareTest\ApiBundle\Exception\EnvironmentException;
use Symfony\Component\HttpFoundation\JsonResponse;

class SwaggerController extends FOSRestController
{
    /**
     * Get the swagger doc for the API.
     *
     * @ApiDoc(
     *  resource=true,
     *  statusCodes={
     *      "200"="OK",
     *  },
     *  section="Swagger"
     * )
     *
     * @Rest\Get("")
     *
     * @return JsonResponse
     */
    public function getApiAction()
    {
        $swaggerDoc = $this->container->get('kernel')->locateResource('@RexSoftwareTestApiBundle/Resources/public/swagger.json');

        if (false === is_readable($swaggerDoc) || false === is_file($swaggerDoc)) {
            throw new EnvironmentException('missing swagger doc: ' . $swaggerDoc);
        }

        $swaggerDoc = file_get_contents($swaggerDoc);
        $swaggerDoc = json_decode($swaggerDoc);

        if (false === is_object($swaggerDoc)) {
            throw new EnvironmentException('invalid swagger doc: ' . json_last_error());
        }

        return new JsonResponse($swaggerDoc);
    }
}
