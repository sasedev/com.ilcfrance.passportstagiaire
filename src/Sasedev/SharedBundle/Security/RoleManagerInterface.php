<?php
namespace Sasedev\SharedBundle\Security;

use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
interface RoleManagerInterface
{

	/**
	 *
	 * @return array
	 */
	public function getRoles();

	/**
	 *
	 * @return string
	 */
	public function getClass();

	/**
	 *
	 * @return ObjectManager
	 */
	public function getEntityManager();

	/**
	 *
	 * @return ObjectRepository
	 */
	public function getEntityRepository();

	/**
	 *
	 * @return RoleInterface
	 */
	public function createRole();

	/**
	 *
	 * @return self
	 */
	public function saveRole(RoleInterface $role);
}