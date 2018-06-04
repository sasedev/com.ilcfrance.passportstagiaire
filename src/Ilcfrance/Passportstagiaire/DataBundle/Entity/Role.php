<?php
namespace Ilcfrance\Passportstagiaire\DataBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ilcfrance\Passportstagiaire\DataBundle\Model\EntityTraceable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Role\Role as BaseRole;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Role
 *
 * @ORM\Table(name="ilcfrance_roles")
 * @ORM\Entity(repositoryClass="Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\RoleRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_Role")
 * @UniqueEntity(fields={"id"}, errorPath="id", groups={"id"})
 *
 * @author sasedev <sinus@saseprod.net>
 */
class Role extends BaseRole implements \Serializable, EntityTraceable
{

    /**
     *
     * @var string @ORM\Column(name="id", type="text", nullable=false)
     *      @ORM\Id
     *      @Assert\Regex(pattern="/^ROLE\_[A-Z](([A-Z0-9\_]+[A-Z0-9])|[A-Z0-9])$/", groups={"id"})
     */
    protected $id;

    /**
     *
     * @var string @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     *
     * @var \DateTime @ORM\Column(name="created_at", type="datetimetz", nullable=true)
     */
    protected $dtCrea;

    /**
     *
     * @var \DateTime @ORM\Column(name="updated_at", type="datetimetz", nullable=true)
     *      @Gedmo\Timestampable(on="update")
     */
    protected $dtUpdate;

    /**
     *
     * @var Collection @ORM\ManyToMany(targetEntity="Role", inversedBy="childs", cascade={"persist"})
     *      @ORM\JoinTable(name="ilcfrance_role_parents",
     *      joinColumns={
     *      @ORM\JoinColumn(name="child_id", referencedColumnName="id")
     *      },
     *      inverseJoinColumns={
     *      @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     *      }
     *      )
     *      @ORM\OrderBy({"id" = "ASC"})
     *      @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_Role_parents")
     */
    protected $parents;

    /**
     *
     * @var Collection @ORM\ManyToMany(targetEntity="Role", mappedBy="parents", cascade={"persist"})
     *      @ORM\JoinTable(name="ilcfrance_role_parents",
     *      joinColumns={
     *      @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     *      },
     *      inverseJoinColumns={
     *      @ORM\JoinColumn(name="child_id", referencedColumnName="id")
     *      }
     *      )
     *      @ORM\OrderBy({"id" = "ASC"})
     *      @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_Role_childs")
     */
    protected $childs;

    /**
     *
     * @var Collection @ORM\ManyToMany(targetEntity="User", mappedBy="userRoles", cascade={"persist"})
     *      @ORM\JoinTable(name="ilcfrance_users_roles",
     *      joinColumns={
     *      @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     *      },
     *      inverseJoinColumns={
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *      }
     *      )
     *      @ORM\OrderBy({"id" = "ASC"})
     *      @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_Role_users")
     */
    protected $users;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dtCrea = new \DateTime('now');

        $this->parents = new ArrayCollection();
        $this->childs = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
     * Get $id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set $id
     *
     * @param string $id
     *
     * @return Role
     */
    public function setId($id)
    {
        $this->id = \strtoupper($id);

        return $this;
    }

    /**
     * Get $description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set $description
     *
     * @param string $description
     *
     * @return Role
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get $dtCrea
     *
     * @return \DateTime
     */
    public function getDtCrea()
    {
        return $this->dtCrea;
    }

    /**
     * Set $dtCrea
     *
     * @param \DateTime $dtCrea
     *
     * @return Role
     */
    public function setDtCrea(\DateTime $dtCrea = null)
    {
        $this->dtCrea = $dtCrea;

        return $this;
    }

    /**
     * Get $dtUpdate
     *
     * @return \DateTime
     */
    public function getDtUpdate()
    {
        return $this->dtUpdate;
    }

    /**
     * Set $dtUpdate
     *
     * @param \DateTime $dtUpdate
     *
     * @return Role
     */
    public function setDtUpdate(\DateTime $dtUpdate = null)
    {
        $this->dtUpdate = $dtUpdate;

        return $this;
    }

    /**
     * Add $parent
     *
     * @param Role $parent
     *
     * @return Role
     */
    public function addParent(Role $parent)
    {
        $this->parents[] = $parent;

        return $this;
    }

