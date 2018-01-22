<?php
namespace Ilcfrance\Passportstagiaire\DataBundle\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * UserRepository
 *
 * @author sasedev <sinus@saseprod.net>
 */
class UserRepository extends EntityRepository implements UserProviderInterface, UserLoaderInterface
{

    /**
     * Used for Authentification Security
     *
     * {@inheritdoc} @see UserProviderInterface::loadUserByUsername()
     * @param string $username
     *
     * @throws UsernameNotFoundException
     *
     * @return User
     */
    public function loadUserByUsername($id)
    {
        $id = \strtolower($id);
        $qb = $this->createQueryBuilder('u')
            ->where('u.id = :id')
            ->andWhere('u.lockout = :lockout')
            ->setParameter('id', $id)
            ->setParameter('lockout', User::LOCKOUT_UNLOCKED);
        $query = $qb->getQuery();

        try {
            $user = $query->getSingleResult();
        } catch (NoResultException $e) {
            $exp = new UsernameNotFoundException(sprintf('Unable to find an active User identified by "%s".', $id), 0, $e);
            $exp->setUsername($id);
            throw $exp;
        }

        return $user;
    }

    /**
     * Used for Authentification Security
     *
     * {@inheritdoc} @see UserProviderInterface::refreshUser()
     * @param UserInterface $user
     *
     * @return User
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }

        return $this->loadUserByUsername($user->getId());
    }

    /**
     * Check if is a sublass of the Entity
     *
     * {@inheritdoc} @see UserProviderInterface::supportsClass()
     * @param string $class
     *
     * @return boolean
     */
    public function supportsClass($class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }
}