<?php
namespace Ilcfrance\Passportstagiaire\DataBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ilcfrance\Passportstagiaire\DataBundle\Model\EntityTraceable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TraineeRecord
 *
 * @ORM\Table(name="ilcfrance_trainee_records")
 * @ORM\Entity(repositoryClass="Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\TraineeRecordRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_TraineeRecord")
 *
 * @author sasedev <sinus@saseprod.net>
 */
class TraineeRecord implements EntityTraceable
{
    
    /**
     *
     * @var integer
     */
    const RT_NORMAL = 1;
    
    /**
     *
     * @var integer
     */
    const RT_PHONE = 2;

    /**
     *
     * @var string @ORM\Column(name="id", type="guid", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     *
     * @var Trainee @ORM\ManyToOne(targetEntity="Trainee", inversedBy="records", cascade={"persist"})
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="trainee_id", referencedColumnName="id")
     *      })
     *      @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_TraineeRecord_trainee")
     */
    protected $trainee;

    /**
     *
     * @var TraineeHistorical @ORM\ManyToOne(targetEntity="TraineeHistorical", inversedBy="records", cascade={"persist"})
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="hist_id", referencedColumnName="id")
     *      })
     *      @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_TraineeRecord_historical")
     */
    protected $historical;

    /**
     *
     * @var User @ORM\ManyToOne(targetEntity="User", inversedBy="records", cascade={"persist"})
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="teacher_id", referencedColumnName="id")
     *      })
     *      @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_TraineeRecord_teacher")
     */
    protected $teacher;

    /**
     *
     * @var string @ORM\Column(name="teacher_name", type="text", nullable=true)
     */
    protected $teacherName;

    /**
     *
     * @var \DateTime @ORM\Column(name="record_date", type="datetimetz", nullable=true)
     *      @Assert\NotNull(groups={"recordDate"})
     */
    protected $recordDate;
    
    /**
     *
     * @var integer @ORM\Column(name="record_type", type="integer", nullable=false)
     * @Assert\Choice(callback="choiceRecordTypeCallback", groups={"recordType"})
     */
    protected $recordType;

    /**
     *
     * @var string @ORM\Column(name="works_covered", type="text", nullable=true)
     *      @Assert\NotBlank(groups={"worksCovered"})
     */
    protected $worksCovered;

    /**
     *
     * @var string @ORM\Column(name="takeaways", type="text", nullable=true)
     */
    protected $takeaways;

    /**
     *
     * @var string @ORM\Column(name="comments", type="text", nullable=true)
     *      @Assert\NotBlank(groups={"comments"})
     */
    protected $comments;

    /**
     *
     * @var string @ORM\Column(name="homeworks", type="text", nullable=true)
     */
    protected $homeworks;
    
    /**
     *
     * @var string @ORM\Column(name="correctionvocabulairy", type="text", nullable=true)
     */
    protected $correctionVocabulairy;
    
    /**
     *
     * @var string @ORM\Column(name="correctionstructure", type="text", nullable=true)
     */
    protected $correctionStructure;
    
    /**
     *
     * @var string @ORM\Column(name="correctionprononciation", type="text", nullable=true)
     */
    protected $correctionPrononciation;
    
    /**
     *
     * @var string @ORM\Column(name="mailcomments", type="text", nullable=true)
     */
    protected $mailComments;
    
    /**
     *
     * @var integer @ORM\Column(name="fileemls",  type="bigint", nullable=false)
     */
    protected $nbrEmails;

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
     * @var Collection @ORM\OneToMany(targetEntity="TraineeRecordDocument", mappedBy="traineeRecord", cascade={"persist", "remove"})
     *      @ORM\OrderBy({"fileName" = "ASC"})
     *      @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_TraineeRecord_docs")
     */
    protected $docs;
    
    /**
     *
     * @var Collection @ORM\OneToMany(targetEntity="TraineeRecordHomework", mappedBy="traineeRecord", cascade={"persist", "remove"})
     *      @ORM\OrderBy({"dtCrea" = "ASC"})
     *      @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_TraineeRecord_hws")
     */
    protected $hws;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->recordType = self::RT_NORMAL;
        $this->nbrEmails = 0;
        $this->dtCrea = new \DateTime('now');
        $this->docs = new ArrayCollection();
        $this->hws = new ArrayCollection();
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
     * @return TraineeRecord
     */
    public function setTrainee(Trainee $trainee)
    {
        $this->trainee = $trainee;

        return $this;
    }

    /**
     * Get $historical
     *
     * @return TraineeHistorical
     */
    public function getHistorical()
    {
        return $this->historical;
    }

    /**
     * Set $historical
     *
     * @param TraineeHistorical $historical
     *
     * @return TraineeRecord $this
     */
    public function setHistorical($historical)
    {
        $this->historical = $historical;

        if (null != $historical) {
            $this->setTrainee($historical->getTrainee());
        }

        return $this;
    }

    /**
     * Get $teacher
     *
     * @return User
     */
    public function getTeacher()
    {
        return $this->teacher;
    }

