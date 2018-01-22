<?php
namespace Ilcfrance\Passportstagiaire\DataBundle\MongoRepository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\User;

/**
 *
 * @author sasedev
 */
class TraceRepository extends DocumentRepository
{

    /**
     *
     * @param \DateTime $minDtCrea
     * @param number $maxResult
     *
     * @return \Doctrine\ODM\MongoDB\Query\Query
     */
    public function getAllQuery(\DateTime $minDtCrea = null, $maxResult = 0)
    {
        $qb = $this->createQueryBuilder('t');

        if (null == $minDtCrea) {
            $qb->sort('dtCrea', 'DESC')
                ->sort('actionEntity', 'DESC')
                ->sort('actionType', 'DESC');
        } else {
            $qb->field('dtCrea')
                ->gte($minDtCrea)
                ->sort('dtCrea', 'DESC')
                ->sort('actionEntity', 'DESC')
                ->sort('actionType', 'DESC');
        }

        if ($maxResult > 0) {
            $qb->limit($maxResult);
        }

        return $qb->getQuery();
    }

    /**
     *
     * @param \DateTime $minDtCrea
     * @param number $maxResult
     *
     * @return mixed|\Doctrine\MongoDB\CursorInterface|\Doctrine\MongoDB\Cursor|array|boolean|object
     */
    public function getAll(\DateTime $minDtCrea = null, $maxResult = 0)
    {
        return $this->getAllQuery($minDtCrea, $maxResult)->execute();
    }

    /**
     *
     * @param User $user
     * @param \DateTime $minDtCrea
     *
     * @return \Doctrine\ODM\MongoDB\Query\Query
     */
    public function getAllRelatedToUserQuery(User $user, \DateTime $minDtCrea = null)
    {
        if (null == $minDtCrea) {
            $qb = $this->createQueryBuilder('t');
            $qb->addAnd($qb->expr()
                ->field('userId')
                ->equals($user->getId()));
            $qb->sort('dtCrea', 'ASC');
            $qb->sort('actionEntity', 'ASC');
            $qb->sort('actionType', 'ASC');

            return $qb->getQuery();
        } else {
            $qb = $this->createQueryBuilder('t');
            $qb->addAnd($qb->expr()
                ->field('dtCrea')
                ->gte($minDtCrea));
            $qb->addAnd($qb->expr()
                ->field('userId')
                ->equals((string) $user->getId()));
            $qb->sort('dtCrea', 'ASC');
            $qb->sort('actionEntity', 'ASC');
            $qb->sort('actionType', 'ASC');

            return $qb->getQuery();
        }
    }

    /**
     *
     * @param User $user
     * @param \DateTime $minDtCrea
     *
     * @return mixed|\Doctrine\MongoDB\CursorInterface|\Doctrine\MongoDB\Cursor|array|boolean|object
     */
    public function getAllRelatedToUser(User $user, \DateTime $minDtCrea = null)
    {
        return $this->getAllRelatedToUserQuery($user, $minDtCrea)->execute();
    }

    /**
     *
     * @param mixed $entity_id
     * @param string $entity_type
     *
     * @return \Doctrine\ODM\MongoDB\Query\Query
     */
    public function getAllByEntityQuery($entity_id, $entity_type)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->addAnd($qb->expr()
            ->field('actionEntity')
            ->equals((string) $entity_type));
        $qb->addAnd($qb->expr()
            ->field('actionId')
            ->equals((string) $entity_id));
        $qb->sort('dtCrea', 'ASC');

        return $qb->getQuery();
    }

    /**
     *
     * @param mixed $entity_id
     * @param string $entity_type
     *
     * @return mixed|\Doctrine\MongoDB\CursorInterface|\Doctrine\MongoDB\Cursor|array|boolean|object
     */
    public function getAllByEntity($entity_id, $entity_type)
    {
        return $this->getAllByEntityQuery($entity_id, $entity_type)->execute();
    }
}