    /**
     * Remove $parent
     *
     * @param Role $parent
     *
     * @return Role
     */
    public function removeParent(Role $parent)
    {
        $this->parents->removeElement($parent);

        return $this;
    }

    /**
     * Get $parents
     *
     * @return ArrayCollection
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * Set $parents
     *
     * @param Collection $parents
     *
     * @return Role
     */
    public function setParents(Collection $parents)
    {
        $this->parents = $parents;

        return $this;
    }

    /**
     * Add $child
     *
     * @param Role $child
     *
     * @return Role
     */
    public function addChild(Role $child)
    {
        $this->childs[] = $child;
        $child->addParent($this);

        return $this;
    }

    /**
     * Remove $child
     *
     * @param Role $child
     *
     * @return Role
     */
    public function removeChild(Role $child)
    {
        $this->childs->removeElement($child);
        $child->removeParent($this);

        return $this;
    }

    /**
     * Get $childs
     *
     * @return ArrayCollection
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * Set $childs
     *
     * @param Collection $childs
     *
     * @return Role
     */
    public function setChilds(Collection $childs)
    {
        $this->childs = $childs;

        return $this;
    }

    /**
     * Add $user
     *
     * @param User $user
     *
     * @return Role
     */
    public function addUser(User $user)
    {
        $this->users[] = $user;
        $user->addUserRole($this);

        return $this;
    }

    /**
     * Remove $user
     *
     * @param User $user
     *
     * @return Role
     */
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
        $user->removeUserRole($this);

        return $this;
    }

    /**
     * Get $users
     *
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set $users
     *
     * @param Collection $users
     *
     * @return Role
     */
    public function setUsers(Collection $users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     *
     * {@inheritdoc} @see RoleInterface::getRole()
     * @return string
     */
    public function getRole()
    {
        return $this->getId();
    }

    /**
     * Serializes the Role.
     *
     * {@inheritdoc} @see Serializable::serialize()
     * @return string
     */
    public function serialize()
    {
        return \serialize(array(
            $this->id,
            $this->description
        ));
    }

    /**
     * Unserializes the User.
     *
     * {@inheritdoc} @see Serializable::unserialize()
     * @param string $serialized
     *
     * @return Role
     */
    public function unserialize($serialized)
    {
        $data = \unserialize($serialized);
        // add a few extra elements in the array to ensure that we have enough
        // keys when
        // unserializing
        // older data which does not include all properties.
        $data = array_merge($data, array_fill(0, 2, null));

        list ($this->id, $this->description) = $data;

        return $this;
    }

    /**
     * string representation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getId();
    }

    public function jsonSerialize()
    {
        $dtCrea = null;
        if (null != $this->dtCrea) {
            $dtCrea = $this->dtCrea->format(\DateTime::ISO8601);
        }
        $dtUpdate = null;
        if (null != $this->dtUpdate) {
            $dtUpdate = $this->dtUpdate->format(\DateTime::ISO8601);
        }
        $users = array();
        foreach ($this->getUsers() as $user) {
            $users[] = $user->getId();
        }
        $parents = array();
        foreach ($this->getParents() as $parent) {
            $parents[] = $parent->getId();
        }
        $childs = array();
        foreach ($this->getChilds() as $child) {
            $childs[] = $child->getId();
        }

        return [
            'id' => $this->id,
            'description' => $this->description,
            'dtCrea' => $dtCrea,
            'dtUpdate' => $dtUpdate,
            'parents' => $parents,
            'childs' => $childs,
            'users' => $users
        ];
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Ilcfrance\Passportstagiaire\DataBundle\Model\EntityTraceable::getRelated()
     */
    public function getRelated()
    {
        $ret = array();

        foreach ($this->getParents() as $role) {
            $related = array(
                'id' => $role->getId(),
                'class' => Role::class
            );
            $ret[] = $related;
        }

        foreach ($this->getChilds() as $role) {
            $related = array(
                'id' => $role->getId(),
                'class' => Role::class
            );
            $ret[] = $related;
        }

        foreach ($this->getUsers() as $user) {
            $related = array(
                'id' => $user->getId(),
                'class' => User::class
            );
            $ret[] = $related;
        }
        return $ret;
    }
}