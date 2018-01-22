<?php
namespace Ilcfrance\Passportstagiaire\SecurityBundle\Listener;

use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
class LoginSuccess
{

    /**
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * Constructor
     *
     * @param Doctrine $doctrine
     */
    public function __construct(Doctrine $doctrine)
    {
        $this->em = $doctrine->getManager();
    }

    /**
     * Fired on login
     *
     * @param InteractiveLoginEvent $event
     */
    public function onLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        if ($user) {
            $user->setLastLogin(new \DateTime());
            $user->setLogins($user->getLogins() + 1);
            $this->em->persist($user);
            $this->em->flush();

            /*
             * $request = $event->getRequest();
             * $session = $request->getSession();
             * if (null != $user->getPreferedLocale()) {
             * $locale = $user->getPreferedLocale();
             * $session->set('_locale', $locale->getPrefix());
             * }
             */
        }
    }
}