<?php

namespace RexSoftwareTest\ApiBundle\Controller;


use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;
use RexSoftwareTest\ApiBundle\Repository\MovieRepository;
use Symfony\Component\HttpFoundation\Response;

class MovieController extends FOSRestController
{
    protected $movieRepository;

    public function __construct(MovieRepository $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    /**
     * Get all movies that optionally meet certain conditions.
     *
     * @ApiDoc(
     *  resource=true,
     *  statusCodes={
     *      "200"="All movies that meet the conditions are returned."
     *  },
     *  responseMap={
     *      "200"={"class"="array<RexSoftwareTest\ApiBundle\Entity\Movie>", "groups"={"movie"}}
     *  },
     *  section="Movie"
     * )
     *
     * @Rest\Get("")
     *
     * @return Response
     */
    public function getIndexAction()
    {
        $view = $this->view($this->movieRepository->findAll());
        $view->setContext((new Context())->addGroup('movie'));
        return $this->handleView($view);
    }
}
