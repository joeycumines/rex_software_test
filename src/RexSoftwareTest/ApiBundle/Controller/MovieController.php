<?php

namespace RexSoftwareTest\ApiBundle\Controller;


use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;
use RexSoftwareTest\ApiBundle\Entity\Movie;
use RexSoftwareTest\ApiBundle\Repository\MovieRepository;
use RexSoftwareTest\ApiBundle\Services\JsonFormFactory\JsonFormFactory;
use Symfony\Component\HttpFoundation\Response;

class MovieController extends FOSRestController
{
    protected $jsonFormFactory;

    protected $movieRepository;

    public function __construct(JsonFormFactory $jsonFormFactory, MovieRepository $movieRepository)
    {
        $this->jsonFormFactory = $jsonFormFactory;
        $this->movieRepository = $movieRepository;
    }

    /**
     * Get all movies that optionally meet certain conditions.
     *
     * @ApiDoc(
     *     resource=true,
     *     statusCodes={
     *         "200"="All movies that meet the conditions are returned."
     *     },
     *     responseMap={
     *         "200"={"class"="array<RexSoftwareTest\ApiBundle\Entity\Movie>", "groups"={"movie"}}
     *     },
     *     section="Movie"
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

    /**
     * Get a movie by id.
     *
     * @ApiDoc(
     *     resource=true,
     *     statusCodes={
     *         "200"="The movie with the id provided is returned.",
     *         "404"="The movie was not found, and error message will be returned."
     *     },
     *     responseMap={
     *         "200"={"class"="RexSoftwareTest\ApiBundle\Entity\Movie", "groups"={"movie"}},
     *         "404"="RexSoftwareTest\ApiBundle\Model\HttpErrorResponseModel"
     *     },
     *     section="Movie"
     * )
     *
     * @Rest\Get("/{movie}")
     *
     * @param Movie $movie
     *
     * @return Response
     */
    public function getMovieAction(Movie $movie)
    {
        $view = $this->view($movie);
        $view->setContext((new Context())->addGroup('movie'));
        return $this->handleView($view);
    }

    /**
     * Create a new movie, and get the (now fully initialized) movie, with it's new id.
     *
     * @ApiDoc(
     *     resource=true,
     *     statusCodes={
     *         "200"="The movie with the id provided is returned.",
     *         "400"="A bad request was made, an error message will be returned."
     *     },
     *     responseMap={
     *         "200"={"class"="RexSoftwareTest\ApiBundle\Entity\Movie", "groups"={"movie"}},
     *         "400"="RexSoftwareTest\ApiBundle\Model\HttpErrorResponseModel"
     *     },
     *     section="Movie"
     * )
     *
     * @Rest\Post("")
     *
     * @return Response
     */
    public function postMovieAction()
    {
        return new Response();
    }
}
