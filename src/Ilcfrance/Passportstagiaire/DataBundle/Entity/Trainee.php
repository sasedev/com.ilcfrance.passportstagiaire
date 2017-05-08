<?php
namespace Ilcfrance\Passportstagiaire\DataBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable as GedmoTimestampable;
use Ilcfrance\Passportstagiaire\DataBundle\Model\EntityTraceable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Trainee
 * @ORM\Table(name="ilcfrance_trainees")
 * @ORM\Entity(repositoryClass="Ilcfrance\Passportstagiaire\DataBundle\Repository\TraineeRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_Trainee")
 * @UniqueEntity(fields={"firstName", "lastName"}, errorPath="lastName", groups={"firstName", "lastName"})
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class Trainee implements EntityTraceable
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
	 * @var string @ORM\Column(name="lname", type="text", nullable=true)
	 *      @Assert\Length(min="2", max="200", groups={"lastName"})
	 */
	protected $lastName;

	/**
	 *
	 * @var string @ORM\Column(name="fname", type="text", nullable=true)
	 *      @Assert\Length(min="2", max="200", groups={"firstName"})
	 */
	protected $firstName;

	/**
	 *
	 * @var string @ORM\Column(name="address", type="text", nullable=true)
	 */
	protected $address;

	/**
	 *
	 * @var string @ORM\Column(name="town", type="text", nullable=true)
	 */
	protected $town;

	/**
	 *
	 * @var string @ORM\Column(name="email", type="text", nullable=true)
	 *      @Assert\Email(checkMX=true)
	 */
	protected $email;

	/**
	 *
	 * @var string @ORM\Column(name="phone", type="text", nullable=true)
	 */
	protected $phone;

	/**
	 *
	 * @var string @ORM\Column(name="mobile", type="text", nullable=true)
	 */
	protected $mobile;

	/**
	 *
	 * @var string @ORM\Column(name="job", type="text", nullable=true)
	 */
	protected $job;

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
	 * @var Collection @ORM\OneToMany(targetEntity="TraineeRecord", mappedBy="trainee", cascade={"persist", "remove"})
	 *      @ORM\OrderBy({"recordDate" = "ASC"})
	 *      @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_Trainee_records")
	 */
	protected $records;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->dtCrea = new \DateTime('now');
		$this->records = new ArrayCollection();
		$this->origine = "PASSPORT";
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
	 * @return Trainee
	 */
	public function setLastName($lastName)
	{
		$this->lastName = \strtolower($lastName);

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
	 * @return Trainee
	 */
	public function setFirstName($firstName)
	{
		$this->firstName = \strtolower($firstName);

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getAddress()
	{
		return $this->address;
	}

	/**
	 *
	 * @param string $address
	 *
	 * @return Trainee
	 */
	public function setAddress($address)
	{
		$this->address = $address;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getTown()
	{
		return $this->town;
	}

	/**
	 *
	 * @param string $town
	 *
	 * @return Trainee
	 */
	public function setTown($town)
	{
		$this->town = $town;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 *
	 * @param string $email
	 *
	 * @return Trainee
	 */
	public function setEmail($email)
	{
		$this->email = $email;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getPhone()
	{
		return $this->phone;
	}

	/**
	 *
	 * @param string $phone
	 *
	 * @return Trainee
	 */
	public function setPhone($phone)
	{
		$this->phone = $phone;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getMobile()
	{
		return $this->mobile;
	}

	/**
	 *
	 * @param string $mobile
	 *
	 * @return Trainee
	 */
	public function setMobile($mobile)
	{
		$this->mobile = $mobile;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getJob()
	{
		return $this->job;
	}

	/**
	 *
	 * @param string $job
	 *
	 * @return Trainee
	 */
	public function setJob($job)
	{
		$this->job = $job;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getInitLevel()
	{
		return $this->initLevel;
	}

	/**
	 *
	 * @param string $initLevel
	 *
	 * @return Trainee
	 */
	public function setInitLevel($initLevel)
	{
		$this->initLevel = $initLevel;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getLevel()
	{
		return $this->level;
	}

	/**
	 *
	 * @param string $level
	 */
	public function setLevel($level)
	{
		$this->level = $level;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getNeeds()
	{
		return $this->needs;
	}

	/**
	 *
	 * @param string $needs
	 *
	 * @return Trainee
	 */
	public function setNeeds($needs)
	{
		$this->needs = $needs;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getCourses()
	{
		return $this->courses;
	}

	/**
	 *
	 * @param string $courses
	 *
	 * @return Trainee
	 */
	public function setCourses($courses)
	{
		$this->courses = $courses;

		return $this;
	}

	/**
	 *
	 * @return the string
	 */
	public function getOrigine()
	{
		return $this->origine;
	}

	/**
	 *
	 * @param string $origine
	 *
	 * @return Trainee
	 */
	public function setOrigine($origine)
	{
		$this->origine = $origine;

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
	 * @return Trainee
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
	 * @return Trainee
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
	 * @return Trainee
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
	 * @return Trainee
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
	 * @return Trainee
	 */
	public function setRecords(Collection $records)
	{
		$this->records = $records;

		return $this;
	}

	public function getFullName()
	{
		return $this->lastName . ' ' . $this->firstName;
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
			'lastName' => $this->lastName,
			'firstName' => $this->firstName,
			'address' => $this->address,
			'town' => $this->town,
			'job' => $this->job,
			'initLevel' => $this->initLevel,
			'level' => $this->level,
			'needs' => $this->needs,
			'courses' => $this->courses,
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
		foreach ($this->getRecords() as $record) {
			$related = array(
				'id' => $record->getId(),
				'class' => TraineeRecord::class
			);
			$ret[] = $related;
		}
		return $ret;
	}
}