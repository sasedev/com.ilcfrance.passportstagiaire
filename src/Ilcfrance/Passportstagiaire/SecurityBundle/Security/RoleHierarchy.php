<?php
namespace Ilcfrance\Passportstagiaire\SecurityBundle\Security;

use Sasedev\SharedBundle\Security\RoleManagerInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchy as BaseRoleHierarchy;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
class RoleHierarchy extends BaseRoleHierarchy
{

    /**
     *
     * @var RoleManagerInterface
     */
    protected $rm;

    /**
     *
     * @param RoleManagerInterface $rm
     */
    public function __construct(RoleManagerInterface $rm)
    {
        $this->rm = $rm;
        $map = $this->buildRolesTree();
        parent::__construct($map);
    }

    protected function buildRolesTree()
    {
        $hierarchy = [];
        $roles = $this->rm->getRoles();
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