    /**
     * Set $teacher
     *
     * @param User $teacher
     *
     * @return TraineeRecord
     */
    public function setTeacher(User $teacher = null)
    {
        $this->teacher = $teacher;

        return $this;
    }

    /**
     * Get $teacherName
     *
     * @return string
     */
    public function getTeacherName()
    {
        return $this->teacherName;
    }

    /**
     * Set $teacherName
     *
     * @param string $teacherName
     *
     * @return TraineeRecord
     */
    public function setTeacherName($teacherName)
    {
        $this->teacherName = $teacherName;

        return $this;
    }

    /**
     * Get $recordDate
     *
     * @return \DateTime
     */
    public function getRecordDate()
    {
        return $this->recordDate;
    }

    /**
     * Set $recordDate
     *
     * @param \DateTime $recordDate
     *
     * @return TraineeRecord
     */
    public function setRecordDate(\DateTime $recordDate = null)
    {
        $this->recordDate = $recordDate;

        return $this;
    }

    /**
     * Get $recordType
     *
     * @return integer
     */
    public function getRecordType()
    {
        return $this->recordType;
    }

    /**
     * Set $recordType
     *
     * @param integer $recordType
     *
     * @return TraineeRecord $this
     */
    public function setRecordType($recordType)
    {
        $this->recordType = $recordType;
        
        return $this;
    }

    /**
     * Get $worksCovered
     *
     * @return string
     */
    public function getWorksCovered()
    {
        return $this->worksCovered;
    }

    /**
     * Set $worksCovered
     *
     * @param string $worksCovered
     *
     * @return TraineeRecord
     */
    public function setWorksCovered($worksCovered)
    {
        $this->worksCovered = $worksCovered;

        return $this;
    }

    /**
     * Get $takeaways
     *
     * @return string
     */
    public function getTakeaways()
    {
        return $this->takeaways;
    }

    /**
     * Set $takeaways
     *
     * @param string $takeaways
     *
     * @return TraineeRecord
     */
    public function setTakeaways($takeaways)
    {
        $this->takeaways = $takeaways;

        return $this;
    }

    /**
     * Get $comments
     *
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set $comments
     *
     * @param string $comments
     *
     * @return TraineeRecord
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get $homeworks
     *
     * @return string
     */
    public function getHomeworks()
    {
        return $this->homeworks;
    }

    /**
     * Set $homeworks
     *
     * @param string $homeworks
     *
     * @return TraineeRecord
     */
    public function setHomeworks($homeworks)
    {
        $this->homeworks = $homeworks;

        return $this;
    }

    /**
     * Get $correctionVocabulairy
     *
     * @return string
     */
    public function getCorrectionVocabulairy()
    {
        return $this->correctionVocabulairy;
    }

    /**
     * Set $correctionVocabulairy
     *
     * @param string $correctionVocabulairy
     *
     * @return TraineeRecord $this
     */
    public function setCorrectionVocabulairy($correctionVocabulairy)
    {
        $this->correctionVocabulairy = $correctionVocabulairy;
        
        return $this;
    }

    /**
     * Get $correctionStructure
     *
     * @return string
     */
    public function getCorrectionStructure()
    {
        return $this->correctionStructure;
    }

    /**
     * Set $correctionStructure
     *
     * @param string $correctionStructure
     *
     * @return TraineeRecord $this
     */
    public function setCorrectionStructure($correctionStructure)
    {
        $this->correctionStructure = $correctionStructure;
        
        return $this;
    }

    /**
     * Get $correctionPrononciation
     *
     * @return string
     */
    public function getCorrectionPrononciation()
    {
        return $this->correctionPrononciation;
    }

    /**
     * Set $correctionPrononciation
     *
     * @param string $correctionPrononciation
     *
     * @return TraineeRecord $this
     */
    public function setCorrectionPrononciation($correctionPrononciation)
    {
        $this->correctionPrononciation = $correctionPrononciation;
        
        return $this;
    }

    /**
     * Get $mailComments
     *
     * @return string
     */
    public function getMailComments()
    {
        return $this->mailComments;
    }

    /**
     * Set $mailComments
     *
     * @param string $mailComments
     *
     * @return TraineeRecord $this
     */
    public function setMailComments($mailComments)
    {
        $this->mailComments = $mailComments;
        
        return $this;
    }

    /**
     * Get $nbrEmails
     *
     * @return integer
     */
    public function getNbrEmails()
    {
        return $this->nbrEmails;
    }

