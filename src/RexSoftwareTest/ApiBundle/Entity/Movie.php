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
    const MAX_RATING = 10.0;
    const MIN_RATING = 0.0;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id",nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Type("integer")
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
     * @JMS\Type("string")
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
     * @JMS\Type("string")
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
     * @JMS\Type("string")
     * @JMS\Groups({"movie"})
     *
     * @var string|null
     */
    protected $image;

    /**
     * The movie's current rating / 10, updated with each rating to avoid excessive re-calculation, it starts at 6...
     * or so the original plan was, for simplicity it just gets set, right now.
     *
     * @ORM\Column(type="decimal",precision=4,scale=2,nullable=false)
     *
     * @JMS\Type("float")
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

    /**
     * The ids of the roles that are in the movie.
     *
     * @JMS\Type("array<integer>")
     * @JMS\Accessor(getter="getRoleIds")
     * @JMS\SerializedName("role_ids")
     * @JMS\Groups({"movie"})
     *
     * @var int[]
     */
    protected $roleIds = null;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
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
     * @param string|null $description
     *
     * @return Movie
     */
    public function setDescription($description): Movie
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
     * @param string|null $image
     *
     * @return Movie
     */
    public function setImage($image): Movie
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
     * Validate a rating value, and return it as a float.
     *
     * @param mixed $rating
     *
     * @return float
     *
     * @throws \InvalidArgumentException
     */
    public static function validateRating($rating): float
    {
        if (false === is_numeric($rating)) {
            throw new \InvalidArgumentException(sprintf('Non-numeric rating "%s".', $rating));
        }
        $rating = (float)$rating;
        if ($rating < self::MIN_RATING || $rating > self::MAX_RATING) {
            throw new \InvalidArgumentException(sprintf('Rating %.2F is out of bounds.', $rating));
        }
        return $rating;
    }

    /**
     * @param float $rating
     *
     * @return Movie
     */
    public function setRating($rating): Movie
    {
        try {
            $this->rating = self::validateRating($rating);
        } catch (\InvalidArgumentException $e) {
        }
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
     * Set the roles, which also clears any explicitly set role ids.
     *
     * @param ArrayCollection $roles
     *
     * @return Movie
     */
    public function setRoles(ArrayCollection $roles): Movie
    {
        $this->roles = $roles;
        $this->roleIds = null;
        return $this;
    }

    /**
     * Get the role ids, which are generated from the actual roles collection, if the role ids have not been
     * explicitly set to an array.
     *
     * @return int[]
     */
    public function getRoleIds()
    {
        if (true === is_array($this->roleIds)) {
            return $this->roleIds;
        }
        $roleIds = [];
        if (null !== $this->roles) {
            foreach ($this->roles as $role) {
                if (!$role instanceof Role || false === is_int($role->getId())) {
                    continue;
                }
                $roleIds[] = $role->getId();
            }
        }
        return $roleIds;
    }

    /**
     * @param int[]|null $roleIds
     *
     * @return Movie
     */
    public function setRoleIds($roleIds): Movie
    {
        $this->roleIds = $roleIds;
        return $this;
    }
}
