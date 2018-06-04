<?php
namespace Ilcfrance\Passportstagiaire\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Ilcfrance\Passportstagiaire\DataBundle\Model\EntityTraceable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Intl\Intl;

/**
 * Locale
 *
 * @ORM\Table(name="ilcfrance_locales")
 * @ORM\Entity(repositoryClass="Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\LocaleRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_Locale")
 * @UniqueEntity(fields={"id"}, errorPath="id", groups={"id"})
 *
 * @author sasedev <sinus@saseprod.net>
 */
class Locale implements EntityTraceable
{

    /**
     *
     * @var integer
     */
    const ST_DISABLED = 2;

    /**
     *
     * @var integer
     */
    const ST_ENABLED = 1;

    /**
     *
     * @var string
     */
    const DIR_LTR = 'ltr';

    /**
     *
     * @var string
     */
    const DIR_RTL = 'rtl';

    /**
     *
     * @var string @ORM\Column(name="id", type="text", nullable=false)
     *      @ORM\Id
     *      @Assert\Locale(groups={"id"})
     *      @Assert\Regex(pattern="/^[a-z]{2}$/", groups={"id"})
     */
    protected $id;

    /**
     *
     * @var integer @ORM\Column(name="status", type="integer", nullable=false)
     */
    protected $status;

    /**
     *
     * @var string @ORM\Column(name="direction", type="text", nullable=false)
     */
    protected $direction;

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
     * @var Collection @ORM\OneToMany(targetEntity="User", mappedBy="locale", cascade={"persist"})
     *      @ORM\OrderBy({"id" = "ASC"})
     *      @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_Locale_users")
     */
    protected $users;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->status = self::ST_DISABLED;
        $this->direction = self::DIR_LTR;
        $this->dtCrea = new \DateTime('now');
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
     * @return Locale
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get $locale
     *
     * @return string
     */
    public function getLanguageName()
    {
        return \ucfirst(Intl::getLanguageBundle()->getLanguageName($this->id));
    }

    /**
     * Get $name
     *
     * @return string
     */
    public function getDisplayName()
    {
        return \ucfirst(\Locale::getDisplayName($this->id, $this->id));
    }

    /**
     * Get $status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set $status
     *
     * @param integer $status
     *
     * @return Locale
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Set $status
     *
     * @param integer $status
     *
     * @return Locale Locale public function setStatus($status)
     *         {
     *         $this->status = $status;
     *         return $this;
     *         }
     *         /**
     *         Get $direction
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * Set $direction
     *
     * @param string $direction
     *
     * @return Locale
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;

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
     * @return Locale
     */
    public function setDtCrea(\DateTime $dtCrea = null)
    {
        $this->dtCrea = $dtCrea;

        return $this;
    }

    /**
     * Get \$dtUpdate
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
     * @return Locale
     */
    public function setDtUpdate(\DateTime $dtUpdate = null)
    {
        $this->dtUpdate = $dtUpdate;

        return $this;
    }

    /**
     * Add user
     *
     * @param User $user
     *
     * @return Locale
     */
    public function addUser(User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param User $user
     *
     * @return Locale
     */
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);

        return $this;
    }

    /**
     * Get users
     *
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set users
     *
     * @param Collection $users
     *
     * @return Locale
     */
    public function setUsers(Collection $users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Choice Form status
     *
     * @return multitype:string
     */
    public static function choiceStatus()
    {
        return array(
            'Locale.status.choice.' . self::ST_DISABLED => self::ST_DISABLED,
            'Locale.status.choice.' . self::ST_ENABLED => self::ST_ENABLED
        );
    }

    /**
     * Choice Validator status
     *
     * @return multitype:integer
     */
    public static function choiceStatusCallback()
    {
        return array(
            self::ST_DISABLED,
            self::ST_ENABLED
        );
    }

    /**
     * Choice Form direction
     *
     * @return multitype:string
     */
    public static function choiceDirection()
    {
        return array(
            'Locale.direction.choice.' . self::DIR_LTR => self::DIR_LTR,
            'Locale.direction.choice.' . self::DIR_RTL => self::DIR_RTL
        );
    }

    /**
     * Choice Validator status
     *
     * @return multitype:string
     */
    public static function choiceDirectionCallback()
    {
        return array(
            self::DIR_LTR,
            self::DIR_RTL
        );
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

        return [
            'id' => $this->id,
            'status' => $this->status,
            'direction' => $this->direction,
            'dtCrea' => $dtCrea,
            'dtUpdate' => $dtUpdate,
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