<?php

namespace RexSoftwareTest\ApiBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * A role of an actor in a movie.
 *
 * @ORM\Table("roles")
 * @ORM\Entity(repositoryClass="RexSoftwareTest\ApiBundle\Repository\RoleRepository")
 */
class Role
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id",nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Type("integer")
     * @JMS\Groups({"role"})
     *
     * @var int
     */
    protected $id;

    /**
     * The movie of which this role is for.
     *
     * @ORM\ManyToOne(targetEntity="Movie",inversedBy="roles")
     * @ORM\JoinColumn(name="movie_id",referencedColumnName="id",nullable=false)
     *
     * @var Movie
     */
    protected $movie;

    /**
     * The id of the movie of which this role is for.
     *
     * @JMS\Type("integer")
     * @JMS\Accessor(getter="getMovieId")
     * @JMS\SerializedName("movie_id")
     * @JMS\Groups({"role"})
     *
     * @var int
     */
    protected $movieId;

    /**
     * The actor who played this role.
     *
     * @ORM\ManyToOne(targetEntity="Actor",inversedBy="roles")
     * @ORM\JoinColumn(name="actor_id",referencedColumnName="id",nullable=false)
     *
     * @var Actor
     */
    protected $actor;

    /**
     * The id of the actor who played this role.
     *
     * @JMS\Type("integer")
     * @JMS\Accessor(getter="getActorId")
     * @JMS\SerializedName("actor_id")
     * @JMS\Groups({"role"})
     *
     * @var int
     */
    protected $actorId;

    /**
     * The name of the character / role.
     *
     * @ORM\Column(name="name",type="string",nullable=false,length=512)
     *
     * @JMS\Type("string")
     * @JMS\Groups({"role"})
     *
     * @var string
     */
    protected $name;

    /**
     * @return int
     */
    public function getMovieId()
    {
        if (!$this->movie instanceof Movie) {
            return null;
        }
        return $this->movie->getId();
    }

    /**
     * @return int
     */
    public function getActorId()
    {
        if (!$this->actor instanceof Actor) {
            return null;
        }
        return $this->actor->getId();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Role
     */
    public function setId(int $id): Role
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Movie
     */
    public function getMovie()
    {
        return $this->movie;
    }

    /**
     * @param Movie $movie
     *
     * @return Role
     */
    public function setMovie(Movie $movie): Role
    {
        $this->movie = $movie;
        return $this;
    }

    /**
     * @return Actor
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * @param Actor $actor
     *
     * @return Role
     */
    public function setActor(Actor $actor): Role
    {
        $this->actor = $actor;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Role
     */
    public function setName(string $name): Role
    {
        $this->name = $name;
        return $this;
    }
}