    /**
     * Set $nbrEmails
     *
     * @param integer $nbrEmails
     *
     * @return TraineeRecord $this
     */
    public function setNbrEmails($nbrEmails)
    {
        $this->nbrEmails = $nbrEmails;
        
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
     * @return TraineeRecord
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
     * @return TraineeRecord
     */
    public function setDtUpdate(\DateTime $dtUpdate = null)
    {
        $this->dtUpdate = $dtUpdate;

        return $this;
    }

    /**
     * Add doc
     *
     * @param TraineeRecordDocument $doc
     *
     * @return TraineeRecord
     */
    public function addDoc(TraineeRecordDocument $doc)
    {
        $this->docs[] = $doc;

        return $this;
    }

    /**
     * Remove doc
     *
     * @param TraineeRecordDocument $doc
     *
     * @return TraineeRecord
     */
    public function removeDoc(TraineeRecordDocument $doc)
    {
        $this->docs->removeElement($doc);

        return $this;
    }

    /**
     * Get docs
     *
     * @return ArrayCollection
     */
    public function getDocs()
    {
        return $this->docs;
    }

    /**
     * Set docs
     *
     * @param Collection $docs
     *
     * @return TraineeRecord
     */
    public function setDocs(Collection $docs)
    {
        $this->docs = $docs;

        return $this;
    }
    
    /**
     * Add hw
     *
     * @param TraineeRecordHomework $hw
     *
     * @return TraineeRecord
     */
    public function addHw(TraineeRecordHomework $hw)
    {
        $this->hws[] = $hw;
        
        return $this;
    }
    
    /**
     * Remove hw
     *
     * @param TraineeRecordHomework $hw
     *
     * @return TraineeRecord
     */
    public function removeHw(TraineeRecordHomework $hw)
    {
        $this->hws->removeElement($hw);
        
        return $this;
    }
    
    /**
     * Get hws
     *
     * @return ArrayCollection
     */
    public function getHws()
    {
        return $this->hws;
    }
    
    /**
     * Set hws
     *
     * @param Collection $hws
     *
     * @return TraineeRecord
     */
    public function setHws(Collection $hws)
    {
        $this->hws = $hws;
        
        return $this;
    }

    /**
     * Get $fullName
     *
     * @return string
     */
    public function getFullName()
    {
        $fullName = '';

        if (null != $this->getRecordDate()) {
            $fullName .= $this->getRecordDate()->format('Y-m-d') . ' ' . $this->getRecordDate()->format('H:i:s') . ' ';
        }

        if (null != $this->getHistorical()) {
            $fullName .= '(' . $this->getHistorical()->getYear() . ' ' . $this->getHistorical()->getOrigine() . ') ';
        }

        $fullName .= ' - ' . $this->getTrainee()->getFullName();

        return $fullName;
    }

    /**
     * Get $smallName
     *
     * @return string
     */
    public function getSmallName()
    {
        $smallName = '';

        if (null != $this->getRecordDate()) {
            $smallName .= $this->getRecordDate()->format('Y-m-d') . ' ' . $this->getRecordDate()->format('H:i:s') . ' ';
        }

        return $smallName;
    }
    
    /**
     * Choice Form status
     *
     * @return multitype:string
     */
    public static function choiceRecordType()
    {
        return array(
            'TraineeRecord.recordType.choice.' . self::RT_NORMAL => self::RT_NORMAL,
            'TraineeRecord.recordType.choice.' . self::RT_PHONE => self::RT_PHONE
        );
    }
    
    /**
     * Choice Validator status
     *
     * @return multitype:integer
     */
    public static function choiceRecordTypeCallback()
    {
        return array(
            self::RT_NORMAL,
            self::RT_PHONE
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
        $recordDate = null;
        if (null != $this->recordDate) {
            $recordDate = $this->recordDate->format(\DateTime::ISO8601);
        }
        $teacher = null;
        if (null != $this->teacher) {
            $teacher = $this->teacher->getId();
        }
        $historical = null;
        if (null != $this->historical) {
            $historical = $this->historical->getId();
        }
        $docs = array();
        foreach ($this->getDocs() as $doc) {
            $docs[] = $doc->getId();
        }
        $hws = array();
        foreach ($this->getHws() as $hw) {
            $hws[] = $hw->getId();
        }

        return [
            'id' => $this->id,
            'trainee' => $this->trainee->getId(),
            'historical' => $historical,
            'teacher' => $teacher,
            'teacherName' => $this->teacherName,
            'recordDate' => $recordDate,
            'recordType' => $this->recordType,
            'worksCovered' => $this->worksCovered,
            'comments' => $this->comments,
            'homeworks' => $this->homeworks,
            'correctionVocabulairy' => $this->correctionVocabulairy,
            'correctionStructure' => $this->correctionStructure,
            'correctionPrononciation' => $this->correctionPrononciation,
            'mailComments' => $this->mailComments,
            'nbrEmails' => $this->nbrEmails,
            'dtCrea' => $dtCrea,
            'dtUpdate' => $dtUpdate,
            'docs' => $docs,
            'hws' => $hws
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

        if (null != $this->getHistorical()) {
            $related = array(
                'id' => $this->getHistorical()->getId(),
                'class' => TraineeHistorical::class
            );
            $ret[] = $related;
        }

        if (null != $this->getTeacher()) {
            $related = array(
                'id' => $this->getTeacher()->getId(),
                'class' => User::class
            );
            $ret[] = $related;
        }

        return $ret;
    }
}