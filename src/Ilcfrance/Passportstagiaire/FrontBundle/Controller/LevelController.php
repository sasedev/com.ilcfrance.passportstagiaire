<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Controller;

use Ilcfrance\Passportstagiaire\DataBundle\Entity\Level;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Level\AddTForm as LevelAddTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Level\UpdateNameTForm as LevelUpdateNameTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Level\UpdateDescriptionTForm as LevelUpdateDescriptionTForm;
use Ilcfrance\Passportstagiaire\ResBundle\Controller\IlcfranceController;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
class LevelController extends IlcfranceController
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->addTwigVar('menu_active', 'admin');
        $this->addTwigVar('admmenu_active', 'levels');
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $em = $this->getEntityManager();
        $levels = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Level')->findAll();
        $this->addTwigVar('levels', $levels);

        $this->addTwigVar('admmenu_active', 'levels_list');
        $this->addTwigVar('pageTitle', $this->translate('Level.pageTitle.admin.list'));
        $this->setHtmlHeadPageTitle($this->translate('Level.htmlHeadPageTitle.admin.list') . ' - ' . $this->getParameter('sitename'));
        return $this->render('IlcfrancePassportstagiaireFrontBundle:Level:list.html.twig', $this->getTwigVars());
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addGetAction(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_level_list'));
        }
        $level = new Level();
        $levelAddForm = $this->createForm(LevelAddTForm::class, $level);
        $this->addTwigVar('level', $level);
        $this->addTwigVar('LevelAddForm', $levelAddForm->createView());

        $this->addTwigVar('admmenu_active', 'levels_add');
        $this->addTwigVar('pageTitle', $this->translate('Level.pageTitle.admin.add'));
        $this->setHtmlHeadPageTitle($this->translate('Level.htmlHeadPageTitle.admin.add') . ' - ' . $this->getParameter('sitename'));
        return $this->render('IlcfrancePassportstagiaireFrontBundle:Level:add.html.twig', $this->getTwigVars());
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addPostAction(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_level_list'));
        }
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_level_addGet'));
        }
        $level = new Level();
        $levelAddForm = $this->createForm(LevelAddTForm::class, $level);

        $reqData = $request->request->all();

        if (isset($reqData['LevelAddForm'])) {
            $levelAddForm->handleRequest($request);
            if ($levelAddForm->isValid()) {

                $em = $this->getEntityManager();

                $em->persist($level);
                $em->flush();

                $this->addFlash('success', $this->translate('Level.add.success', array(
                    '%level%' => $level->getName()
                )));

                return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_level_editGet', array(
                    'id' => $level->getId()
                )));
            } else {
                $this->addFlash('error', $this->translate('Level.add.failure'));
            }
        }
        $this->addTwigVar('level', $level);
        $this->addTwigVar('LevelAddForm', $levelAddForm->createView());

        $this->addTwigVar('admmenu_active', 'levels_add');
        $this->addTwigVar('pageTitle', $this->translate('Level.pageTitle.admin.add'));
        $this->setHtmlHeadPageTitle($this->translate('Level.htmlHeadPageTitle.admin.add') . ' - ' . $this->getParameter('sitename'));
        return $this->render('IlcfrancePassportstagiaireFrontBundle:Level:add.html.twig', $this->getTwigVars());
    }

    /**
     *
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id, Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_level_list'));
        }
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_level_list');
        }
        $em = $this->getEntityManager();
        try {
            $level = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Level')->find($id);

            if (null == $level) {
                $this->addFlash('warning', $this->translate('Level.notfound'));
            } else {
                $em->remove($level);
                $em->flush();

                $this->addFlash('success', $this->translate('Level.delete.success', array(
                    '%level%' => $level->getName()
                )));
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());

            $this->addFlash('error', $this->translate('Level.delete.failure'));
        }

        return $this->redirect($urlFrom);
    }

    /**
     *
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editGetAction($id, Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_level_list'));
        }
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_level_list');
        }

        $em = $this->getEntityManager();
        try {
            $level = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Level')->find($id);

            if (null == $level) {
                $this->addFlash('warning', $this->translate('Level.notfound'));
                return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_level_list'));
            } else {
                $levelUpdateDescriptionForm = $this->createForm(LevelUpdateDescriptionTForm::class, $level);
                $levelUpdateNameForm = $this->createForm(LevelUpdateNameTForm::class, $level);

                $this->addTwigVar('tabActive', $this->getSession()
                    ->get('tabActive', 1));
                $this->getSession()->remove('tabActive');

                $this->addTwigVar('level', $level);
                $this->addTwigVar('LevelUpdateDescriptionForm', $levelUpdateDescriptionForm->createView());
                $this->addTwigVar('LevelUpdateNameForm', $levelUpdateNameForm->createView());

                $this->addTwigVar('admmenu_active', 'levels_edit');
                $this->addTwigVar('pageTitle', $this->translate('Level.pageTitle.admin.edit', array(
                    '%level%' => $level->getName()
                )));
                $this->setHtmlHeadPageTitle($this->translate('Level.htmlHeadPageTitle.admin.edit', array(
                    '%level%' => $level->getName()
                )) . ' - ' . $this->getParameter('sitename'));

                return $this->render('IlcfrancePassportstagiaireFrontBundle:Level:edit.html.twig', $this->getTwigVars());
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
        }

        return $this->redirect($urlFrom);
    }

    /**
     *
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editPostAction($id, Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_level_list'));
        }
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_level_list');
        }

        $em = $this->getEntityManager();
        try {
            $level = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Level')->find($id);

            if (null == $level) {
                $this->addFlash('warning', $this->translate('Level.notfound'));
                return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_level_list'));
            } else {
                $levelUpdateDescriptionForm = $this->createForm(LevelUpdateDescriptionTForm::class, $level);
                $levelUpdateNameForm = $this->createForm(LevelUpdateNameTForm::class, $level);

                $this->addTwigVar('tabActive', $this->getSession()
                    ->get('tabActive', 1));
                $this->getSession()->remove('tabActive');
                $reqData = $request->request->all();

                if (isset($reqData['LevelUpdateDescriptionForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $levelUpdateDescriptionForm->handleRequest($request);
                    if ($levelUpdateDescriptionForm->isValid()) {
                        $em->persist($level);
                        $em->flush();

                        $this->addFlash('success', $this->translate('Level.edit.success', array(
                            '%level%' => $level->getName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($level);

                        $this->addFlash('error', $this->translate('Level.edit.failure', array(
                            '%level%' => $level->getName()
                        )));
                    }
                } elseif (isset($reqData['LevelUpdateNameForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $levelUpdateNameForm->handleRequest($request);
                    if ($levelUpdateNameForm->isValid()) {

                        $em->persist($level);
                        $em->flush();

                        $this->addFlash('success', $this->translate('Level.edit.success', array(
                            '%level%' => $level->getName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {

                        $em->refresh($level);

                        $this->addFlash('error', $this->translate('Level.edit.failure', array(
                            '%level%' => $level->getName()
                        )));
                    }
                }

                $this->addTwigVar('level', $level);
                $this->addTwigVar('LevelUpdateDescriptionForm', $levelUpdateDescriptionForm->createView());
                $this->addTwigVar('LevelUpdateNameForm', $levelUpdateNameForm->createView());

                $this->addTwigVar('admmenu_active', 'levels_edit');
                $this->addTwigVar('pageTitle', $this->translate('Level.pageTitle.admin.edit', array(
                    '%level%' => $level->getName()
                )));
                $this->setHtmlHeadPageTitle($this->translate('Level.htmlHeadPageTitle.admin.edit', array(
                    '%level%' => $level->getName()
                )) . ' - ' . $this->getParameter('sitename'));

                return $this->render('IlcfrancePassportstagiaireFrontBundle:Level:edit.html.twig', $this->getTwigVars());
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
        }

        return $this->redirect($urlFrom);
    }
}