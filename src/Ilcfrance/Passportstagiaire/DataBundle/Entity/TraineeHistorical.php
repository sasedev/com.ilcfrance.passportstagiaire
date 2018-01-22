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
 * TraineeHistorical
 * @ORM\Table(name="ilcfrance_trainee_historicals")
 * @ORM\Entity(repositoryClass="Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\TraineeHistoricalRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_TraineeHistorical")
 * @UniqueEntity(fields={"trainee", "year", "origine"}, errorPath="year", groups={"trainee", "year", "origine", "Default"})
 *
 * @author sasedev <sinus@saseprod.net>
 */
class TraineeHistorical implements EntityTraceable
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
    const TRAINEE_OVERRIDE_NO = 1;

    /**
     *
     * @var integer
     */
    const TRAINEE_OVERRIDE_YES = 2;

    /**
     *
     * @var string @ORM\Column(name="id", type="guid", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     *
     * @var Trainee @ORM\ManyToOne(targetEntity="Trainee", inversedBy="historicals", cascade={"persist"})
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="trainee_id", referencedColumnName="id")
     *      })
     *      @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_TraineeHistorical_trainee")
     */
    protected $trainee;

    /**
     *
     * @var string @ORM\Column(name="year", type="text", nullable=false)
     *      @Assert\NotNull(groups={"year"})
     */
    protected $year;

    /**
     *
     * @var string @ORM\Column(name="initlevel", type="text", nullable=true)
     */
    protected $initLevel;

    /**
     *
     * @var string @ORM\Column(name="level", type="text", nullable=true)
     */
    protected $level;

    /**
     *
     * @var string @ORM\Column(name="needs", type="text", nullable=true)
     */
    protected $needs;

    /**
     *
     * @var string @ORM\Column(name="courses", type="text", nullable=true)
     */
    protected $courses;

    /**
     *
     * @var string @ORM\Column(name="origine", type="text", nullable=true)
     */
    protected $origine;

    /**
     *
     * @var integer @ORM\Column(name="lockout", type="integer", nullable=false)
     *      @Assert\Choice(callback="choiceLockoutCallback", groups={"lockout"})
     */
    protected $lockout;

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
     * @var Collection @ORM\OneToMany(targetEntity="TraineeRecord", mappedBy="historical", cascade={"persist", "remove"})
     *      @ORM\OrderBy({"recordDate" = "DESC"})
     *      @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_TraineeHistorical_records")
     */
    protected $records;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lockout = self::LOCKOUT_UNLOCKED;
        $this->year = date("Y");
        $this->dtCrea = new \DateTime('now');
        $this->records = new ArrayCollection();
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Ilcfrance\Passportstagiaire\DataBundle\Model\EntityTraceable::getId()
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get $trainee
     *
     * @return Trainee
     */
    public function getTrainee()
    {
        return $this->trainee;
    }

    /**
     *
     * @param Trainee $trainee
     *
     * @return TraineeHistorical
     */
    public function setTrainee(Trainee $trainee)
    {
        $this->trainee = $trainee;

        return $this;
    }

    /**
     * Get $year
     *
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set $year
     *
     * @param string $year
     *
     * @return TraineeHistorical $this
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get $initLevel
     *
     * @return string
     */
    public function getInitLevel()
    {
        return $this->initLevel;
    }

    /**
     * Set $initLevel
     *
     * @param string $initLevel
     *
     * @return TraineeHistorical $this
     */
    public function setInitLevel($initLevel)
    {
        $this->initLevel = $initLevel;

        return $this;
    }

    /**
     * Get $level
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set $level
     *
     * @param string $level
     *
     * @return TraineeHistorical $this
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get $needs
     *
     * @return string
     */
    public function getNeeds()
    {
        return $this->needs;
    }

    /**
     * Set $needs
     *
     * @param string $needs
     *
     * @return TraineeHistorical $this
     */
    public function setNeeds($needs)
    {
        $this->needs = $needs;

        return $this;
    }

    /**
     * Get $courses
     *
     * @return string
     */
    public function getCourses()
    {
        return $this->courses;
    }

    /**
     * Set $courses
     *
     * @param string $courses
     *
     * @return TraineeHistorical $this
     */
    public function setCourses($courses)
    {
        $this->courses = $courses;

        return $this;
    }

    /**
     * Get $origine
     *
     * @return string
     */
    public function getOrigine()
    {
        return $this->origine;
    }

    /**
     * Set $origine
     *
     * @param string $origine
     *
     * @return TraineeHistorical $this
     */
    public function setOrigine($origine)
    {
        $this->origine = $origine;

        return $this;
    }

    /**
     * Get $lockout
     *
     * @return number
     */
    public function getLockout()
    {
        return $this->lockout;
    }

    /**
     * Set $lockout
     *
     * @param number $lockout
     *
     * @return TraineeHistorical $this
     */
    public function setLockout($lockout)
    {
        $this->lockout = $lockout;

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
     * @return TraineeHistorical
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
     * @return TraineeHistorical
     */
    public function setDtUpdate(\DateTime $dtUpdate = null)
    {
        $this->dtUpdate = $dtUpdate;

        return $this;
    }

    /**
     * Add record
     *
     * @param TraineeRecord $record
     *
     * @return TraineeHistorical
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
     * @return TraineeHistorical
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
     * @return TraineeHistorical
     */
    public function setRecords(Collection $records)
    {
        $this->records = $records;

        return $this;
    }

    /**
     * Choice Form lockout
     *
     * @return multitype:string
     */
    public static function choiceLockout()
    {
        return array(
            'TraineeHistorical.lockout.choice.' . self::LOCKOUT_UNLOCKED => self::LOCKOUT_UNLOCKED,
            'TraineeHistorical.lockout.choice.' . self::LOCKOUT_LOCKED => self::LOCKOUT_LOCKED
        );
    }

    /**
     * Get $fullName
     *
     * @return string
     */
    public function getFullName()
    {
        $fullName = $this->getYear() . ' ';
        $fullName .= $this->getOrigine() . ' - ';
        $fullName .= $this->getTrainee()->getFullName();

        return $fullName;
    }

    /**
     * Get $smallName
     *
     * @return string
     */
    public function getSmallName()
    {
        $smallName = $this->getYear() . ' ';
        $smallName .= $this->getOrigine();

        return $smallName;
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
     * Choice Form lockout
     *
     * @return multitype:string
     */
    public static function choiceTraineeOverride()
    {
        return array(
            'TraineeHistorical.traineeOverride.choice.' . self::TRAINEE_OVERRIDE_NO => self::TRAINEE_OVERRIDE_NO,
            'TraineeHistorical.traineeOverride.choice.' . self::TRAINEE_OVERRIDE_YES => self::TRAINEE_OVERRIDE_YES
        );
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
        $records = array();
        foreach ($this->getRecords() as $record) {
            $records[] = $record->getId();
        }

        return [
            'id' => $this->id,
            'trainee' => $this->trainee->getId(),
            'year' => $this->year,
            'initLevel' => $this->initLevel,
            'level' => $this->level,
            'needs' => $this->needs,
            'courses' => $this->courses,
            'origine' => $this->origine,
            'lockout' => $this->lockout,
            'dtCrea' => $dtCrea,
            'dtUpdate' => $dtUpdate,
            'records' => $records
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
            'id' => $this->getTrainee()->getId(),
            'class' => Trainee::class
        );
        $ret[] = $related;

        return $ret;
    }
}