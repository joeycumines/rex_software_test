<?php

namespace RexSoftwareTest\ApiBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
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
     * @JMS\Type("integer")
     * @JMS\Groups({"actor"})
     *
     * @var int
     */
    protected $id;

    /**
     * The name of the actor.
     *
     * @ORM\Column(name="name",type="string",nullable=false,length=512)
     *
     * @JMS\Type("string")
     * @JMS\Groups({"actor"})
     *
     * @var string
     */
    protected $name;

    /**
     * The date the actor was born (datetime at the 00:00), this field is nullable.
     *
     * @ORM\Column(name="birth_date",type="datetime",nullable=true)
     *
     * @JMS\Type("DateTime")
     * @JMS\Groups({"actor"})
     * @JMS\SerializedName("birth_date")
     *
     * @var \DateTime|null
     */
    protected $birthDate;

    /**
     * The age of the actor, this field is effectively virtual and only present for serialization purposes, and is
     * calculated via date.
     *
     * @JMS\Accessor(getter="getAge")
     * @JMS\Type("integer")
     * @JMS\Groups({"actor"})
     *
     * @var int|null
     */
    protected $age;

    /**
     * A short bio for the actor, this field is nullable.
     *
     * @ORM\Column(name="bio",type="string",nullable=true)
     *
     * @JMS\Type("string")
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
     * @JMS\Type("string")
     * @JMS\Groups({"actor"})
     *
     * @var string|null
     */
    protected $image;

    /**
     * The roles this actor has played.
     *
     * @ORM\OneToMany(targetEntity="Role", mappedBy="actor")
     *
     * @var ArrayCollection
     */
    protected $roles;

    /**
     * The ids of the roles that this actor has played.
     *
     * @JMS\Type("array<integer>")
     * @JMS\Accessor(getter="serializeRoleIds")
     * @JMS\SerializedName("role_ids")
     * @JMS\Groups({"actor"})
     *
     * @var int[]
     */
    protected $roleIds = [];

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
        if ($birthDate instanceof \DateTimeInterface && new \DateTime() < $birthDate) {
            throw new \InvalidArgumentException('$birthDate must not be a future date');
        }
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
     * @return Actor
     */
    public function setName(string $name): Actor
    {
        $this->name = $name;
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
     * @return Actor
     */
    public function setRoles(ArrayCollection $roles): Actor
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * Convert the actor's birth date to an age value (years).
     *
     * @return int|null
     */
    public function getAge()
    {
        $birthDate = $this->birthDate;
        if (!$birthDate instanceof \DateTimeInterface) {
            return null;
        }
        $birthDate = (new \DateTime())->setTimestamp($birthDate->getTimestamp())->setTime(0, 0);
        return $birthDate->diff(new \DateTime())->y;
    }

    /**
     * @return int[]
     */
    public function getRoleIds(): array
    {
        return $this->roleIds;
    }

    /**
     * @param int[] $roleIds
     *
     * @return Actor
     */
    public function setRoleIds(array $roleIds): Actor
    {
        $this->roleIds = $roleIds;
        return $this;
    }

    /**
     * Converts the array collection of roles into an array of int ids.
     *
     * @return int[]
     */
    public function serializeRoleIds(): array
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
}
