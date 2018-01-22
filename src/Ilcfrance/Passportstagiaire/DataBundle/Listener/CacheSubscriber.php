<?php
namespace Ilcfrance\Passportstagiaire\DataBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\Document;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\Locale;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\Program;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\Role;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\Trainee;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\TraineeHistorical;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\TraineeRecord;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\TraineeRecordDocument;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\User;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\UserPicture;
use Ilcfrance\Passportstagiaire\DataBundle\MongoDocument\Trace;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * CacheSubscriber
 *
 * @author sasedev <sinus@sasedev.net>
 */
class CacheSubscriber implements EventSubscriber
{

    /**
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     *
     * @var DocumentManager
     */
    private $dm;

    /**
     *
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     *
     * @var User
     */
    private $user;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param DocumentManager $dm
     * @param TokenStorage $tokenStorage
     */
    public function __construct(LoggerInterface $logger, DocumentManager $dm, TokenStorage $tokenStorage)
    {
        $this->logger = $logger;
        $this->dm = $dm;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Doctrine\Common\EventSubscriber::getSubscribedEvents()
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::postPersist,
            Events::preUpdate,
            Events::preRemove
        );
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Document) {
            $this->persistDocument($args);
        } elseif ($entity instanceof Locale) {
            $this->persistLocale($args);
        } elseif ($entity instanceof Program) {
            $this->persistProgram($args);
        } elseif ($entity instanceof Role) {
            $this->persistRole($args);
        } elseif ($entity instanceof Trainee) {
            $this->persistTrainee($args);
        } elseif ($entity instanceof TraineeHistorical) {
            $this->persistTraineeHistorical($args);
        } elseif ($entity instanceof TraineeRecord) {
            $this->persistTraineeRecord($args);
        } elseif ($entity instanceof TraineeRecordDocument) {
            $this->persistTraineeRecordDocument($args);
        } elseif ($entity instanceof User) {
            $this->persistUser($args);
        } elseif ($entity instanceof UserPicture) {
            $this->persistUserPicture($args);
        }
    }

    private function persistDocument(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();

        if (null != $cache && $cache->containsEntity(Document::class, $id)) {
            $cache->evictEntity(Document::class, $id);
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_CREATE);

        $trace->setActionEntity(Document::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($entity, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function persistLocale(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();

        if (null != $cache && $cache->containsEntity(Locale::class, $id)) {
            $cache->evictEntity(Locale::class, $id);
        }

        if (null != $cache && $cache->containsCollection(Locale::class, 'users', $id)) {
            $cache->evictCollection(Locale::class, 'users', $id);
        }

        foreach ($entity->getUsers() as $user) {
            if (null != $cache && $cache->containsEntity(User::class, $user->getId())) {
                $cache->evictEntity(User::class, $user->getId());
            }
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_CREATE);

        $trace->setActionEntity(Locale::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($entity, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function persistProgram(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();

        if (null != $cache && $cache->containsEntity(Program::class, $id)) {
            $cache->evictEntity(Program::class, $id);
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_CREATE);

        $trace->setActionEntity(Program::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($entity, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function persistRole(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();

        if (null != $cache && $cache->containsEntity(Role::class, $id)) {
            $cache->evictEntity(Role::class, $id);
        }

        if (null != $cache && $cache->containsCollection(Role::class, 'childs', $id)) {
            $cache->evictCollection(Role::class, 'childs', $id);
        }

        if (null != $cache && $cache->containsCollection(Role::class, 'parents', $id)) {
            $cache->evictCollection(Role::class, 'parents', $id);
        }

        if (null != $cache && $cache->containsCollection(Role::class, 'users', $id)) {
            $cache->evictCollection(Role::class, 'users', $id);
        }

        foreach ($entity->getParents() as $role) {
            if (null != $cache && $cache->containsCollection(Role::class, 'childs', $role->getId())) {
                $cache->evictCollection(Role::class, 'childs', $role->getId());
            }
        }

        foreach ($entity->getChilds() as $role) {
            if (null != $cache && $cache->containsCollection(Role::class, 'parents', $role->getId())) {
                $cache->evictCollection(Role::class, 'parents', $role->getId());
            }
        }

        foreach ($entity->getUsers() as $user) {
            if (null != $cache && $cache->containsCollection(User::class, 'userRoles', $user->getId())) {
                $cache->evictCollection(User::class, 'userRoles', $user->getId());
            }
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_CREATE);

        $trace->setActionEntity(Role::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($entity, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function persistTrainee(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();

        if (null != $cache && $cache->containsEntity(Trainee::class, $id)) {
            $cache->evictEntity(Trainee::class, $id);
        }

        if (null != $cache && $cache->containsCollection(Trainee::class, 'records', $id)) {
            $cache->evictCollection(Trainee::class, 'records', $id);
        }

        if (null != $cache && $cache->containsCollection(Trainee::class, 'historicals', $id)) {
            $cache->evictCollection(Trainee::class, 'historicals', $id);
        }

        foreach ($entity->getRecords() as $record) {
            if (null != $cache && $cache->containsEntity(TraineeRecord::class, $record->getId())) {
                $cache->evictEntity(TraineeRecord::class, $record->getId());
            }
        }

        foreach ($entity->getHistoricals() as $historical) {
            if (null != $cache && $cache->containsEntity(TraineeHistorical::class, $historical->getId())) {
                $cache->evictEntity(TraineeHistorical::class, $historical->getId());
            }
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_CREATE);

        $trace->setActionEntity(Trainee::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($entity, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function persistTraineeHistorical(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();

        if (null != $cache && $cache->containsEntity(TraineeHistorical::class, $id)) {
            $cache->evictEntity(TraineeHistorical::class, $id);
        }

        if (null != $entity->getTrainee()) {
            $trainee = $entity->getTrainee();
            if (null != $cache && $cache->containsCollection(Trainee::class, 'historicals', $trainee->getId())) {
                $cache->evictCollection(Trainee::class, 'historicals', $trainee->getId());
            }
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_CREATE);

        $trace->setActionEntity(TraineeHistorical::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($entity, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function persistTraineeRecord(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();

        if (null != $cache && $cache->containsEntity(TraineeRecord::class, $id)) {
            $cache->evictEntity(TraineeRecord::class, $id);
        }

        if (null != $cache && $cache->containsCollection(TraineeRecord::class, 'docs', $id)) {
            $cache->evictCollection(TraineeRecord::class, 'docs', $id);
        }

        foreach ($entity->getDocs() as $doc) {
            if (null != $cache && $cache->containsEntity(TraineeRecordDocument::class, $doc->getId())) {
                $cache->evictEntity(TraineeRecordDocument::class, $doc->getId());
            }
        }

        if (null != $entity->getTrainee()) {
            $trainee = $entity->getTrainee();
            if (null != $cache && $cache->containsCollection(Trainee::class, 'records', $trainee->getId())) {
                $cache->evictCollection(Trainee::class, 'records', $trainee->getId());
            }
        }

        if (null != $entity->getHistorical()) {
            $historical = $entity->getHistorical();
            if (null != $cache && $cache->containsCollection(TraineeHistorical::class, 'records', $historical->getId())) {
                $cache->evictCollection(TraineeHistorical::class, 'records', $historical->getId());
            }
        }

        if (null != $entity->getTeacher()) {
            $teacher = $entity->getTeacher();
            if (null != $cache && $cache->containsCollection(User::class, 'records', $teacher->getId())) {
                $cache->evictCollection(User::class, 'records', $teacher->getId());
            }
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_CREATE);

        $trace->setActionEntity(TraineeRecord::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($entity, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function persistTraineeRecordDocument(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();

        if (null != $cache && $cache->containsEntity(TraineeRecordDocument::class, $id)) {
            $cache->evictEntity(TraineeRecordDocument::class, $id);
        }

        if (null != $entity->getTraineeRecord()) {
            $traineeRecord = $entity->getTraineeRecord();
            if (null != $cache && $cache->containsCollection(TraineeRecord::class, 'docs', $traineeRecord->getId())) {
                $cache->evictCollection(TraineeRecord::class, 'docs', $traineeRecord->getId());
            }
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_CREATE);

        $trace->setActionEntity(TraineeRecordDocument::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($entity, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function persistUser(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();

        if (null != $cache && $cache->containsEntity(User::class, $id)) {
            $cache->evictEntity(User::class, $id);
        }

        foreach ($entity->getUserRoles() as $role) {
            if (null != $cache && $cache->containsCollection(Role::class, 'users', $role->getId())) {
                $cache->evictCollection(Role::class, 'users', $role->getId());
            }
        }

        if (null != $entity->getLocale()) {
            $locale = $entity->getLocale();
            if (null != $cache && $cache->containsCollection(Locale::class, 'users', $locale->getId())) {
                $cache->evictCollection(Locale::class, 'users', $locale->getId());
            }
        }

        if (null != $entity->getPicture()) {
            $picture = $entity->getPicture();
            if (null != $cache && $cache->containsEntity(UserPicture::class, $picture->getId())) {
                $cache->evictEntity(UserPicture::class, $picture->getId());
            }
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_CREATE);

        $trace->setActionEntity(User::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($entity, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function persistUserPicture(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();

        if (null != $cache && $cache->containsEntity(UserPicture::class, $id)) {
            $cache->evictEntity(UserPicture::class, $id);
        }

        $user = $entity->getId();
        if (null != $cache && $cache->containsEntity(User::class, $user->getId())) {
            $cache->evictEntity(User::class, $user->getId());
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_CREATE);

        $trace->setActionEntity(UserPicture::class);
        $trace->setActionId($id->getId());
        $trace->setMsg(\json_encode($entity, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Document) {
            $this->updateDocument($args);
        } elseif ($entity instanceof Locale) {
            $this->updateLocale($args);
        } elseif ($entity instanceof Program) {
            $this->updateProgram($args);
        } elseif ($entity instanceof Role) {
            $this->updateRole($args);
        } elseif ($entity instanceof Trainee) {
            $this->updateTrainee($args);
        } elseif ($entity instanceof TraineeHistorical) {
            $this->updateTraineeHistorical($args);
        } elseif ($entity instanceof TraineeRecord) {
            $this->updateTraineeRecord($args);
        } elseif ($entity instanceof TraineeRecordDocument) {
            $this->updateTraineeRecordDocument($args);
        } elseif ($entity instanceof User) {
            $this->updateUser($args);
        } elseif ($entity instanceof UserPicture) {
            $this->updateUserPicture($args);
        }
    }

    private function updateDocument(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();

        if (null != $cache && $cache->containsEntity(Document::class, $id)) {
            $cache->evictEntity(Document::class, $id);
        }

        $changes = $args->getEntityChangeSet();
        foreach ($changes as $changename => &$changevalue) {
            if ($changename == 'id') {
                $oldId = $changevalue[0];
                if (null != $cache && $cache->containsEntity(Document::class, $oldId)) {
                    $cache->evictEntity(Document::class, $oldId);
                }
            } else {
                if ($changevalue[0] instanceof \DateTime) {
                    $changevalue[0] = $changevalue[0]->format(\DateTime::ISO8601);
                }
                if ($changevalue[1] instanceof \DateTime) {
                    $changevalue[1] = $changevalue[1]->format(\DateTime::ISO8601);
                }
            }
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_UPDATE);

        $trace->setActionEntity(Document::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($changes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function updateLocale(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();
        $uow = $em->getUnitOfWork();

        if (null != $cache && $cache->containsEntity(Locale::class, $id)) {
            $cache->evictEntity(Locale::class, $id);
        }

        $changes = $args->getEntityChangeSet();
        foreach ($changes as $changename => &$changevalue) {
            if ($changename == 'id') {
                $oldId = $changevalue[0];
                if (null != $cache && $cache->containsEntity(Locale::class, $oldId)) {
                    $cache->evictEntity(Locale::class, $oldId);
                }

                if (null != $cache && $cache->containsCollection(Locale::class, 'users', $oldId)) {
                    $cache->evictCollection(Locale::class, 'users', $oldId);
                }

                foreach ($entity->getUsers() as $user) {
                    if (null != $cache && $cache->containsEntity(User::class, $user->getId())) {
                        $cache->evictEntity(User::class, $user->getId());
                    }
                }
            } else {
                if ($changevalue[0] instanceof \DateTime) {
                    $changevalue[0] = $changevalue[0]->format(\DateTime::ISO8601);
                }
                if ($changevalue[1] instanceof \DateTime) {
                    $changevalue[1] = $changevalue[1]->format(\DateTime::ISO8601);
                }
            }
        }

        foreach ($uow->getScheduledCollectionUpdates() as $cols) {
            foreach ($cols->getInsertDiff() as $col) {
                if ($col instanceof User) {
                    if (null != $cache && $cache->containsEntity(User::class, $col->getId())) {
                        $cache->evictEntity(User::class, $col->getId());
                    }
                } else {
                    $this->logger->emergency('CacheSubscriber updateLocale getInsertDiff: ' . \get_class($col));
                }
            }
            foreach ($cols->getDeleteDiff() as $col) {
                if ($col instanceof User) {
                    if (null != $cache && $cache->containsEntity(User::class, $col->getId())) {
                        $cache->evictEntity(User::class, $col->getId());
                    }
                } else {
                    $this->logger->emergency('CacheSubscriber updateLocale getDeleteDiff: ' . \get_class($col));
                }
            }
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_UPDATE);

        $trace->setActionEntity(Locale::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($changes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function updateProgram(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();

        if (null != $cache && $cache->containsEntity(Program::class, $id)) {
            $cache->evictEntity(Program::class, $id);
        }

        $changes = $args->getEntityChangeSet();
        foreach ($changes as $changename => &$changevalue) {
            if ($changename == 'id') {
                $oldId = $changevalue[0];
                if (null != $cache && $cache->containsEntity(Program::class, $oldId)) {
                    $cache->evictEntity(Program::class, $oldId);
                }
            } else {
                if ($changevalue[0] instanceof \DateTime) {
                    $changevalue[0] = $changevalue[0]->format(\DateTime::ISO8601);
                }
                if ($changevalue[1] instanceof \DateTime) {
                    $changevalue[1] = $changevalue[1]->format(\DateTime::ISO8601);
                }
            }
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_UPDATE);

        $trace->setActionEntity(Program::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($changes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function updateRole(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();
        $uow = $em->getUnitOfWork();

        if (null != $cache && $cache->containsEntity(Role::class, $id)) {
            $cache->evictEntity(Role::class, $id);
        }

        $changes = $args->getEntityChangeSet();
        foreach ($changes as $changename => &$changevalue) {
            if ($changename == 'id') {
                $oldId = $changevalue[0];
                if (null != $cache && $cache->containsEntity(Role::class, $oldId)) {
                    $cache->evictEntity(Role::class, $oldId);
                }

                if (null != $cache && $cache->containsCollection(Role::class, 'childs', $oldId)) {
                    $cache->evictCollection(Role::class, 'childs', $oldId);
                }

                if (null != $cache && $cache->containsCollection(Role::class, 'parents', $oldId)) {
                    $cache->evictCollection(Role::class, 'parents', $oldId);
                }

                if (null != $cache && $cache->containsCollection(Role::class, 'users', $oldId)) {
                    $cache->evictCollection(Role::class, 'users', $oldId);
                }

                foreach ($entity->getParents() as $role) {
                    if (null != $cache && $cache->containsCollection(Role::class, 'childs', $role->getId())) {
                        $cache->evictCollection(Role::class, 'childs', $role->getId());
                    }
                }

                foreach ($entity->getChilds() as $role) {
                    if (null != $cache && $cache->containsCollection(Role::class, 'parents', $role->getId())) {
                        $cache->evictCollection(Role::class, 'parents', $role->getId());
                    }
                }

                foreach ($entity->getUsers() as $user) {
                    if (null != $cache && $cache->containsCollection(User::class, 'userRoles', $user->getId())) {
                        $cache->evictCollection(User::class, 'userRoles', $user->getId());
                    }
                }
            } else {
                if ($changevalue[0] instanceof \DateTime) {
                    $changevalue[0] = $changevalue[0]->format(\DateTime::ISO8601);
                }
                if ($changevalue[1] instanceof \DateTime) {
                    $changevalue[1] = $changevalue[1]->format(\DateTime::ISO8601);
                }
            }
        }

        $hasChangeRoles = false;
        $insertedRoles = array();
        $deletedRoles = array();

        foreach ($uow->getScheduledCollectionUpdates() as $cols) {
            foreach ($cols->getInsertDiff() as $col) {
                if ($col instanceof Role) {
                    $hasChangeRoles = true;

                    $insertedRoles[] = $col->getId();

                    if (null != $cache && $cache->containsCollection(Role::class, 'parents', $oldId)) {
                        $cache->evictCollection(Role::class, 'parents', $col->getId());
                    }

                    foreach ($col->getParents() as $role) {
                        if (null != $cache && $cache->containsCollection(Role::class, 'childs', $role->getId())) {
                            $cache->evictCollection(Role::class, 'childs', $role->getId());
                        }
                    }
                }
            }
            foreach ($cols->getDeleteDiff() as $col) {
                if ($col instanceof Role) {
                    $hasChangeRoles = true;

                    $insertedRoles[] = $col->getId();

                    if (null != $cache && $cache->containsCollection(Role::class, 'parents', $oldId)) {
                        $cache->evictCollection(Role::class, 'parents', $col->getId());
                    }

                    foreach ($col->getParents() as $role) {
                        if (null != $cache && $cache->containsCollection(Role::class, 'childs', $role->getId())) {
                            $cache->evictCollection(Role::class, 'childs', $role->getId());
                        }
                    }
                }
            }
        }

        if ($hasChangeRoles) {
            if (null != $cache && $cache->containsCollection(Role)) {
                $cache->evictCollection(Role::class, 'parents', $id);
            }

            $roles = array();
            foreach ($entity->getParents() as $role) {
                $roles[] = $role->getId();
            }

            $initRoles = \array_diff($roles, $insertedRoles);
            $initRoles = \array_merge($initRoles, $deletedRoles);
            $changes['parents'] = array(
                $initRoles,
                $roles
            );
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_UPDATE);

        $trace->setActionEntity(Role::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($changes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function updateTrainee(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();

        if (null != $cache && $cache->containsEntity(Trainee::class, $id)) {
            $cache->evictEntity(Trainee::class, $id);
        }

        $changes = $args->getEntityChangeSet();
        foreach ($changes as $changename => &$changevalue) {
            if ($changename == 'id') {
                $oldId = $changevalue[0];
                if (null != $cache && $cache->containsEntity(Trainee::class, $oldId)) {
                    $cache->evictEntity(Trainee::class, $oldId);
                }

                if (null != $cache && $cache->containsCollection(Trainee::class, 'records', $oldId)) {
                    $cache->evictCollection(Trainee::class, 'records', $oldId);
                }

                if (null != $cache && $cache->containsCollection(Trainee::class, 'historicals', $id)) {
                    $cache->evictCollection(Trainee::class, 'historicals', $oldId);
                }

                foreach ($entity->getRecords() as $record) {
                    if (null != $cache && $cache->containsEntity(TraineeRecord::class, $record->getId())) {
                        $cache->evictEntity(TraineeRecord::class, $record->getId());
                    }
                }

                foreach ($entity->getHistoricals() as $historical) {
                    if (null != $cache && $cache->containsEntity(TraineeHistorical::class, $historical->getId())) {
                        $cache->evictEntity(TraineeHistorical::class, $historical->getId());
                    }
                }
            } else {
                if ($changevalue[0] instanceof \DateTime) {
                    $changevalue[0] = $changevalue[0]->format(\DateTime::ISO8601);
                }
                if ($changevalue[1] instanceof \DateTime) {
                    $changevalue[1] = $changevalue[1]->format(\DateTime::ISO8601);
                }
            }
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_UPDATE);

        $trace->setActionEntity(Trainee::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($changes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function updateTraineeHistorical(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();
        // $uow = $em->getUnitOfWork();

        if (null != $cache && $cache->containsEntity(TraineeHistorical::class, $id)) {
            $cache->evictEntity(TraineeHistorical::class, $id);
        }

        $changes = $args->getEntityChangeSet();
        foreach ($changes as $changename => &$changevalue) {
            if ($changename == 'id') {
                $oldId = $changevalue[0];
                if (null != $cache && $cache->containsEntity(TraineeHistorical::class, $oldId)) {
                    $cache->evictEntity(TraineeHistorical::class, $oldId);
                }
            } elseif ($changename == 'trainee') {
                if ($changevalue[0] instanceof Trainee) {
                    $trainee = $changevalue[0];
                    if (null != $cache && $cache->containsCollection(Trainee::class, 'historicals', $trainee->getId())) {
                        $cache->evictCollection(Trainee::class, 'historicals', $trainee->getId());
                    }
                }
                if ($changevalue[1] instanceof Trainee) {
                    $trainee = $changevalue[1];
                    if (null != $cache && $cache->containsCollection(Trainee::class, 'historicals', $trainee->getId())) {
                        $cache->evictCollection(Trainee::class, 'historicals', $trainee->getId());
                    }
                }
            } else {
                if ($changevalue[0] instanceof \DateTime) {
                    $changevalue[0] = $changevalue[0]->format(\DateTime::ISO8601);
                }
                if ($changevalue[1] instanceof \DateTime) {
                    $changevalue[1] = $changevalue[1]->format(\DateTime::ISO8601);
                }
            }
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_UPDATE);

        $trace->setActionEntity(TraineeHistorical::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($changes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function updateTraineeRecord(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();
        // $uow = $em->getUnitOfWork();

        $changes = $args->getEntityChangeSet();
        foreach ($changes as $changename => &$changevalue) {
            if ($changename == 'id') {
                $oldId = $changevalue[0];
                if (null != $cache && $cache->containsEntity(TraineeRecord::class, $oldId)) {
                    $cache->evictEntity(TraineeRecord::class, $oldId);
                }
            } elseif ($changename == "trainee") {
                if ($changevalue[0] instanceof Trainee) {
                    $trainee = $changevalue[0];
                    $trainee = $entity->getTrainee();
                    if (null != $cache && $cache->containsCollection(Trainee::class, 'records', $trainee->getId())) {
                        $cache->evictCollection(Trainee::class, 'records', $trainee->getId());
                    }
                }
                if ($changevalue[1] instanceof TraineeHistorical) {
                    $historical = $changevalue[1];
                    $historical = $entity->getHistorical();
                    if (null != $cache && $cache->containsCollection(TraineeHistorical::class, 'records', $historical->getId())) {
                        $cache->evictCollection(TraineeHistorical::class, 'records', $historical->getId());
                    }
                }
            } elseif ($changename == "historical") {
                if ($changevalue[0] instanceof TraineeHistorical) {
                    $historical = $changevalue[0];
                    $historical = $entity->getHistorical();
                    if (null != $cache && $cache->containsCollection(TraineeHistorical::class, 'records', $historical->getId())) {
                        $cache->evictCollection(TraineeHistorical::class, 'records', $historical->getId());
                    }
                }
                if ($changevalue[1] instanceof TraineeHistorical) {
                    $historical = $changevalue[1];
                    $historical = $entity->getHistorical();
                    if (null != $cache && $cache->containsCollection(TraineeHistorical::class, 'records', $historical->getId())) {
                        $cache->evictCollection(TraineeHistorical::class, 'records', $historical->getId());
                    }
                }
            } else {
                if ($changevalue[0] instanceof \DateTime) {
                    $changevalue[0] = $changevalue[0]->format(\DateTime::ISO8601);
                }
                if ($changevalue[1] instanceof \DateTime) {
                    $changevalue[1] = $changevalue[1]->format(\DateTime::ISO8601);
                }
            }
        }

        if (null != $cache && $cache->containsEntity(TraineeRecord::class, $id)) {
            $cache->evictEntity(TraineeRecord::class, $id);
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_UPDATE);

        $trace->setActionEntity(TraineeRecord::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($changes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function updateTraineeRecordDocument(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();
        // $uow = $em->getUnitOfWork();

        if (null != $cache && $cache->containsEntity(TraineeRecordDocument::class, $id)) {
            $cache->evictEntity(TraineeRecordDocument::class, $id);
        }

        $changes = $args->getEntityChangeSet();
        foreach ($changes as $changename => &$changevalue) {
            if ($changename == 'id') {
                $oldId = $changevalue[0];
                if (null != $cache && $cache->containsEntity(TraineeRecordDocument::class, $oldId)) {
                    $cache->evictEntity(TraineeRecordDocument::class, $oldId);
                }
            } elseif ($changename == 'traineeRecord') {
                if ($changevalue[0] instanceof TraineeRecord) {

                    $traineeRecord = $changevalue[0];

                    if (null != $cache && $cache->containsCollection(TraineeRecord::class, 'docs', $traineeRecord->getId())) {
                        $cache->evictCollection(TraineeRecord::class, 'docs', $traineeRecord->getId());
                    }

                    $changevalue[0] = $changevalue[0]->getId();
                }
                if ($changevalue[1] instanceof TraineeRecord) {

                    $traineeRecord = $changevalue[1];

                    if (null != $cache && $cache->containsCollection(TraineeRecord::class, 'docs', $traineeRecord->getId())) {
                        $cache->evictCollection(TraineeRecord::class, 'docs', $traineeRecord->getId());
                    }

                    $changevalue[0] = $changevalue[0]->getId();
                }
            } else {
                if ($changevalue[0] instanceof \DateTime) {
                    $changevalue[0] = $changevalue[0]->format(\DateTime::ISO8601);
                }
                if ($changevalue[1] instanceof \DateTime) {
                    $changevalue[1] = $changevalue[1]->format(\DateTime::ISO8601);
                }
            }
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_UPDATE);

        $trace->setActionEntity(TraineeRecordDocument::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($changes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function updateUser(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();
        $uow = $em->getUnitOfWork();

        if (null != $cache && $cache->containsEntity(User::class, $id)) {
            $cache->evictEntity(User::class, $id);
        }

        $changes = $args->getEntityChangeSet();
        foreach ($changes as $changename => &$changevalue) {
            if ($changename == 'id') {
                $oldId = $changevalue[0];

                if (null != $cache && $cache->containsEntity(User::class, $oldId)) {
                    $cache->evictEntity(User::class, $oldId);
                }

                if (null != $cache && $cache->containsCollection(User::class, 'userRoles', $oldId)) {
                    $cache->evictCollection(User::class, 'userRoles', $oldId);
                }

                if (null != $cache && $cache->containsCollection(User::class, 'records', $oldId)) {
                    $cache->evictCollection(User::class, 'records', $oldId);
                }

                if (null != $cache && $cache->containsEntity(UserPicture::class, $oldId)) {
                    $cache->evictEntity(UserPicture::class, $oldId);
                }
            } elseif ($changename == 'locale') {
                if ($changevalue[0] instanceof Locale) {

                    $locale = $changevalue[0];

                    if (null != $cache && $cache->containsCollection(Locale::class, 'users', $locale->getId())) {
                        $cache->evictCollection(Locale::class, 'users', $locale->getId());
                    }

                    $changevalue[0] = $changevalue[0]->getId();
                }
                if ($changevalue[1] instanceof Locale) {

                    $locale = $changevalue[1];

                    if (null != $cache && $cache->containsCollection(Locale::class, 'users', $locale->getId())) {
                        $cache->evictCollection(Locale::class, 'users', $locale->getId());
                    }

                    $changevalue[1] = $changevalue[1]->getId();
                }
            } else {
                if ($changevalue[0] instanceof \DateTime) {
                    $changevalue[0] = $changevalue[0]->format(\DateTime::ISO8601);
                }
                if ($changevalue[1] instanceof \DateTime) {
                    $changevalue[1] = $changevalue[1]->format(\DateTime::ISO8601);
                }
            }
        }

        $hasChangeRoles = false;
        $insertedRoles = array();
        $deletedRoles = array();

        $hasChangeRecords = false;

        foreach ($uow->getScheduledCollectionUpdates() as $cols) {
            foreach ($cols->getInsertDiff() as $col) {
                if ($col instanceof Role) {
                    $hasChangeRoles = true;

                    $insertedRoles[] = $col->getId();

                    if (null != $cache && $cache->containsCollection(Role::class, 'users', $col->getId())) {
                        $cache->evictCollection(Role::class, 'users', $col->getId());
                    }
                } elseif ($col instanceof TraineeRecord) {
                    $hasChangeRecords = true;
                }
            }
            foreach ($cols->getDeleteDiff() as $col) {
                if ($col instanceof Role) {
                    $hasChangeRoles = true;

                    $deletedRoles[] = $col->getId();

                    if (null != $cache && $cache->containsCollection(Role::class, 'users', $col->getId())) {
                        $cache->evictCollection(Role::class, 'users', $col->getId());
                    }
                } elseif ($col instanceof TraineeRecord) {
                    $hasChangeRecords = true;
                }
            }
        }

        if ($hasChangeRoles) {
            if (null != $cache && $cache->containsCollection(User::class, 'userRoles', $id)) {
                $cache->evictCollection(User::class, 'userRoles', $id);
            }

            $roles = array();
            foreach ($entity->getUserRoles() as $role) {
                $roles[] = $role->getId();
            }

            $initRoles = \array_diff($roles, $insertedRoles);
            $initRoles = \array_merge($initRoles, $deletedRoles);
            $changes['userRoles'] = array(
                $initRoles,
                $roles
            );
        }

        if ($hasChangeRecords) {
            if (null != $cache && $cache->containsCollection(User::class, 'records', $id)) {
                $cache->evictCollection(User::class, 'records', $id);
            }
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_UPDATE);

        $trace->setActionEntity(User::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($changes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function updateUserPicture(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();

        if (null != $cache && $cache->containsEntity(UserPicture::class, $id)) {
            $cache->evictEntity(UserPicture::class, $id);
        }

        $changes = $args->getEntityChangeSet();
        foreach ($changes as $changename => &$changevalue) {
            if ($changename == 'id') {
                $oldId = $changevalue[0];
                if (null != $cache && $cache->containsEntity(UserPicture::class, $oldId)) {
                    $cache->evictEntity(UserPicture::class, $oldId);
                }
                if (null != $cache && $cache->containsEntity(User::class, $oldId)) {
                    $cache->evictEntity(User::class, $oldId);
                }
            } else {
                if ($changevalue[0] instanceof \DateTime) {
                    $changevalue[0] = $changevalue[0]->format(\DateTime::ISO8601);
                }
                if ($changevalue[1] instanceof \DateTime) {
                    $changevalue[1] = $changevalue[1]->format(\DateTime::ISO8601);
                }
            }
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_UPDATE);

        $trace->setActionEntity(UserPicture::class);
        $trace->setActionId($id->getId());
        $trace->setMsg(\json_encode($changes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Document) {
            $this->removeDocument($args);
        } elseif ($entity instanceof Locale) {
            $this->removeLocale($args);
        } elseif ($entity instanceof Program) {
            $this->removeProgram($args);
        } elseif ($entity instanceof Role) {
            $this->removeRole($args);
        } elseif ($entity instanceof Trainee) {
            $this->removeTrainee($args);
        } elseif ($entity instanceof TraineeHistorical) {
            $this->removeTraineeHistorical($args);
        } elseif ($entity instanceof TraineeRecord) {
            $this->removeTraineeRecord($args);
        } elseif ($entity instanceof TraineeRecordDocument) {
            $this->removeTraineeRecordDocument($args);
        } elseif ($entity instanceof User) {
            $this->removeUser($args);
        } elseif ($entity instanceof UserPicture) {
            $this->removeUserPicture($args);
        }
    }

    private function removeDocument(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();
        // $uow = $em->getUnitOfWork();

        if (null != $cache && $cache->containsEntity(Document::class, $id)) {
            $cache->evictEntity(Document::class, $id);
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_DELETE);

        $trace->setActionEntity(Document::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($entity, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function removeLocale(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();
        // $uow = $em->getUnitOfWork();

        if (null != $cache && $cache->containsEntity(Locale::class, $id)) {
            $cache->evictEntity(Locale::class, $id);
        }

        if (null != $cache && $cache->containsCollection(Locale::class, 'users', $id)) {
            $cache->evictCollection(Locale::class, 'users', $id);
        }

        foreach ($entity->getUsers() as $user) {
            if (null != $cache && $cache->containsEntity(User::class, $user->getId())) {
                $cache->evictEntity(User::class, $user->getId());
            }
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_DELETE);

        $trace->setActionEntity(Locale::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($entity, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function removeProgram(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();
        // $uow = $em->getUnitOfWork();

        if (null != $cache && $cache->containsEntity(Program::class, $id)) {
            $cache->evictEntity(Program::class, $id);
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_DELETE);

        $trace->setActionEntity(Program::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($entity, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function removeRole(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();
        // $uow = $em->getUnitOfWork();

        if (null != $cache && $cache->containsEntity(Role::class, $id)) {
            $cache->evictEntity(Role::class, $id);
        }

        if (null != $cache && $cache->containsCollection(Role::class, 'childs', $id)) {
            $cache->evictCollection(Role::class, 'childs', $id);
        }

        if (null != $cache && $cache->containsCollection(Role::class, 'parents', $id)) {
            $cache->evictCollection(Role::class, 'parents', $id);
        }

        if (null != $cache && $cache->containsCollection(Role::class, 'users', $id)) {
            $cache->evictCollection(Role::class, 'users', $id);
        }

        foreach ($entity->getParents() as $role) {
            if (null != $cache && $cache->containsCollection(Role::class, 'childs', $role->getId())) {
                $cache->evictCollection(Role::class, 'childs', $role->getId());
            }
        }

        foreach ($entity->getChilds() as $role) {
            if (null != $cache && $cache->containsCollection(Role::class, 'parents', $role->getId())) {
                $cache->evictCollection(Role::class, 'parents', $role->getId());
            }
        }

        foreach ($entity->getUsers() as $user) {
            if (null != $cache && $cache->containsCollection(User::class, 'userRoles', $user->getId())) {
                $cache->evictCollection(User::class, 'userRoles', $user->getId());
            }
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_DELETE);

        $trace->setActionEntity(Role::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($entity, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function removeTrainee(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();
        // $uow = $em->getUnitOfWork();

        if (null != $cache && $cache->containsEntity(Trainee::class, $id)) {
            $cache->evictEntity(Trainee::class, $id);
        }

        if (null != $cache && $cache->containsCollection(Trainee::class, 'records', $id)) {
            $cache->evictCollection(Trainee::class, 'records', $id);
        }

        if (null != $cache && $cache->containsCollection(Trainee::class, 'historicals', $id)) {
            $cache->evictCollection(Trainee::class, 'historicals', $id);
        }

        foreach ($entity->getRecords() as $record) {
            if (null != $cache && $cache->containsEntity(TraineeRecord::class, $record->getId())) {
                $cache->evictEntity(TraineeRecord::class, $record->getId());
            }
        }

        foreach ($entity->getHistoricals() as $historical) {
            if (null != $cache && $cache->containsEntity(TraineeHistorical::class, $historical->getId())) {
                $cache->evictEntity(TraineeHistorical::class, $historical->getId());
            }
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_DELETE);

        $trace->setActionEntity(Trainee::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($entity, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function removeTraineeHistorical(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();
        // $uow = $em->getUnitOfWork();

        if (null != $cache && $cache->containsEntity(TraineeHistorical::class, $id)) {
            $cache->evictEntity(TraineeHistorical::class, $id);
        }

        if (null != $entity->getTrainee()) {
            $trainee = $entity->getTrainee();
            if (null != $cache && $cache->containsCollection(Trainee::class, 'historicals', $trainee->getId())) {
                $cache->evictCollection(Trainee::class, 'historicals', $trainee->getId());
            }
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_DELETE);

        $trace->setActionEntity(TraineeHistorical::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($entity, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function removeTraineeRecord(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();
        // $uow = $em->getUnitOfWork();

        if (null != $cache && $cache->containsEntity(TraineeRecord::class, $id)) {
            $cache->evictEntity(TraineeRecord::class, $id);
        }

        if (null != $entity->getHistorical()) {
            $historical = $entity->getHistorical();
            if (null != $cache && $cache->containsCollection(TraineeHistorical::class, 'records', $historical->getId())) {
                $cache->evictCollection(TraineeHistorical::class, 'records', $historical->getId());
            }
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_DELETE);

        $trace->setActionEntity(TraineeRecord::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($entity, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function removeTraineeRecordDocument(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();
        // $uow = $em->getUnitOfWork();

        if (null != $cache && $cache->containsEntity(TraineeRecordDocument::class, $id)) {
            $cache->evictEntity(TraineeRecordDocument::class, $id);
        }

        if (null != $entity->getTraineeRecord()) {
            $traineeRecord = $entity->getTraineeRecord();
            if (null != $cache && $cache->containsCollection(TraineeRecord::class, 'docs', $traineeRecord->getId())) {
                $cache->evictCollection(TraineeRecord::class, 'docs', $traineeRecord->getId());
            }
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_DELETE);

        $trace->setActionEntity(TraineeRecordDocument::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($entity, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function removeUser(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();
        // $uow = $em->getUnitOfWork();

        if (null != $cache && $cache->containsEntity(User::class, $id)) {
            $cache->evictEntity(User::class, $id);
        }

        if (null != $cache && $cache->containsCollection(User::class, 'userRoles', $id)) {
            $cache->evictCollection(User::class, 'userRoles', $id);
        }

        if (null != $cache && $cache->containsCollection(User::class, 'records', $id)) {
            $cache->evictCollection(User::class, 'records', $id);
        }

        foreach ($entity->getUserRoles() as $role) {
            if (null != $cache && $cache->containsCollection(Role::class, 'users', $role->getId())) {
                $cache->evictCollection(Role::class, 'users', $role->getId());
            }
        }

        if (null != $entity->getLocale()) {
            $locale = $entity->getLocale();
            if (null != $cache && $cache->containsCollection(Locale::class, 'users', $locale->getId())) {
                $cache->evictCollection(Locale::class, 'users', $locale->getId());
            }
        }

        if (null != $entity->getPicture()) {
            $picture = $entity->getPicture();
            if (null != $cache && $cache->containsEntity(UserPicture::class, $picture->getId())) {
                $cache->evictEntity(UserPicture::class, $picture->getId());
            }
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_DELETE);

        $trace->setActionEntity(User::class);
        $trace->setActionId($id);
        $trace->setMsg(\json_encode($entity, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    private function removeUserPicture(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $cache = $em->getCache();
        $id = $entity->getId();

        if (null != $cache && $cache->containsEntity(UserPicture::class, $id)) {
            $cache->evictEntity(UserPicture::class, $id);
        }

        $user = $entity->getId();
        if (null != $cache && $cache->containsEntity(User::class, $user->getId())) {
            $cache->evictEntity(User::class, $user->getId());
        }

        $trace = $this->initTrace();
        $trace->setActionType(Trace::AT_DELETE);

        $trace->setActionEntity(UserPicture::class);
        $trace->setActionId($id->getId());
        $trace->setMsg(\json_encode($entity, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->flushTrace($trace);
    }

    /**
     *
     * @return Trace
     */
    private function initTrace()
    {
        $trace = new Trace();

        if (null != $this->tokenStorage && null != $this->tokenStorage->getToken()) {
            $this->user = $this->tokenStorage->getToken()->getUser();
        }
        if ($this->user != null && $this->user instanceof User) {
            $trace->setUserId($this->user->getId());
            $trace->setUserFullname($this->user->getFullName());
        } else {
            $trace->setUserFullname("--<=SYSTEM=>--");
        }
        return $trace;
    }

    /**
     *
     * @param Trace $trace
     */
    private function flushTrace(Trace $trace)
    {
        $this->dm->persist($trace);
        $this->dm->flush();
    }
}

