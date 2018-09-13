<?php
namespace Ilcfrance\Passportstagiaire\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ilcfrance\Passportstagiaire\DataBundle\Model\EntityTraceable;

/**
 * TraineeRecordHomework
 *
 * @ORM\Table(name="ilcfrance_trainee_record_homeworks")
 * @ORM\Entity(repositoryClass="Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\TraineeRecordHomeworkRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_TraineeRecordHomework")
 *
 * @author sasedev <sinus@saseprod.net>
 */
class TraineeRecordHomework implements EntityTraceable
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
     * @var TraineeRecord @ORM\ManyToOne(targetEntity="TraineeRecord", inversedBy="hws", cascade={"persist"})
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="record_id", referencedColumnName="id")
     *      })
     *      @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_TraineeRecordHomework_traineeRecord")
     */
    protected $traineeRecord;
    
    /**
     *
     * @var Homework @ORM\ManyToOne(targetEntity="Homework", inversedBy="hws", cascade={"persist"})
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="homework_id", referencedColumnName="id")
     *      })
     *      @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_TraineeRecordHomework_homework")
     */
    protected $homework;

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
    public function __construct()
    {
        $this->dtCrea = new \DateTime('now');
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
     * Get $traineeRecord
     *
     * @return TraineeRecord
     */
    public function getTraineeRecord()
    {
        return $this->traineeRecord;
    }

    /**
     *
     * @param TraineeRecord $traineeRecord
     *
     * @return TraineeRecordHomework
     */
    public function setTraineeRecord(TraineeRecord $traineeRecord)
    {
        $this->traineeRecord = $traineeRecord;

        return $this;
    }
    
    /**
     * Get $homework
     *
     * @return Homework
     */
    public function getHomework()
    {
        return $this->homework;
    }
    
    /**
     *
     * @param Homework $homework
     *
     * @return TraineeRecordHomework
     */
    public function setHomework(Homework $homework)
    {
        $this->homework = $homework;
        
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
     * @return TraineeRecordHomework
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
     * @return TraineeRecordHomework
     */
    public function setDtUpdate($dtUpdate)
    {
        $this->dtUpdate = $dtUpdate;

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
            'traineeRecord' => $this->traineeRecord->getId(),
            'homework' => $this->homework->getId(),
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
            'id' => $this->getTraineeRecord()->getId(),
            'class' => TraineeRecord::class
        );
        $ret[] = $related;
        
        $related = array(
            'id' => $this->getHomework()->getId(),
            'class' => Homework::class
        );
        $ret[] = $related;
        
        return $ret;
    }
}