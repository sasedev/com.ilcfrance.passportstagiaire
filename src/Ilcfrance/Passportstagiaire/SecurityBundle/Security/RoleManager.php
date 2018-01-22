<?php
namespace Ilcfrance\Passportstagiaire\SecurityBundle\Security;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Sasedev\SharedBundle\Security\RoleManagerInterface;
use Symfony\Component\Security\Core\Role\Role;

/**
 *
 * @author sasedev <sinus@saseprod.net>
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
    protected $entityManager;

    /**
     *
     * @var ObjectRepository
     */
    protected $entityRepository;

    /**
     *
     * @param ManagerRegistry $managerRegistry
     * @param string $class
     */
    public function __construct(ManagerRegistry $managerRegistry, $class)
    {
        $this->class = $class;
        $this->entityManager = $managerRegistry->getManagerForClass($class);
        $this->entityRepository = $this->entityManager->getRepository($class);
    }

    /**
     *
     * {@inheritdoc} @see RoleManagerInterface::getEntityManager()
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     *
     * {@inheritdoc} @see RoleManagerInterface::getEntityRepository()
     */
    public function getEntityRepository()
    {
        return $this->entityRepository;
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
    public function saveRole(Role $role)
    {
        $this->getEntityManager()->persist($role);
        $this->getEntityManager()->flush();

        return $this;
    }
}