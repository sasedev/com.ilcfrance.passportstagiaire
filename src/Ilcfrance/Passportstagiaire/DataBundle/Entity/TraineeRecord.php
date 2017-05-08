<?php
namespace Ilcfrance\Passportstagiaire\DataBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable as GedmoTimestampable;
use Ilcfrance\Passportstagiaire\DataBundle\Model\EntityTraceable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TraineeRecord
 * @ORM\Table(name="ilcfrance_trainee_records")
 * @ORM\Entity(repositoryClass="Ilcfrance\Passportstagiaire\DataBundle\Repository\TraineeRecordRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_TraineeRecord")
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class TraineeRecord implements EntityTraceable
{

	/**
	 *
	 * @var guid @ORM\Column(name="id", type="guid", nullable=false)
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
	 * @var string @ORM\Column(name="works_covered", type="text", nullable=true)
	 *      @Assert\NotBlank(groups={"worksCovered"})
	 */
	protected $worksCovered;

	/**
	 *
	 * @var string @ORM\Column(name="takeaways", type="text", nullable=true)
	 *      @Assert\NotBlank(groups={"takeaways"})
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
	 * @var \DateTime @ORM\Column(name="created_at", type="datetimetz", nullable=true)
	 */
	protected $dtCrea;

	/**
	 *
	 * @var \DateTime @ORM\Column(name="updated_at", type="datetimetz", nullable=true)
	 *      @GedmoTimestampable(on="update")
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
	 * Constructor
	 */
	public function __construct()
	{
		$this->dtCrea = new \DateTime('now');
		$this->docs = new ArrayCollection();
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
	 * Get $stagaire
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
		$docs = array();
		foreach ($this->getDocs() as $doc) {
			$docs[] = $doc->getId();
		}

		return [
			'id' => $this->id,
			'trainee' => $this->trainee->getId(),
			'teacher' => $teacher,
			'teacherName' => $this->teacherName,
			'recordDate' => $recordDate,
			'worksCovered' => $this->worksCovered,
			'comments' => $this->comments,
			'homeworks' => $this->homeworks,
			'dtCrea' => $dtCrea,
			'dtUpdate' => $dtUpdate,
			'docs' => $docs
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