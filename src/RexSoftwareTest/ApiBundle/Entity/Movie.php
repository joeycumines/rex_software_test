<?php

namespace RexSoftwareTest\ApiBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * The movie entity.
 *
 * @ORM\Table("movies")
 * @ORM\Entity(repositoryClass="RexSoftwareTest\ApiBundle\Repository\MovieRepository")
 */
class Movie
{
    const INITIAL_RATING = 6.0;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id",nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Groups({"movie"})
     *
     * @var int
     */
    protected $id;

    /**
     * The title of the movie, non nullable.
     *
     * @ORM\Column(name="name",type="string",nullable=false,length=512)
     *
     * @JMS\Groups({"movie"})
     *
     * @var string
     */
    protected $name;

    /**
     * The movie's description, this field is nullable.
     *
     * @ORM\Column(name="description",type="string",nullable=true)
     *
     * @JMS\Groups({"movie"})
     *
     * @var string|null
     */
    protected $description;

    /**
     * A unique identifier for the movie's image (poster, etc), this field is nullable.
     *
     * @ORM\Column(name="image",type="string",nullable=true)
     *
     * @JMS\Groups({"movie"})
     *
     * @var string|null
     */
    protected $image;

    /**
     * The movie's current rating / 10, updated with each rating to avoid excessive re-calculation, it starts at 6.
     *
     * @ORM\Column(type="decimal",precision=4,scale=2,nullable=false)
     *
     * @JMS\Groups({"movie"})
     *
     * @var float
     */
    protected $rating = self::INITIAL_RATING;

    /**
     * The roles in this movie.
     *
     * @ORM\OneToMany(targetEntity="Role", mappedBy="movie")
     *
     * @var ArrayCollection
     */
    protected $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    /**
     * The ids of the roles that are in the movie.
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("role_ids")
     * @JMS\Groups({"movie"})
     *
     * @return int[]
     */
    public function getRoleIds(): array
    {
        $roles = [];
        foreach ($this->roles as $role) {
            if (!$role instanceof Role || false === is_int($role->getId())) {
                continue;
            }
            $roles[] = $role->getId();
        }
        return $roles;
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
     * @return Movie
     */
    public function setId(int $id): Movie
    {
        $this->id = $id;
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
     * @return Movie
     */
    public function setName(string $name): Movie
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Movie
     */
    public function setDescription(string $description): Movie
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     *
     * @return Movie
     */
    public function setImage(string $image): Movie
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return float
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param float $rating
     *
     * @return Movie
     */
    public function setRating(float $rating): Movie
    {
        $this->rating = $rating;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param ArrayCollection $roles
     *
     * @return Movie
     */
    public function setRoles(ArrayCollection $roles)
    {
        $this->roles = $roles;
        return $this;
    }
}
