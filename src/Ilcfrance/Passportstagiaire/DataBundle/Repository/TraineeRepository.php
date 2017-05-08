<?php
namespace Ilcfrance\Passportstagiaire\DataBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * TraineeRepository
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class TraineeRepository extends EntityRepository
{

	/**
	 * Get Query for All Entities
	 *
	 * @return \Doctrine\ORM\Query
	 */
	public function getOneByIdQuery($id, $cache = true)
	{
		$qb = $this->createQueryBuilder('t')->where('t.id = :id')->setParameter('id', $id);
		$query = $qb->getQuery();

		if ($cache) {
			$cacheId = 'Trainee_getOneByIdQuery' . $id;
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
	 * Count All
	 *
	 * @param boolean $cache
	 *
	 * @return Ambigous <\Doctrine\ORM\mixed, mixed, multitype:, \Doctrine\DBAL\Driver\Statement,
	 *         \Doctrine\Common\Cache\mixed>
	 */
	public function count()
	{
		$qb = $this->createQueryBuilder('t')->select('count(t)');
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
		$qb = $this->createQueryBuilder('t')->orderBy('t.lastName', 'ASC')->addOrderBy('t.firstName', 'ASC');
		$query = $qb->getQuery();

		if ($cache) {
			$query->setCacheable('true')->useQueryCache(true)->setLifetime(200)->useResultCache(true, 200, 'Trainee_getAllQuery');
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
	public function getAll($cache = true)
	{
		$query = $this->getAllQuery($cache);

		return $query->execute();
	}
}