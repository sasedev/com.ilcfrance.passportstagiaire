<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Controller;

use Ilcfrance\Passportstagiaire\FrontBundle\Form\Homework\AddTForm as HomeworkAddTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Homework\UpdateContentTForm as HomeworkUpdateContentTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Homework\UpdateDescriptionTForm as HomeworkUpdateDescriptionTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Homework\UpdateOriginalNameTForm as HomeworkUpdateOriginalNameTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Homework\UpdateLevelTForm as HomeworkUpdateLevelTForm;
use Ilcfrance\Passportstagiaire\ResBundle\Controller\IlcfranceController;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\Homework;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
class HomeworkController extends IlcfranceController
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->addTwigVar('menu_active', 'admin');
        $this->addTwigVar('admmenu_active', 'homeworks');
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $em = $this->getEntityManager();
        $homeworks = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Homework')->findAll();
        $this->addTwigVar('homeworks', $homeworks);

        $this->addTwigVar('admmenu_active', 'homeworks_list');
        $this->addTwigVar('pageTitle', $this->translate('Homework.pageTitle.admin.list'));
        $this->setHtmlHeadPageTitle($this->translate('Homework.htmlHeadPageTitle.admin.list') . ' - ' . $this->getParameter('sitename'));
        return $this->render('IlcfrancePassportstagiaireFrontBundle:Homework:list.html.twig', $this->getTwigVars());
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addGetAction(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_homework_list'));
        }
        $homework = new Homework();
        $homeworkAddForm = $this->createForm(HomeworkAddTForm::class, $homework);
        $this->addTwigVar('homework', $homework);
        $this->addTwigVar('HomeworkAddForm', $homeworkAddForm->createView());

        $this->addTwigVar('admmenu_active', 'homeworks_add');
        $this->addTwigVar('pageTitle', $this->translate('Homework.pageTitle.admin.add'));
        $this->setHtmlHeadPageTitle($this->translate('Homework.htmlHeadPageTitle.admin.add') . ' - ' . $this->getParameter('sitename'));
        return $this->render('IlcfrancePassportstagiaireFrontBundle:Homework:add.html.twig', $this->getTwigVars());
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addPostAction(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_homework_list'));
        }
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_homework_addGet'));
        }
        $homework = new Homework();
        $homeworkAddForm = $this->createForm(HomeworkAddTForm::class, $homework);

        $reqData = $request->request->all();

        if (isset($reqData['HomeworkAddForm'])) {
            $homeworkAddForm->handleRequest($request);
            if ($homeworkAddForm->isValid()) {

                $em = $this->getEntityManager();

                $homeworkFile = $homeworkAddForm['file']->getData();

                $homeworkDir = $this->getParameter('kernel.root_dir') . '/../web/res/homeworks';

                $originalName = $homeworkFile->getClientOriginalName();
                $fileName = sha1(uniqid(mt_rand(), true)) . '.' . strtolower($homeworkFile->getClientOriginalExtension());
                $mimeType = $homeworkFile->getMimeType();
                $homeworkFile->move($homeworkDir, $fileName);

                $size = filesize($homeworkDir . '/' . $fileName);
                $md5 = md5_file($homeworkDir . '/' . $fileName);

                $homework->setFileName($fileName);
                $homework->setOriginalName($originalName);
                $homework->setSize($size);
                $homework->setMimeType($mimeType);
                $homework->setMd5($md5);

                $em->persist($homework);
                $em->flush();

                $this->addFlash('success', $this->translate('Homework.add.success', array(
                    '%homework%' => $homework->getOriginalName()
                )));

                return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_homework_editGet', array(
                    'id' => $homework->getId()
                )));
            } else {
                $this->addFlash('error', $this->translate('Homework.add.failure'));
            }
        }
        $this->addTwigVar('homework', $homework);
        $this->addTwigVar('HomeworkAddForm', $homeworkAddForm->createView());

        $this->addTwigVar('admmenu_active', 'homeworks_add');
        $this->addTwigVar('pageTitle', $this->translate('Homework.pageTitle.admin.add'));
        $this->setHtmlHeadPageTitle($this->translate('Homework.htmlHeadPageTitle.admin.add') . ' - ' . $this->getParameter('sitename'));
        return $this->render('IlcfrancePassportstagiaireFrontBundle:Homework:add.html.twig', $this->getTwigVars());
    }

    /**
     *
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id, Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_homework_list'));
        }
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_homework_list');
        }
        $em = $this->getEntityManager();
        try {
            $homework = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Homework')->find($id);

            if (null == $homework) {
                $this->addFlash('warning', $this->translate('Homework.notfound'));
            } else {
                $em->remove($homework);
                $em->flush();

                $this->addFlash('success', $this->translate('Homework.delete.success', array(
                    '%homework%' => $homework->getOriginalName()
                )));
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());

            $this->addFlash('error', $this->translate('Homework.delete.failure'));
        }

        return $this->redirect($urlFrom);
    }

    /**
     *
     * @param string $uid
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function downloadAction($id, Request $request)
    {
        $urlFrom = $this->getReferer();
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_homework_list');
        }
        $em = $this->getEntityManager();
        try {
            $homework = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Homework')->find($id);

            if (null == $homework) {
                $this->addFlash('warning', $this->translate('Homework.download.notfound'));
            } else {
                $homeworkDir = $this->getParameter('kernel.root_dir') . '/../web/res/homeworks';
                $fileName = $homework->getFileName();

                try {
                    $dlFile = new File($homeworkDir . '/' . $fileName);
                    $response = new StreamedResponse(function () use ($dlFile) {
                        $handle = fopen($dlFile->getRealPath(), 'r');
                        while (!feof($handle)) {
                            $buffer = fread($handle, 1024);
                            echo $buffer;
                            flush();
                        }
                        fclose($handle);
                    });

                    $response->headers->set('Content-Type', $homework->getMimeType());
                    $response->headers->set('Cache-Control', '');
                    $response->headers->set('Content-Length', $homework->getSize());
                    $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s', $homework->getDtUpdate()
                        ->getTimestamp()));
                    $fallback = $this->normalize($homework->getOriginalName());

                    $contentDisposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $homework->getOriginalName(), $fallback);
                    $response->headers->set('Content-Disposition', $contentDisposition);

                    $homework->setNbrDownloads($homework->getNbrDownloads() + 1);
                    $em->persist($homework);
                    $em->flush();

                    return $response;
                } catch (FileNotFoundException $fnfex) {
                    $this->addFlash('warning', $this->translate('Homework.download.notfound'));
                }
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
            $this->addFlash('warning', $this->translate('Homework.download.notfound'));
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
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_homework_list'));
        }
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_homework_list');
        }

        $em = $this->getEntityManager();
        try {
            $homework = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Homework')->find($id);

            if (null == $homework) {
                $this->addFlash('warning', $this->translate('Homework.notfound'));
                return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_homework_list'));
            } else {
                $homeworkUpdateDescriptionForm = $this->createForm(HomeworkUpdateDescriptionTForm::class, $homework);
                $homeworkUpdateContentForm = $this->createForm(HomeworkUpdateContentTForm::class, $homework);
                $homeworkUpdateOriginalNameForm = $this->createForm(HomeworkUpdateOriginalNameTForm::class, $homework);
                $homeworkUpdateLevelForm = $this->createForm(HomeworkUpdateLevelTForm::class, $homework);

                $this->addTwigVar('tabActive', $this->getSession()
                    ->get('tabActive', 1));
                $this->getSession()->remove('tabActive');

                $this->addTwigVar('homework', $homework);
                $this->addTwigVar('HomeworkUpdateDescriptionForm', $homeworkUpdateDescriptionForm->createView());
                $this->addTwigVar('HomeworkUpdateContentForm', $homeworkUpdateContentForm->createView());
                $this->addTwigVar('HomeworkUpdateOriginalNameForm', $homeworkUpdateOriginalNameForm->createView());
                $this->addTwigVar('HomeworkUpdateLevelForm', $homeworkUpdateLevelForm->createView());

                $this->addTwigVar('admmenu_active', 'homeworks_edit');
                $this->addTwigVar('pageTitle', $this->translate('Homework.pageTitle.admin.edit', array(
                    '%homework%' => $homework->getOriginalName()
                )));
                $this->setHtmlHeadPageTitle($this->translate('Homework.htmlHeadPageTitle.admin.edit', array(
                    '%homework%' => $homework->getOriginalName()
                )) . ' - ' . $this->getParameter('sitename'));

                return $this->render('IlcfrancePassportstagiaireFrontBundle:Homework:edit.html.twig', $this->getTwigVars());
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
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_homework_list'));
        }
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_homework_list');
        }

        $em = $this->getEntityManager();
        try {
            $homework = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Homework')->find($id);

            if (null == $homework) {
                $this->addFlash('warning', $this->translate('Homework.notfound'));
                return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_homework_list'));
            } else {
                $homeworkUpdateDescriptionForm = $this->createForm(HomeworkUpdateDescriptionTForm::class, $homework);
                $homeworkUpdateContentForm = $this->createForm(HomeworkUpdateContentTForm::class, $homework);
                $homeworkUpdateOriginalNameForm = $this->createForm(HomeworkUpdateOriginalNameTForm::class, $homework);
                $homeworkUpdateLevelForm = $this->createForm(HomeworkUpdateLevelTForm::class, $homework);

                $this->addTwigVar('tabActive', $this->getSession()
                    ->get('tabActive', 1));
                $this->getSession()->remove('tabActive');
                $reqData = $request->request->all();

                if (isset($reqData['HomeworkUpdateDescriptionForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $homeworkUpdateDescriptionForm->handleRequest($request);
                    if ($homeworkUpdateDescriptionForm->isValid()) {
                        $em->persist($homework);
                        $em->flush();

                        $this->addFlash('success', $this->translate('Homework.edit.success', array(
                            '%homework%' => $homework->getOriginalName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($homework);

                        $this->addFlash('error', $this->translate('Homework.edit.failure', array(
                            '%homework%' => $homework->getOriginalName()
                        )));
                    }
                } elseif (isset($reqData['HomeworkUpdateOriginalNameForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $homeworkUpdateOriginalNameForm->handleRequest($request);
                    if ($homeworkUpdateOriginalNameForm->isValid()) {
                        $em->persist($homework);
                        $em->flush();

                        $this->addFlash('success', $this->translate('Homework.edit.success', array(
                            '%homework%' => $homework->getOriginalName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($homework);

                        $this->addFlash('error', $this->translate('Homework.edit.failure', array(
                            '%homework%' => $homework->getOriginalName()
                        )));
                    }
                } elseif (isset($reqData['HomeworkUpdateLevelForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $homeworkUpdateLevelForm->handleRequest($request);
                    if ($homeworkUpdateLevelForm->isValid()) {
                        $em->persist($homework);
                        $em->flush();
                        
                        $this->addFlash('success', $this->translate('Homework.edit.success', array(
                            '%homework%' => $homework->getOriginalName()
                        )));
                        
                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($homework);
                        
                        $this->addFlash('error', $this->translate('Homework.edit.failure', array(
                            '%homework%' => $homework->getOriginalName()
                        )));
                    }
                } elseif (isset($reqData['HomeworkUpdateContentForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $homeworkUpdateContentForm->handleRequest($request);
                    if ($homeworkUpdateContentForm->isValid()) {

                        $homeworkFile = $homeworkUpdateContentForm['file']->getData();

                        $homeworkDir = $this->getParameter('kernel.root_dir') . '/../web/res/homeworks';

                        $originalName = $homeworkFile->getClientOriginalName();
                        $fileName = sha1(uniqid(mt_rand(), true)) . '.' . strtolower($homeworkFile->getClientOriginalExtension());
                        $mimeType = $homeworkFile->getMimeType();
                        $homeworkFile->move($homeworkDir, $fileName);

                        $size = filesize($homeworkDir . '/' . $fileName);
                        $md5 = md5_file($homeworkDir . '/' . $fileName);

                        $homework->setFileName($fileName);
                        $homework->setOriginalName($originalName);
                        $homework->setSize($size);
                        $homework->setMimeType($mimeType);
                        $homework->setMd5($md5);

                        $em->persist($homework);
                        $em->flush();

                        $this->addFlash('success', $this->translate('Homework.edit.success', array(
                            '%homework%' => $homework->getOriginalName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {

                        $em->refresh($homework);

                        $this->addFlash('error', $this->translate('Homework.edit.failure', array(
                            '%homework%' => $homework->getOriginalName()
                        )));
                    }
                }

                $this->addTwigVar('homework', $homework);
                $this->addTwigVar('HomeworkUpdateDescriptionForm', $homeworkUpdateDescriptionForm->createView());
                $this->addTwigVar('HomeworkUpdateContentForm', $homeworkUpdateContentForm->createView());
                $this->addTwigVar('HomeworkUpdateOriginalNameForm', $homeworkUpdateOriginalNameForm->createView());
                $this->addTwigVar('HomeworkUpdateLevelForm', $homeworkUpdateLevelForm->createView());

                $this->addTwigVar('admmenu_active', 'homeworks_edit');
                $this->addTwigVar('pageTitle', $this->translate('Homework.pageTitle.admin.edit', array(
                    '%homework%' => $homework->getOriginalName()
                )));
                $this->setHtmlHeadPageTitle($this->translate('Homework.htmlHeadPageTitle.admin.edit', array(
                    '%homework%' => $homework->getOriginalName()
                )) . ' - ' . $this->getParameter('sitename'));

                return $this->render('IlcfrancePassportstagiaireFrontBundle:Homework:edit.html.twig', $this->getTwigVars());
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
        }

        return $this->redirect($urlFrom);
    }
}