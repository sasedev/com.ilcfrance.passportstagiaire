<?php
namespace Ilcfrance\Passportstagiaire\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ilcfrance\Passportstagiaire\DataBundle\Model\EntityTraceable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * UserPicture
 *
 * @ORM\Table(name="ilcfrance_users_pictures")
 * @ORM\Entity(repositoryClass="Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\UserPictureRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_UserPicture")
 *
 * @author sasedev <sinus@saseprod.net>
 */
class UserPicture implements EntityTraceable
{

    /**
     *
     * @var User @ORM\Id
     *      @ORM\OneToOne(targetEntity="User", inversedBy="picture", cascade={"persist"})
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *      })
     *      @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_UserPicture_id")
     */
    protected $id;

    /**
     *
     * @var string @ORM\Column(name="pic_url", type="text", nullable=true)
     *      @Assert\NotBlank(groups={"url"})
     */
    protected $url;

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
     * Constructor
     */
    public function __construct(User $user = null)
    {
        $this->dtCrea = new \DateTime('now');

        if (null != $user) {
            $this->setId($user);
        }
    }

    /**
     * get $id
     *
     * @return User
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set $id
     *
     * @param User $id
     *
     * @return UserPicture
     */
    public function setId(User $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get $url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set $url
     *
     * @param string $url
     *
     * @return UserPicture
     */
    public function setUrl($url)
    {
        $this->url = $url;

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
     * @return UserPicture
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
     * @return UserPicture
     */
    public function setDtUpdate(\DateTime $dtUpdate = null)
    {
        $this->dtUpdate = $dtUpdate;

        return $this;
    }

    /**
     * string representation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return ((string) $this->getId()) . $this->getUrl();
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

        return [
            'id' => $this->id->getId(),
            'url' => $this->url,
            'dtCrea' => $dtCrea,
            'dtUpdate' => $dtUpdate
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

        $related = array(
            'id' => $this->getId()->getId(),
            'class' => User::class
        );
        $ret[] = $related;

        return $ret;
    }
}