<?php
namespace Ilcfrance\Passportstagiaire\DataBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * TraineeRecordRepository
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class TraineeRecordRepository extends EntityRepository
{

	/**
	 * Get Query for All Entities
	 *
	 * @return \Doctrine\ORM\Query
	 */
	public function getOneByIdQuery($id, $cache = true)
	{
		$qb = $this->createQueryBuilder('tr')->where('tr.id = :id')->setParameter('id', $id);
		$query = $qb->getQuery();

		if ($cache) {
			$cacheId = 'TraineeRecord_getOneByIdQuery' . $id;
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
}