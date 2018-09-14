<?php
namespace Ilcfrance\Passportstagiaire\DataBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ilcfrance\Passportstagiaire\DataBundle\Model\EntityTraceable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Level
 *
 * @ORM\Table(name="ilcfrance_levels")
 * @ORM\Entity(repositoryClass="Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\LevelRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_Level")
 * @UniqueEntity(fields={"name"}, errorPath="name", groups={"name"})
 *
 * @author sasedev <sinus@saseprod.net>
 */
class Level implements EntityTraceable
{

    /**
     *
     * @var string @ORM\Column(name="id", type="guid", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     *
     * @var string @ORM\Column(name="name", type="text", nullable=false)
     */
    protected $name;

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
     * @var Collection @ORM\OneToMany(targetEntity="Homework", mappedBy="level", cascade={"persist", "remove"})
     *      @ORM\OrderBy({"fileName" = "ASC"})
     *      @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_Level_homeworks")
     */
    protected $homeworks;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->size = 0;
        $this->nbrDownloads = 0;
        $this->dtCrea = new \DateTime('now');
        $this->homeworks = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get $name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set $name
     *
     * @param string $name
     *
     * @return Level $this
     */
    public function setName($name)
    {
        $this->name = $name;
        
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
     * @return Level $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        
        return $this;
    }

    /**
     * Get dtCrea
     *
     * @return \DateTime
     */
    public function getDtCrea()
    {
        return $this->dtCrea;
    }

    /**
     * Set dtCrea
     *
     * @param \DateTime $dtCrea
     *
     * @return Document
     */
    public function setDtCrea($dtCrea)
    {
        $this->dtCrea = $dtCrea;

        return $this;
    }

    /**
     * Get dtUpdate
     *
     * @return \DateTime
     */
    public function getDtUpdate()
    {
        return $this->dtUpdate;
    }

    /**
     * Set dtUpdate
     *
     * @param \DateTime $dtUpdate
     *
     * @return Document
     */
    public function setDtUpdate($dtUpdate)
    {
        $this->dtUpdate = $dtUpdate;

        return $this;
    }
    
    /**
     * Add homework
     *
     * @param Homework $homework
     *
     * @return Level
     */
    public function addHomework(TraineeRecordHomework $homework)
    {
        $this->homeworks[] = $homework;
        
        return $this;
    }
    
    /**
     * Remove homework
     *
     * @param Homework $homework
     *
     * @return Level
     */
    public function removeHomework(TraineeRecordHomework $homework)
    {
        $this->homeworks->removeElement($homework);
        
        return $this;
    }
    
    /**
     * Get homeworks
     *
     * @return ArrayCollection
     */
    public function getHomeworks()
    {
        return $this->homeworks;
    }
    
    /**
     * Set homeworks
     *
     * @param Collection $homeworks
     *
     * @return Level
     */
    public function setHomeworks(Collection $homeworks)
    {
        $this->homeworks = $homeworks;
        
        return $this;
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
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
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
        return $ret;
    }
}