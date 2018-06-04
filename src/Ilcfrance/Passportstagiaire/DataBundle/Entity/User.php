<?php
namespace Ilcfrance\Passportstagiaire\DataBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ilcfrance\Passportstagiaire\DataBundle\Model\EntityTraceable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Encoder\Pbkdf2PasswordEncoder;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="ilcfrance_users")
 * @ORM\Entity(repositoryClass="Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_User")
 * @UniqueEntity(fields={"id"}, errorPath="id", groups={"id"})
 * @UniqueEntity(fields={"email"}, errorPath="email", groups={"email"})
 *
 * @author sasedev <sinus@saseprod.net>
 */
class User implements UserInterface, \Serializable, EntityTraceable
{

    /**
     *
     * @var integer
     */
    const LOCKOUT_UNLOCKED = 1;

    /**
     *
     * @var integer
     */
    const LOCKOUT_LOCKED = 2;

    /**
     *
     * @var integer
     */
    const SEXE_MISS = 1;

    /**
     *
     * @var integer
     */
    const SEXE_MRS = 2;

    /**
     *
     * @var integer
     */
    const SEXE_MR = 3;

    /**
     *
     * @var string @ORM\Column(name="id", type="text", nullable=false, unique=true)
     *      @ORM\Id
     *      @Assert\Regex(pattern="/^[a-z][a-z0-9\_]+$/", groups={"id"})
     */
    protected $id;

    /**
     *
     * @var string @ORM\Column(name="email", type="text", nullable=true)
     *      @Assert\Email(checkMX=true, checkHost=true, groups={"email"})
     */
    protected $email;

    /**
     *
     * @var string @ORM\Column(name="clearpassword", type="text", nullable=true)
     *      @Assert\Length(min="8", max="200", groups={"clearPassword"})
     */
    protected $clearPassword;

    /**
     *
     * @var string @ORM\Column(name="passwd", type="text", nullable=true)
     */
    protected $password;

    /**
     *
     * @var string @ORM\Column(name="salt", type="text", nullable=true)
     */
    protected $salt;

    /**
     *
     * @var string @ORM\Column(name="recoverycode", type="text", nullable=true)
     */
    protected $recoveryCode;

    /**
     *
     * @var \DateTime @ORM\Column(name="recoveryexpiration", type="datetimetz", nullable=true)
     */
    protected $recoveryExpiration;

    /**
     *
     * @var integer @ORM\Column(name="lockout", type="integer", nullable=false)
     *      @Assert\Choice(callback="choiceLockoutCallback", groups={"lockout"})
     */
    protected $lockout;

    /**
     *
     * @var integer @ORM\Column(name="logins", type="bigint", nullable=false)
     */
    protected $logins;

    /**
     *
     * @var \DateTime @ORM\Column(name="lastlogin", type="datetimetz", nullable=true)
     */
    protected $lastLogin;

    /**
     *
     * @var \DateTime @ORM\Column(name="lastactivity", type="datetimetz", nullable=true)
     */
    protected $lastActivity;

    /**
     *
     * @var integer @ORM\Column(name="sexe", type="integer", nullable=true)
     *      @Assert\Choice(callback="choiceSexeCallback", groups={"sexe"})
     */
    protected $sexe;

    /**
     *
     * @var string @ORM\Column(name="lastname", type="text", nullable=true)
     *      @Assert\Length(min="2", max="200", groups={"lastName"})
     */
    protected $lastName;

    /**
     *
     * @var string @ORM\Column(name="firstname", type="text", nullable=true)
     *      @Assert\Length(min="2", max="200", groups={"firstName"})
     */
    protected $firstName;

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
     * @ORM\ManyToOne(targetEntity="Locale", inversedBy="users", cascade={"persist"})
     * @ORM\JoinColumns({@ORM\JoinColumn(name="locale_id", referencedColumnName="id")})
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_User_locale")
     *
     * @var Locale $locale
     */
    protected $locale;

    /**
     *
     * @var UserPicture @ORM\OneToOne(targetEntity="UserPicture", mappedBy="id" , cascade={"persist", "remove"}, orphanRemoval=true )
     *      @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_User_picture")
     */
    protected $picture;

    /**
     *
     * @var Collection @ORM\ManyToMany(targetEntity="Role", inversedBy="users", cascade={"persist"})
     *      @ORM\JoinTable(name="ilcfrance_users_roles",
     *      joinColumns={
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *      },
     *      inverseJoinColumns={
     *      @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     *      }
     *      )
     *      @ORM\OrderBy({"id" = "ASC"})
     *      @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_User_userRoles")
     */
    protected $userRoles;

