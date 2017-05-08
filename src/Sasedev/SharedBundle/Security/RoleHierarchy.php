<?php
namespace Sasedev\SharedBundle\Security;

use Symfony\Component\Security\Core\Role\RoleHierarchy as BaseRoleHierarchy;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
abstract class RoleHierarchy extends BaseRoleHierarchy
{

	/**
	 *
	 * @var RoleManagerInterface
	 */
	private $rm;

	/**
	 *
	 * @var string
	 */
	private $roleClass;

	/**
	 *
	 * @param array $hierarchy
	 * @param string $roleClass
	 */
	public function __construct(RoleManagerInterface $rm, $roleClass)
	{
		$this->rm = $rm;
		$this->roleClass = $roleClass;
		parent::__construct($this->buildRolesTree());
	}

	/**
	 *
	 * @return RoleManagerInterface
	 */
	public function getRoleManager()
	{
		return $this->rm;
	}

	public function getRoleClass()
	{
		return $this->roleClass;
	}

	/**
	 * Here we build an array with roles.
	 * It looks like a two-levelled tree - just
	 * like original Symfony roles are stored in security.yml
	 *
	 * @return array
	 */
	abstract public function buildRolesTree();
	/*
	 * {
	 * $hierarchy = array();
	 * $roles = $this->em->getRepository('SasedevCommonsSharedBundle:Role')
	 * ->getAll();
	 * foreach ($roles as $role) {
	 * if (count($role->getParents()) > 0) {
	 * $roleParents = array();
	 * foreach ($role->getParents() as $parent) {
	 * $roleParents[] = $parent->getRole();
	 * }
	 * $hierarchy[$role->getRole()] = $roleParents;
	 * }
	 * }
	 * return $hierarchy;
	 * }
	 */
}