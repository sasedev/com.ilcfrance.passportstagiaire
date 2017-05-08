<?php
namespace Ilcfrance\Passportstagiaire\SecurityBundle\Security;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Sasedev\SharedBundle\Security\RoleManagerInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class RoleManager implements RoleManagerInterface
{

	/**
	 *
	 * @var string
	 */
	protected $class;

	/**
	 *
	 * @var ObjectManager
	 */
	protected $entity_manager;

	/**
	 *
	 * @var ObjectRepository
	 */
	protected $entity_repository;

	/**
	 *
	 * @param ManagerRegistry $manager_registry
	 * @param string $class
	 */
	public function __construct(ManagerRegistry $manager_registry, $class)
	{
		$this->class = $class;
		$this->entity_manager = $manager_registry->getManagerForClass($class);
		$this->entity_repository = $this->entity_manager->getRepository($class);
	}

	/**
	 *
	 * {@inheritdoc} @see RoleManagerInterface::getEntityManager()
	 */
	public function getEntityManager()
	{
		return $this->entity_manager;
	}

	/**
	 *
	 * {@inheritdoc} @see RoleManagerInterface::getEntityRepository()
	 */
	public function getEntityRepository()
	{
		return $this->entity_repository;
	}

	/**
	 *
	 * {@inheritdoc} @see RoleManagerInterface::getRoles()
	 */
	public function getRoles()
	{
		return $this->getEntityRepository()->findAll();
	}

	/**
	 *
	 * {@inheritdoc} @see RoleManagerInterface::getClass()
	 */
	public function getClass()
	{
		return $this->class;
	}

	/**
	 *
	 * {@inheritdoc} @see RoleManagerInterface::createRole()
	 */
	public function createRole()
	{
		$class = $this->getClass();
		return new $class();
	}

	/**
	 *
	 * {@inheritdoc} @see RoleManagerInterface::saveRole()
	 */
	public function saveRole(RoleInterface $role)
	{
		$this->getEntityManager()->persist($role);
		$this->getEntityManager()->flush();
		return $this;
	}
}