<?php

namespace RexSoftwareTest\ApiBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * The actor entity.
 *
 * @ORM\Table("actors")
 * @ORM\Entity(repositoryClass="RexSoftwareTest\ApiBundle\Repository\ActorRepository")
 */
class Actor
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="id",nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Groups({"actor"})
     *
     * @var int
     */
    protected $id;

    /**
     * The date the actor was born (datetime at the 00:00), this field is nullable.
     *
     * @ORM\Column(name="birth_date",type="datetime",nullable=true)
     *
     * @JMS\Groups({"actor"})
     * @JMS\SerializedName("birth_date")
     *
     * @var \DateTime|null
     */
    protected $birthDate;

    /**
     * A short bio for the actor, this field is nullable.
     *
     * @ORM\Column(name="bio",type="string",nullable=true)
     *
     * @JMS\Groups({"actor"})
     *
     * @var string|null
     */
    protected $bio;

    /**
     * A unique identifier for the actor's portrait, this field is nullable.
     *
     * @ORM\Column(name="image",type="string",nullable=true)
     *
     * @JMS\Groups({"actor"})
     *
     * @var string|null
     */
    protected $image;

    /**
     * The age of the actor.
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("age")
     * @JMS\Groups({"actor"})
     *
     * @var int|null
     */
    public function getAge()
    {
        $birthDate = null;
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
     * @return Actor
     */
    public function setId(int $id): Actor
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * @param \DateTime|null $birthDate
     *
     * @return Actor
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * @param null|string $bio
     *
     * @return Actor
     */
    public function setBio($bio)
    {
        $this->bio = $bio;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param null|string $image
     *
     * @return Actor
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }
}
