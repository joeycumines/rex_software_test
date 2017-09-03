<?php

namespace RexSoftwareTest\ApiBundle\Controller;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;
use RexSoftwareTest\ApiBundle\Entity\Movie;
use RexSoftwareTest\ApiBundle\Entity\Role;
use RexSoftwareTest\ApiBundle\Form\MovieType;
use RexSoftwareTest\ApiBundle\Model\HttpValidationErrorModel;
use RexSoftwareTest\ApiBundle\Repository\MovieRepository;
use RexSoftwareTest\ApiBundle\Services\JsonFormFactory\JsonFormFactory;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class MovieController extends FOSRestController
{
    protected $requestStack;
    protected $jsonFormFactory;
    protected $doctrine;
    protected $movieRepository;

    public function __construct(
        RequestStack $requestStack,
        JsonFormFactory $jsonFormFactory,
        ManagerRegistry $doctrine,
        MovieRepository $movieRepository
    ) {
        $this->requestStack = $requestStack;
        $this->jsonFormFactory = $jsonFormFactory;
        $this->doctrine = $doctrine;
        $this->movieRepository = $movieRepository;
    }

    /**
     * Get all movies.
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
     *         "404"="The movie was not found, an error message will be returned."
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
     *     input="RexSoftwareTest\ApiBundle\Form\MovieType",
     *     statusCodes={
     *         "200"="A new movie was created and returned.",
     *         "400"="A bad request was made, any errors will be returned."
     *     },
     *     responseMap={
     *         "200"={"class"="RexSoftwareTest\ApiBundle\Entity\Movie", "groups"={"movie"}},
     *         "400"="RexSoftwareTest\ApiBundle\Model\HttpValidationErrorModel"
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
        $form = $this->jsonFormFactory->createForm(
            MovieType::class,
            null,
            ['method' => 'POST']
        );
        $form->handleRequest($this->requestStack->getCurrentRequest());

        if (false === $form->isValid()) {
            $view = $this->view(HttpValidationErrorModel::build($form), 400);
            return $this->handleView($view);
        }

        $movie = $form->getData();

        if (!$movie instanceof Movie) {
            throw new \RuntimeException('unexpected state - $movie should always be a Movie');
        }

        $this->doctrine->getManager()->persist($movie);
        $this->doctrine->getManager()->flush();

        $view = $this->view($movie);
        $view->setContext((new Context())->addGroup('movie'));
        return $this->handleView($view);
    }

    /**
     * Update an existing movie, overwriting all values.
     *
     * @ApiDoc(
     *     resource=true,
     *     input="RexSoftwareTest\ApiBundle\Form\MovieType",
     *     statusCodes={
     *         "200"="The movie with the given id is returned after being updated with the changes.",
     *         "400"="A bad request was made, any errors will be returned.",
     *         "404"="The movie was not found, an error message will be returned."
     *     },
     *     responseMap={
     *         "200"={"class"="RexSoftwareTest\ApiBundle\Entity\Movie", "groups"={"movie"}},
     *         "400"="RexSoftwareTest\ApiBundle\Model\HttpValidationErrorModel",
     *         "404"="RexSoftwareTest\ApiBundle\Model\HttpErrorResponseModel"
     *     },
     *     section="Movie"
     * )
     *
     * @Rest\Put("/{movie}")
     *
     * @param Movie $movie
     *
     * @return Response
     */
    public function putMovieAction(Movie $movie)
    {
        $form = $this->jsonFormFactory->createForm(
            MovieType::class,
            $movie,
            ['method' => 'PUT']
        );
        $form->handleRequest($this->requestStack->getCurrentRequest());

        if (false === $form->isValid()) {
            $view = $this->view(HttpValidationErrorModel::build($form), 400);
            return $this->handleView($view);
        }

        $movie = $form->getData();

        if (!$movie instanceof Movie) {
            throw new \RuntimeException('unexpected state - $movie should always be a Movie');
        }

        $this->doctrine->getManager()->flush();

        $view = $this->view($movie);
        $view->setContext((new Context())->addGroup('movie'));
        return $this->handleView($view);
    }

    /**
     * Update an existing movie, only updating the provided values.
     *
     * @ApiDoc(
     *     resource=true,
     *     input="RexSoftwareTest\ApiBundle\Form\MovieType",
     *     statusCodes={
     *         "200"="The movie with the given id is returned after being updated with the changes.",
     *         "400"="A bad request was made, any errors will be returned.",
     *         "404"="The movie was not found, an error message will be returned."
     *     },
     *     responseMap={
     *         "200"={"class"="RexSoftwareTest\ApiBundle\Entity\Movie", "groups"={"movie"}},
     *         "400"="RexSoftwareTest\ApiBundle\Model\HttpValidationErrorModel",
     *         "404"="RexSoftwareTest\ApiBundle\Model\HttpErrorResponseModel"
     *     },
     *     section="Movie"
     * )
     *
     * @Rest\Patch("/{movie}")
     *
     * @param Movie $movie
     *
     * @return Response
     */
    public function patchMovieAction(Movie $movie)
    {
        $form = $this->jsonFormFactory->createForm(
            MovieType::class,
            $movie,
            ['method' => 'PATCH']
        );
        $form->handleRequest($this->requestStack->getCurrentRequest());

        if (false === $form->isValid()) {
            $view = $this->view(HttpValidationErrorModel::build($form), 400);
            return $this->handleView($view);
        }

        $movie = $form->getData();

        if (!$movie instanceof Movie) {
            throw new \RuntimeException('unexpected state - $movie should always be a Movie');
        }

        $this->doctrine->getManager()->flush();

        $view = $this->view($movie);
        $view->setContext((new Context())->addGroup('movie'));
        return $this->handleView($view);
    }

    /**
     * Delete a given movie, and return it's last value.
     *
     * @ApiDoc(
     *     resource=true,
     *     statusCodes={
     *         "200"="The movie was deleted, and it's last value returned.",
     *         "404"="The movie was not found, an error message will be returned."
     *     },
     *     responseMap={
     *         "200"={"class"="RexSoftwareTest\ApiBundle\Entity\Movie", "groups"={"movie"}},
     *         "404"="RexSoftwareTest\ApiBundle\Model\HttpErrorResponseModel"
     *     },
     *     section="Movie"
     * )
     *
     * @Rest\Delete("/{movie}")
     *
     * @param Movie $movie
     *
     * @return Response
     */
    public function deleteMovieAction(Movie $movie)
    {
        $clonedMovie = clone $movie;
        foreach ($movie->getRoles() as $role) {
            if (!$role instanceof Role) {
                continue;
            }
            $role->setMovie(null);
        }
        $movie->setRoles(new ArrayCollection());
        $this->doctrine->getManager()->remove($movie);
        $this->doctrine->getManager()->flush();
        $view = $this->view($clonedMovie);
        $view->setContext((new Context())->addGroup('movie'));
        return $this->handleView($view);
    }
}
