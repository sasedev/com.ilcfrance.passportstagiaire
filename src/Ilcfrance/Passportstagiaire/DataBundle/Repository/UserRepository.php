<?php
namespace Ilcfrance\Passportstagiaire\DataBundle\Repository;

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
 * @author sasedev <seif.salah@gmail.com>
 */
class UserRepository extends EntityRepository implements UserProviderInterface, UserLoaderInterface
{

	/**
	 * Get Query for All Entities
	 *
	 * @return \Doctrine\ORM\Query
	 */
	public function getOneByIdQuery($id, $cache = true)
	{
		$qb = $this->createQueryBuilder('u')->where('u.id = :id')->setParameter('id', $id);
		$query = $qb->getQuery();

		if ($cache) {
			$cacheId = 'User_getOneByIdQuery' . $id;
			$query->setCacheable('true')->useQueryCache(true)->setLifetime(200)->useResultCache(true, 200, $cacheId);
		}

		return $query;
	}

	/**
	 * Get All Entities
	 *
	 * @param boolean $cache
	 *
	 * @return Ambigous <\Doctrine\ORM\mixed,
	 *         \Doctrine\ORM\Internal\Hydration\mixed,
	 *         \Doctrine\DBAL\Driver\Statement,
	 *         \Doctrine\Common\Cache\mixed>
	 */
	public function getOneById($id, $cache = true)
	{
		$query = $this->getOneByIdQuery($id, $cache);

		return $query->getOneOrNullResult();
	}

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
		$qb = $this->createQueryBuilder('u')->where('u.id = :id')->andWhere('u.lockout = :lockout')->setParameter('id', $id)->setParameter('lockout', User::LOCKOUT_UNLOCKED);
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

	/**
	 * Count All
	 *
	 * @param boolean $cache
	 *
	 * @return mixed
	 */
	public function count()
	{
		$qb = $this->createQueryBuilder('u')->select('count(u)');
		$query = $qb->getQuery();

		return $query->getSingleScalarResult();
	}

	/**
	 * Get Query for All Entities
	 *
	 * @return \Doctrine\ORM\Query
	 */
	public function getAllQuery($cache = true)
	{
		$qb = $this->createQueryBuilder('u')->orderBy('u.id', 'ASC');
		$query = $qb->getQuery();

		if ($cache) {
			$query->setCacheable('true')->useQueryCache(true)->setLifetime(200)->useResultCache(true, 200, 'User_getAllQuery');
		}

		return $query;
	}

	/**
	 * Get All Entities
	 *
	 * @param boolean $cache
	 *
	 * @return Ambigous <\Doctrine\ORM\mixed, \Doctrine\ORM\Internal\Hydration\mixed, \Doctrine\DBAL\Driver\Statement,
	 *         \Doctrine\Common\Cache\mixed>
	 */
	public function getAll($cache = true)
	{
		$query = $this->getAllQuery($cache);

		return $query->execute();
	}

	/**
	 * Count for a Search
	 *
	 * @param string $q
	 * @param boolean $cache
	 *
	 * @return Ambigous <\Doctrine\ORM\mixed, mixed, multitype:, \Doctrine\DBAL\Driver\Statement,
	 *         \Doctrine\Common\Cache\mixed>
	 */
	public function countSearch($q)
	{
		$qb = $this->createQueryBuilder('u')->select('count(u)')->distinct()->where('LOWER(u.id) LIKE :key')->orWhere('LOWER(u.email) LIKE :key')->orWhere('LOWER(u.firstName) LIKE :key')->orWhere('LOWER(u.lastName) LIKE :key')->setParameter('key', '%' . strtolower($q) . '%');
		$query = $qb->getQuery();

		return $query->getSingleScalarResult();
	}

	/**
	 * Get Query for a Search
	 *
	 * @param string $q
	 *
	 * @return \Doctrine\ORM\Query
	 */
	public function getAllSearchQuery($q, $cache = true)
	{
		$qb = $this->createQueryBuilder('u')->distinct()->where('LOWER(u.id) LIKE :key')->orWhere('LOWER(u.email) LIKE :key')->orWhere('LOWER(u.firstName) LIKE :key')->orWhere('LOWER(u.lastName) LIKE :key')->orderBy('u.id', 'ASC')->setParameter('key', '%' . strtolower($q) . '%');
		$query = $qb->getQuery();

		$cacheId = 'User_getAllSearchQuery_' . \serialize($q);

		if ($cache) {
			$query->setCacheable('true')->useQueryCache(true)->setLifetime(200)->useResultCache(true, 200, $cacheId);
		}

		return $query;
	}

