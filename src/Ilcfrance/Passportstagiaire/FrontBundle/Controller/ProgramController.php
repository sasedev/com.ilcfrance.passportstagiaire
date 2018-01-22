<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Controller;

use Ilcfrance\Passportstagiaire\FrontBundle\Form\Program\AddTForm as ProgramAddTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Program\UpdateContentTForm as ProgramUpdateContentTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Program\UpdateDescriptionTForm as ProgramUpdateDescriptionTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Program\UpdateOriginalNameTForm as ProgramUpdateOriginalNameTForm;
use Ilcfrance\Passportstagiaire\ResBundle\Controller\IlcfranceController;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\Program;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
class ProgramController extends IlcfranceController
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->addTwigVar('menu_active', 'admin');
        $this->addTwigVar('admmenu_active', 'programs');
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $em = $this->getEntityManager();
        $programs = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Program')->findAll();
        $this->addTwigVar('programs', $programs);

        $this->addTwigVar('admmenu_active', 'programs_list');
        $this->addTwigVar('pageTitle', $this->translate('Program.pageTitle.admin.list'));
        $this->setHtmlHeadPageTitle($this->translate('Program.htmlHeadPageTitle.admin.list') . ' - ' . $this->getParameter('sitename'));
        return $this->render('IlcfrancePassportstagiaireFrontBundle:Program:list.html.twig', $this->getTwigVars());
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addGetAction(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_program_list'));
        }
        $program = new Program();
        $programAddForm = $this->createForm(ProgramAddTForm::class, $program);
        $this->addTwigVar('program', $program);
        $this->addTwigVar('ProgramAddForm', $programAddForm->createView());

        $this->addTwigVar('admmenu_active', 'programs_add');
        $this->addTwigVar('pageTitle', $this->translate('Program.pageTitle.admin.add'));
        $this->setHtmlHeadPageTitle($this->translate('Program.htmlHeadPageTitle.admin.add') . ' - ' . $this->getParameter('sitename'));
        return $this->render('IlcfrancePassportstagiaireFrontBundle:Program:add.html.twig', $this->getTwigVars());
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addPostAction(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_program_list'));
        }
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_program_addGet'));
        }
        $program = new Program();
        $programAddForm = $this->createForm(ProgramAddTForm::class, $program);

        $reqData = $request->request->all();

        if (isset($reqData['ProgramAddForm'])) {
            $programAddForm->handleRequest($request);
            if ($programAddForm->isValid()) {

                $em = $this->getEntityManager();

                $programFile = $programAddForm['file']->getData();

                $programDir = $this->getParameter('kernel.root_dir') . '/../web/res/programs';

                $originalName = $programFile->getClientOriginalName();
                $fileName = sha1(uniqid(mt_rand(), true)) . '.' . strtolower($programFile->getClientOriginalExtension());
                $mimeType = $programFile->getMimeType();
                $programFile->move($programDir, $fileName);

                $size = filesize($programDir . '/' . $fileName);
                $md5 = md5_file($programDir . '/' . $fileName);

                $program->setFileName($fileName);
                $program->setOriginalName($originalName);
                $program->setSize($size);
                $program->setMimeType($mimeType);
                $program->setMd5($md5);

                $em->persist($program);
                $em->flush();

                $this->addFlash('success', $this->translate('Program.add.success', array(
                    '%program%' => $program->getOriginalName()
                )));

                return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_program_editGet', array(
                    'id' => $program->getId()
                )));
            } else {
                $this->addFlash('error', $this->translate('Program.add.failure'));
            }
        }
        $this->addTwigVar('program', $program);
        $this->addTwigVar('ProgramAddForm', $programAddForm->createView());

        $this->addTwigVar('admmenu_active', 'programs_add');
        $this->addTwigVar('pageTitle', $this->translate('Program.pageTitle.admin.add'));
        $this->setHtmlHeadPageTitle($this->translate('Program.htmlHeadPageTitle.admin.add') . ' - ' . $this->getParameter('sitename'));
        return $this->render('IlcfrancePassportstagiaireFrontBundle:Program:add.html.twig', $this->getTwigVars());
    }

    /**
     *
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id, Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_program_list'));
        }
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_program_list');
        }
        $em = $this->getEntityManager();
        try {
            $program = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Program')->find($id);

            if (null == $program) {
                $this->addFlash('warning', $this->translate('Program.notfound'));
            } else {
                $em->remove($program);
                $em->flush();

                $this->addFlash('success', $this->translate('Program.delete.success', array(
                    '%program%' => $program->getOriginalName()
                )));
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());

            $this->addFlash('error', $this->translate('Program.delete.failure'));
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
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_program_list');
        }
        $em = $this->getEntityManager();
        try {
            $program = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Program')->find($id);

            if (null == $program) {
                $this->addFlash('warning', $this->translate('Program.download.notfound'));
            } else {
                $programDir = $this->getParameter('kernel.root_dir') . '/../web/res/programs';
                $fileName = $program->getFileName();

                try {
                    $dlFile = new File($programDir . '/' . $fileName);
                    $response = new StreamedResponse(function () use ($dlFile) {
                        $handle = fopen($dlFile->getRealPath(), 'r');
                        while (!feof($handle)) {
                            $buffer = fread($handle, 1024);
                            echo $buffer;
                            flush();
                        }
                        fclose($handle);
                    });

                    $response->headers->set('Content-Type', $program->getMimeType());
                    $response->headers->set('Cache-Control', '');
                    $response->headers->set('Content-Length', $program->getSize());
                    $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s', $program->getDtUpdate()
                        ->getTimestamp()));
                    $fallback = $this->normalize($program->getOriginalName());

                    $contentDisposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $program->getOriginalName(), $fallback);
                    $response->headers->set('Content-Disposition', $contentDisposition);

                    $program->setNbrDownloads($program->getNbrDownloads() + 1);
                    $em->persist($program);
                    $em->flush();

                    return $response;
                } catch (FileNotFoundException $fnfex) {
                    $this->addFlash('warning', $this->translate('Program.download.notfound'));
                }
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
            $this->addFlash('warning', $this->translate('Program.download.notfound'));
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
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_program_list'));
        }
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_program_list');
        }

        $em = $this->getEntityManager();
        try {
            $program = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Program')->find($id);

            if (null == $program) {
                $this->addFlash('warning', $this->translate('Program.notfound'));
                return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_program_list'));
            } else {
                $programUpdateDescriptionForm = $this->createForm(ProgramUpdateDescriptionTForm::class, $program);
                $programUpdateContentForm = $this->createForm(ProgramUpdateContentTForm::class, $program);
                $programUpdateOriginalNameForm = $this->createForm(ProgramUpdateOriginalNameTForm::class, $program);

                $this->addTwigVar('tabActive', $this->getSession()
                    ->get('tabActive', 1));
                $this->getSession()->remove('tabActive');

                $this->addTwigVar('program', $program);
                $this->addTwigVar('ProgramUpdateDescriptionForm', $programUpdateDescriptionForm->createView());
                $this->addTwigVar('ProgramUpdateContentForm', $programUpdateContentForm->createView());
                $this->addTwigVar('ProgramUpdateOriginalNameForm', $programUpdateOriginalNameForm->createView());

                $this->addTwigVar('admmenu_active', 'programs_edit');
                $this->addTwigVar('pageTitle', $this->translate('Program.pageTitle.admin.edit', array(
                    '%program%' => $program->getOriginalName()
                )));
                $this->setHtmlHeadPageTitle($this->translate('Program.htmlHeadPageTitle.admin.edit', array(
                    '%program%' => $program->getOriginalName()
                )) . ' - ' . $this->getParameter('sitename'));

                return $this->render('IlcfrancePassportstagiaireFrontBundle:Program:edit.html.twig', $this->getTwigVars());
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
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_program_list'));
        }
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_program_list');
        }

        $em = $this->getEntityManager();
        try {
            $program = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Program')->find($id);

            if (null == $program) {
                $this->addFlash('warning', $this->translate('Program.notfound'));
                return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_program_list'));
            } else {
                $programUpdateDescriptionForm = $this->createForm(ProgramUpdateDescriptionTForm::class, $program);
                $programUpdateContentForm = $this->createForm(ProgramUpdateContentTForm::class, $program);
                $programUpdateOriginalNameForm = $this->createForm(ProgramUpdateOriginalNameTForm::class, $program);

                $this->addTwigVar('tabActive', $this->getSession()
                    ->get('tabActive', 1));
                $this->getSession()->remove('tabActive');
                $reqData = $request->request->all();

                if (isset($reqData['ProgramUpdateDescriptionForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $programUpdateDescriptionForm->handleRequest($request);
                    if ($programUpdateDescriptionForm->isValid()) {
                        $em->persist($program);
                        $em->flush();

                        $this->addFlash('success', $this->translate('Program.edit.success', array(
                            '%program%' => $program->getOriginalName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($program);

                        $this->addFlash('error', $this->translate('Program.edit.failure', array(
                            '%program%' => $program->getOriginalName()
                        )));
                    }
                } elseif (isset($reqData['ProgramUpdateOriginalNameForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $programUpdateOriginalNameForm->handleRequest($request);
                    if ($programUpdateOriginalNameForm->isValid()) {
                        $em->persist($program);
                        $em->flush();

                        $this->addFlash('success', $this->translate('Program.edit.success', array(
                            '%program%' => $program->getOriginalName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($program);

                        $this->addFlash('error', $this->translate('Program.edit.failure', array(
                            '%program%' => $program->getOriginalName()
                        )));
                    }
                } elseif (isset($reqData['ProgramUpdateContentForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $programUpdateContentForm->handleRequest($request);
                    if ($programUpdateContentForm->isValid()) {

                        $programFile = $programUpdateContentForm['file']->getData();

                        $programDir = $this->getParameter('kernel.root_dir') . '/../web/res/programs';

                        $originalName = $programFile->getClientOriginalName();
                        $fileName = sha1(uniqid(mt_rand(), true)) . '.' . strtolower($programFile->getClientOriginalExtension());
                        $mimeType = $programFile->getMimeType();
                        $programFile->move($programDir, $fileName);

                        $size = filesize($programDir . '/' . $fileName);
                        $md5 = md5_file($programDir . '/' . $fileName);

                        $program->setFileName($fileName);
                        $program->setOriginalName($originalName);
                        $program->setSize($size);
                        $program->setMimeType($mimeType);
                        $program->setMd5($md5);

                        $em->persist($program);
                        $em->flush();

                        $this->addFlash('success', $this->translate('Program.edit.success', array(
                            '%program%' => $program->getOriginalName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {

                        $em->refresh($program);

                        $this->addFlash('error', $this->translate('Program.edit.failure', array(
                            '%program%' => $program->getOriginalName()
                        )));
                    }
                }

                $this->addTwigVar('program', $program);
                $this->addTwigVar('ProgramUpdateDescriptionForm', $programUpdateDescriptionForm->createView());
                $this->addTwigVar('ProgramUpdateContentForm', $programUpdateContentForm->createView());
                $this->addTwigVar('ProgramUpdateOriginalNameForm', $programUpdateOriginalNameForm->createView());

                $this->addTwigVar('admmenu_active', 'programs_edit');
                $this->addTwigVar('pageTitle', $this->translate('Program.pageTitle.admin.edit', array(
                    '%program%' => $program->getOriginalName()
                )));
                $this->setHtmlHeadPageTitle($this->translate('Program.htmlHeadPageTitle.admin.edit', array(
                    '%program%' => $program->getOriginalName()
                )) . ' - ' . $this->getParameter('sitename'));

                return $this->render('IlcfrancePassportstagiaireFrontBundle:Program:edit.html.twig', $this->getTwigVars());
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
        }

        return $this->redirect($urlFrom);
    }
}