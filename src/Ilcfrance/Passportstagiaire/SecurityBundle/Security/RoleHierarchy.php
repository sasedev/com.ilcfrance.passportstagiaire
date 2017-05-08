<?php
namespace Ilcfrance\Passportstagiaire\SecurityBundle\Security;

use Sasedev\SharedBundle\Security\RoleHierarchy as BaseRoleHierarchy;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class RoleHierarchy extends BaseRoleHierarchy
{

	public function buildRolesTree()
	{
		$hierarchy = array();
		$roles = $this->getRoleManager()->getRoles();
		foreach ($roles as $role) {
			if (count($role->getParents()) > 0) {
				$roleParents = array();
				foreach ($role->getParents() as $parent) {
					$roleParents[] = $parent->getRole();
				}
				$hierarchy[$role->getRole()] = $roleParents;
			}
		}
		return $hierarchy;
	}
}