	/**
	 * Get All for a Search
	 *
	 * @param string $q
	 * @param boolean $cache
	 *
	 * @return Ambigous <\Doctrine\ORM\mixed, \Doctrine\ORM\Internal\Hydration\mixed, \Doctrine\DBAL\Driver\Statement,
	 *         \Doctrine\Common\Cache\mixed>
	 */
	public function getAllSearch($q, $cache = true)
	{
		$query = $this->getAllSearchQuery($q, $cache);

		return $query->execute();
	}

	/**
	 * Count All that are Active $strtotime ago
	 *
	 * @param string $strtotime
	 * @param boolean $cache
	 *
	 * @return Ambigous <\Doctrine\ORM\mixed, mixed, multitype:, \Doctrine\DBAL\Driver\Statement,
	 *         \Doctrine\Common\Cache\mixed>
	 */
	public function countAllActiveNow($strtotime = null)
	{
		if (null == $strtotime || trim($strtotime) == '') {
			$strtotime = '1 minutes ago';
		}

		$delay = new \DateTime();
		$delay->setTimestamp(strtotime($strtotime));

		$qb = $this->createQueryBuilder('u')->select('count(u)')->where('u.lastActivity > :delay')->orderBy('u.lastActivity', 'ASC')->setParameter('delay', $delay);
		$query = $qb->getQuery();

		return $query->getSingleScalarResult();
	}

	/**
	 * Get Query for All Entities that are Active $strtotime ago
	 *
	 * @param string $strtotime
	 *
	 * @return \Doctrine\ORM\Query
	 */
	public function getAllActiveNowQuery($strtotime = null, $cache = true)
	{
		if (null == $strtotime || trim($strtotime) == '') {
			$strtotime = '1 minutes ago';
		}

		$delay = new \DateTime();
		$delay->setTimestamp(strtotime($strtotime));

		$qb = $this->createQueryBuilder('u')->where('u.lastActivity > :delay')->setParameter('delay', $delay)->orderBy('u.lastActivity', 'DESC');
		$query = $qb->getQuery();

		if ($cache) {
			$query->setCacheable('true')->useQueryCache(true)->setLifetime(200)->useResultCache(true, 200, 'User_getAllActiveNowQuery');
		}

		return $query;
	}

	/**
	 * Get All Entities that are Active $strtotime ago
	 *
	 * @param string $strtotime
	 * @param boolean $cache
	 *
	 * @return Ambigous <\Doctrine\ORM\mixed, \Doctrine\ORM\Internal\Hydration\mixed, \Doctrine\DBAL\Driver\Statement,
	 *         \Doctrine\Common\Cache\mixed>
	 */
	public function getAllActiveNow($strtotime = null, $cache = true)
	{
		$query = $this->getAllActiveNowQuery($strtotime, $cache);

		return $query->execute();
	}

	/**
	 * Count All Unlocked
	 *
	 * @param boolean $cache
	 *
	 * @return Ambigous <\Doctrine\ORM\mixed, mixed, multitype:, \Doctrine\DBAL\Driver\Statement,
	 *         \Doctrine\Common\Cache\mixed>
	 */
	public function countAllUnlocked()
	{
		$qb = $this->createQueryBuilder('u')->select('count(u)')->where('u.lockout = :lockout')->setParameter('lockout', User::LOCKOUT_UNLOCKED);
		$query = $qb->getQuery();

		return $query->getSingleScalarResult();
	}

	/**
	 * Get Query for All Entities where lockout is unlocked
	 *
	 * @return \Doctrine\ORM\Query
	 */
	public function getAllUnlockedQuery($cache = true)
	{
		$qb = $this->createQueryBuilder('u')->where('u.lockout = :lockout')->orderBy('u.username', 'ASC')->setParameter('lockout', User::LOCKOUT_UNLOCKED);
		$query = $qb->getQuery();

		if ($cache) {
			$query->setCacheable('true')->useQueryCache(true)->setLifetime(200)->useResultCache(true, 200, 'User_getAllUnlockedQuery');
		}

		return $query;
	}

	/**
	 * Get All Entities where lockout is unlocked
	 *
	 * @param boolean $cache
	 *
	 * @return Ambigous <\Doctrine\ORM\mixed, \Doctrine\ORM\Internal\Hydration\mixed, \Doctrine\DBAL\Driver\Statement,
	 *         \Doctrine\Common\Cache\mixed>
	 */
	public function getAllUnlocked($cache = true)
	{
		$query = $this->getAllUnlockedQuery($cache);

		return $query->execute();
	}
}