    /**
     *
     * @var Collection @ORM\OneToMany(targetEntity="TraineeRecord", mappedBy="teacher", cascade={"persist"})
     *      @ORM\OrderBy({"recordDate" = "ASC"})
     *      @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_User_records")
     */
    protected $records;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setSalt(md5(uniqid(null, true)));
        $this->setClearPassword(self::generateRandomChar(8, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'));
        $this->lockout = self::LOCKOUT_UNLOCKED;
        $this->logins = 0;
        $this->sexe = self::SEXE_MR;
        $this->dtCrea = new \DateTime('now');
        $this->picture = new UserPicture($this);
        $this->userRoles = new ArrayCollection();
        $this->records = new ArrayCollection();
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
     * @return User
     */
    public function setId($id)
    {
        $this->id = \strtolower(\str_replace(' ', '.', \trim($id)));

        return $this;
    }

    /**
     * Get $id
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->id;
    }

    /**
     * Get $email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set $email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = \strtolower(trim($email));

        return $this;
    }

    /**
     * Get $clearPassword
     *
     * @return string
     */
    public function getClearPassword()
    {
        return $this->clearPassword;
    }

    /**
     * Set $clearPassword
     *
     * @param string $clearPassword
     *
     * @return User
     */
    public function setClearPassword($clearPassword)
    {
        $this->clearPassword = $clearPassword;

        return $this->setPassword($clearPassword);
    }

    /**
     * Get $password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set $password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $encoder = new Pbkdf2PasswordEncoder('sha512', true, 1000);
        $this->password = $encoder->encodePassword($password, $this->getSalt());

        return $this;
    }

    /**
     * Get $salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set $salt
     *
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get $recoveryCode
     *
     * @return string
     */
    public function getRecoveryCode()
    {
        return $this->recoveryCode;
    }

    /**
     * Set $recoveryCode
     *
     * @param string $recoveryCode
     *
     * @return User
     */
    public function setRecoveryCode($recoveryCode)
    {
        $this->recoveryCode = $recoveryCode;

        return $this;
    }

    /**
     * Get $recoveryExpiration
     *
     * @return \DateTime
     */
    public function getRecoveryExpiration()
    {
        return $this->recoveryExpiration;
    }

    /**
     * Set $recoveryExpiration
     *
     * @param \DateTime $recoveryExpiration
     *
     * @return User
     */
    public function setRecoveryExpiration(\DateTime $recoveryExpiration = null)
    {
        $this->recoveryExpiration = $recoveryExpiration;

        return $this;
    }

    /**
     * Get $lockout
     *
     * @return integer
     */
    public function getLockout()
    {
        return $this->lockout;
    }

    /**
     * Set $lockout
     *
     * @param integer $lockout
     *
     * @return User
     */
    public function setLockout($lockout)
    {
        $this->lockout = $lockout;

        return $this;
    }

    /**
     * Get $logins
     *
     * @return integer
     */
    public function getLogins()
    {
        return $this->logins;
    }

    /**
     * Set $logins
     *
     * @param integer $logins
     *
     * @return User
     */
    public function setLogins($logins)
    {
        $this->logins = $logins;

        return $this;
    }

    /**
     * Get $lastLogin
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set $lastLogin
     *
     * @param \DateTime $lastLogin
     *
     * @return User
     */
    public function setLastLogin(\DateTime $lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get $lastActivity
     *
     * @return \DateTime
     */
    public function getLastActivity()
    {
        return $this->lastActivity;
    }

    /**
     * Set $lastActivity
     *
     * @param \DateTime $lastActivity
     *
     * @return User
     */
    public function setLastActivity(\DateTime $lastActivity)
    {
        $this->lastActivity = $lastActivity;

        return $this;
    }

    /**
     * Get $sexe
     *
     * @return integer
     */
    public function getSexe()
    {
        return $this->sexe;
    }

    /**
     * Set $sexe
     *
     * @param integer $sexe
     *
     * @return User
     */
    public function setSexe($sexe)
    {
        $this->sexe = $sexe;

        return $this;
    }

    /**
     * Get $lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set $lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get $firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set $firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

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
     * @return User
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
     * @return User
     */
    public function setDtUpdate(\DateTime $dtUpdate = null)
    {
        $this->dtUpdate = $dtUpdate;

        return $this;
    }

    /**
     * Get $locale
     *
     * @return Locale
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set $locale
     *
     * @param Locale $locale
     *
     * @return User
     */
    public function setLocale(Locale $locale = null)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get $picture
     *
     * @return UserPicture
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set $picture
     *
     * @param UserPicture $picture
     *
     * @return User
     */
    public function setPicture(UserPicture $picture)
    {
        $picture->setId($this);

        $this->picture = $picture;

        return $this;
    }

    /**
     * Add $role
     *
     * @param Role $role
     *
     * @return User
     */
    public function addUserRole(Role $role)
    {
        $this->userRoles[] = $role;

        return $this;
    }

    /**
     * Remove $role
     *
     * @param Role $role
     *
     * @return User
     */
    public function removeUserRole(Role $role)
    {
        $this->userRoles->removeElement($role);

        return $this;
    }

    /**
     * Get $userRoles
     *
     * @return ArrayCollection
     */
    public function getUserRoles()
    {
        return $this->userRoles;
    }

    /**
     * Set $userRoles
     *
     * @param Collection $userRoles
     *
     * @return User
     */
    public function setUserRoles(Collection $userRoles)
    {
        $this->userRoles = $userRoles;

        return $this;
    }

    /**
     * Add record
     *
     * @param TraineeRecord $record
     *
     * @return User
     */
    public function addRecord(TraineeRecord $record)
    {
        $this->records[] = $record;

        return $this;
    }

    /**
     * Remove record
     *
     * @param TraineeRecord $record
     *
     * @return User
     */
    public function removeRecord(TraineeRecord $record)
    {
        $this->records->removeElement($record);

        return $this;
    }

    /**
     * Get records
     *
     * @return ArrayCollection
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * Set records
     *
     * @param Collection $records
     *
     * @return User
     */
    public function setRecords(Collection $records)
    {
        $this->records = $records;

        return $this;
    }

    /**
     *
     * {@inheritdoc} @see UserInterface::getRoles()
     */
    public function getRoles()
    {
        $roles = array();
        foreach ($this->userRoles as $role) {
            $roles[] = $role->getId();
            if ($role->getParents()) {
                foreach ($role->getParents() as $parent) {
                    $roles = \array_merge($roles, $this->getRolesParentsIds($parent));
                }
            }
        }
        return $roles;
    }

    /**
     *
     * @param Role $role
     *
     * @return array
     */
    private function getRolesParentsIds(Role $role)
    {
        $roles = array();
        if ($role->getParents()) {
            foreach ($role->getParents() as $parent) {
                $roles = \array_merge($roles, $this->getRolesParentsIds($parent));
            }
        }
        $roles[] = $role->getId();
        return $roles;
    }

    /**
     *
     * {@inheritdoc} @see UserInterface::getRoles()
     */
    public function hasRole(Role $userRole)
    {
        foreach ($this->getRoles() as $role) {
            if ($userRole->getId() == $role) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set the lastActivity to now
     *
     * @return User
     */
    public function isActiveNow()
    {
        return $this->setLastActivity(new \DateTime());
    }

    /**
     * Erases the user credentials.
     *
     * {@inheritdoc} @see UserInterface::eraseCredentials()
     */
    public function eraseCredentials()
    {
        // $this->clearPassword = null;
        $this->recoveryCode = null;
        $this->recoveryExpiration = null;
    }

    /**
     * Choice Form lockout
     *
     * @return multitype:string
     */
    public static function choiceLockout()
    {
        return array(
            'User.lockout.choice.' . self::LOCKOUT_UNLOCKED => self::LOCKOUT_UNLOCKED,
            'User.lockout.choice.' . self::LOCKOUT_LOCKED => self::LOCKOUT_LOCKED
        );
    }

    /**
     * Choice Validator lockout
     *
     * @return multitype:string
     */
    public static function choiceLockoutCallback()
    {
        return array(
            self::LOCKOUT_UNLOCKED,
            self::LOCKOUT_LOCKED
        );
    }

    /**
     * Choice Form sexe
     *
     * @return multitype:string
     */
    public static function choiceSexe()
    {
        return array(
            'User.sexe.choice.' . self::SEXE_MISS => self::SEXE_MISS,
            'User.sexe.choice.' . self::SEXE_MRS => self::SEXE_MRS,
            'User.sexe.choice.' . self::SEXE_MR => self::SEXE_MR
        );
    }

    /**
     * Choice Validator sexe
     *
     * @return multitype:integer
     */
    public static function choiceSexeCallback()
    {
        return array(
            self::SEXE_MISS,
            self::SEXE_MRS,
            self::SEXE_MR
        );
    }

    /**
     * Get a random char (for generated password)
     *
     * @param integer $length
     * @param string $charset
     *
     * @return string
     */
    public static function generateRandomChar($length, $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789#@!?+=*/-')
    {
        $str = '';
        $count = strlen($charset);
        while ($length--) {
            $str .= $charset[mt_rand(0, $count - 1)];
        }

        return $str;
    }

    /**
     * Get calculated fullName From username, firstName and lastName
     *
     * @return string
     */
    public function getFullName()
    {
        if (null == $this->getFirstName() && null == $this->getLastName()) {
            return $this->getUsername();
        } elseif (null != $this->getFirstName() && null != $this->getLastName()) {
            return $this->getFirstName() . ' ' . $this->getLastName();
        } else {
            if (null != $this->getLastName()) {
                return $this->getLastName();
            }
            if (null != $this->getFirstName()) {
                return $this->getFirstName();
            }
        }
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

    /**
     * Serializes the User.
     * The serialized data have to contain the fields used by the equals method
     * and the username.
     *
     * {@inheritdoc} @see Serializable::serialize()
     * @return string
     */
    public function serialize()
    {
        return \serialize(array(
            $this->id,
            $this->email,
            $this->password,
            $this->salt,
            $this->lockout
        ));
    }

    /**
     * Unserializes the User.
     *
     * {@inheritdoc} @see Serializable::unserialize()
     * @param string $serialized
     *
     * @return User
     */
    public function unserialize($serialized)
    {
        $data = \unserialize($serialized);
        // add a few extra elements in the array to ensure that we have enough
        // keys when
        // unserializing
        // older data which does not include all properties.
        $data = array_merge($data, array_fill(0, 2, null));

        list ($this->id, $this->email, $this->password, $this->salt, $this->lockout) = $data;

        return $this;
    }

    public function jsonSerialize()
    {
        $lastLogin = null;
        if (null != $this->lastLogin) {
            $lastLogin = $this->lastLogin->format(\DateTime::ISO8601);
        }
        $lastActivity = null;
        if (null != $this->lastActivity) {
            $lastActivity = $this->lastActivity->format(\DateTime::ISO8601);
        }
        $dtCrea = null;
        if (null != $this->dtCrea) {
            $dtCrea = $this->dtCrea->format(\DateTime::ISO8601);
        }
        $dtUpdate = null;
        if (null != $this->dtUpdate) {
            $dtUpdate = $this->dtUpdate->format(\DateTime::ISO8601);
        }
        $roles = array();
        foreach ($this->getUserRoles() as $role) {
            $roles[] = $role->getId();
        }
        $records = array();
        foreach ($this->getRecords() as $record) {
            $records[] = $record->getId();
        }
        $locale = null;
        if (null != $this->getLocale()) {
            $locale = $this->getLocale()->getId();
        }
        $picture = null;
        if (null != $this->getPicture()) {
            $picture = $this->getPicture()->getUrl();
        }

        return [
            'id' => $this->id,
            'email' => $this->email,
            'lockout' => $this->lockout,
            'logins' => $this->logins,
            'sexe' => $this->sexe,
            'lastName' => $this->lastName,
            'firstName' => $this->firstName,
            'lastLogin' => $lastLogin,
            'lastActivity' => $lastActivity,
            'dtCrea' => $dtCrea,
            'dtUpdate' => $dtUpdate,
            'userRoles' => $roles,
            'records' => $records,
            'locale' => $locale,
            'picture' => $picture
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

        if (null != $this->getLocale()) {
            $related = array(
                'id' => $this->getLocale()->getId(),
                'class' => Locale::class
            );
            $ret[] = $related;
        }

        if (null != $this->getPicture()) {
            $related = array(
                'id' => $this->getId(),
                'class' => UserPicture::class
            );
            $ret[] = $related;
        }

        foreach ($this->getUserRoles() as $role) {
            $related = array(
                'id' => $role->getId(),
                'class' => Role::class
            );
            $ret[] = $related;
        }

        return $ret;
    }
}