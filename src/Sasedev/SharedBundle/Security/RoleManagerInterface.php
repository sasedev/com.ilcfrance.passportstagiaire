<?php
namespace Sasedev\SharedBundle\Security;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\Role\Role;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
/**
 * RoleManagerInterface
 *
 * @author sasedev <sinus@saseprod.net>
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
     * @return Role
     */
    public function createRole();

    /**
     *
     * @param Role $role
     *
     * @return self
     */
    public function saveRole(Role $role);
}
