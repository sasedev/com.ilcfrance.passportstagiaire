<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Controller;

use Swift_EmbeddedFile;
use Swift_Message;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecordDocument\UpdateContentTForm as TraineeRecordDocumentUpdateContentTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecordDocument\UpdateDescriptionTForm as TraineeRecordDocumentUpdateDescriptionTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecordDocument\UpdateOriginalNameTForm as TraineeRecordDocumentUpdateOriginalNameTForm;
use Ilcfrance\Passportstagiaire\ResBundle\Controller\IlcfranceController;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 *
 * @author sasedev
 */
class TraineeRecordDocumentController extends IlcfranceController
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->addTwigVar('menu_active', 'admin');
        $this->addTwigVar('admmenu_active', 'trainees');
    }

    /**
     *
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id, Request $request)
    {
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_trainee_list');
        }
        $em = $this->getEntityManager();
        try {
            $traineeRecordDocument = $em->getRepository('IlcfrancePassportstagiaireDataBundle:TraineeRecordDocument')->find($id);

            if (null == $traineeRecordDocument) {
                $this->addFlash('warning', $this->translate('TraineeRecordDocument.notfound'));
            } else {
                if (!$this->isGranted('ROLE_ADMIN')) {
                    $user = $this->getSecurityTokenStorage()
                        ->getToken()
                        ->getUser();
                    if ($user->getId() != $traineeRecordDocument->getTraineeRecord()
                        ->getTeacher()
                        ->getId()) {
                        $this->addFlash('error', $this->translate('TraineeRecordDocument.delete.failure'));
                        return $this->redirect($urlFrom);
                    }
                }

                $em->remove($traineeRecordDocument);
                $em->flush();

                $this->addFlash('success', $this->translate('TraineeRecordDocument.delete.success', array(
                    '%traineeRecordDocument%' => $traineeRecordDocument->getOriginalName()
                )));
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());

            $this->addFlash('error', $this->translate('TraineeRecordDocument.delete.failure'));
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
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_trainee_list');
        }
        $em = $this->getEntityManager();
        try {
            $traineeRecordDocument = $em->getRepository('IlcfrancePassportstagiaireDataBundle:TraineeRecordDocument')->find($id);

            if (null == $traineeRecordDocument) {
                $this->addFlash('warning', $this->translate('TraineeRecordDocument.download.notfound'));
            } else {
                $traineeRecordDocumentDir = $this->getParameter('kernel.root_dir') . '/../web/res/traineerecorddocuments';
                $fileName = $traineeRecordDocument->getFileName();

                try {
                    $dlFile = new File($traineeRecordDocumentDir . '/' . $fileName);
                    $response = new StreamedResponse(function () use ($dlFile) {
                        $handle = fopen($dlFile->getRealPath(), 'r');
                        while (!feof($handle)) {
                            $buffer = fread($handle, 1024);
                            echo $buffer;
                            flush();
                        }
                        fclose($handle);
                    });

                    $response->headers->set('Content-Type', $traineeRecordDocument->getMimeType());
                    $response->headers->set('Cache-Control', '');
                    $response->headers->set('Content-Length', $traineeRecordDocument->getSize());
                    $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s', $traineeRecordDocument->getDtUpdate()
                        ->getTimestamp()));
                    $fallback = $this->normalize($traineeRecordDocument->getOriginalName());

                    $contentDisposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $traineeRecordDocument->getOriginalName(), $fallback);
                    $response->headers->set('Content-Disposition', $contentDisposition);

                    $traineeRecordDocument->setNbrDownloads($traineeRecordDocument->getNbrDownloads() + 1);
                    $em->persist($traineeRecordDocument);
                    $em->flush();

                    return $response;
                } catch (FileNotFoundException $fnfex) {
                    $this->addFlash('warning', $this->translate('TraineeRecordDocument.download.notfound'));
                }
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
            $this->addFlash('warning', $this->translate('TraineeRecordDocument.download.notfound'));
        }

        return $this->redirect($urlFrom);
    }

    /**
     *
     * @param string $uid
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function sendmailAction($id, Request $request)
    {
        $urlFrom = $this->getReferer();
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_trainee_list');
        }
        $em = $this->getEntityManager();
        try {
            $traineeRecordDocument = $em->getRepository('IlcfrancePassportstagiaireDataBundle:TraineeRecordDocument')->find($id);

            if (null == $traineeRecordDocument) {
                $this->addFlash('warning', $this->translate('TraineeRecordDocument.sendmail.notfound'));
            } else {
                $traineeRecordDocumentDir = $this->getParameter('kernel.root_dir') . '/../web/res/traineerecorddocuments';
                $fileName = $traineeRecordDocument->getFileName();
                $user = $traineeRecordDocument->getTraineeRecord()->getTrainee();
                $email = $user->getEmail();
                if (null == $email || \trim($email) == '') {
                    $this->addFlash('warning', $this->translate('TraineeRecordDocument.sendmail.notemail'));
                } else {
                    try {
                        $dlFile = new File($traineeRecordDocumentDir . '/' . $fileName);

                        $mvars = array();
                        $mvars['user'] = $user;
                        $nfilename = $this->normalize($traineeRecordDocument->getOriginalName());
                        $mvars['filename'] = $nfilename;
                        $mvars['filedesc'] = $traineeRecordDocument->getDescription();

                        $attachement = Swift_EmbeddedFile::fromPath($dlFile->getRealPath());
                        $attachement->setFilename($nfilename);
                        $attachement->setContentType($traineeRecordDocument->getMimeType());
                        $attachement->setDisposition('attachement');

                        $from = $this->getParameter('mail_from');
                        $fromName = $this->getParameter('mail_from_name');
                        $subject = $this->translate('_mail.TraineeRecordDocument_subject', array());
                        $message = Swift_Message::newInstance();

                        $mvars['attachment'] = $message->embed($attachement);

                        $message->setFrom($from, $fromName)
                            ->setTo($user->getEmail(), $user->getFullname())
                            ->setSubject($subject)
                            ->setBody($this->renderView('IlcfrancePassportstagiaireFrontBundle:TraineeRecordDocument:sendmail.html.twig', $mvars), 'text/html');
                        // $message->attach(Swift_EmbeddedFile::fromPath($dlFile->getRealPath())->setFilename($this->normalize($traineeRecordDocument->getOriginalName()))
                        // ->setContentType($traineeRecordDocument->getMimeType()));

                        $this->sendmail($message);

                        $traineeRecordDocument->setNbrEmails($traineeRecordDocument->getNbrEmails() + 1);
                        $em->persist($traineeRecordDocument);
                        $em->flush();
                        $this->addFlash('success', $this->translate('TraineeRecordDocument.sendmail.success', array(
                            '%traineeRecordDocument%' => $traineeRecordDocument->getOriginalName()
                        )));
                    } catch (FileNotFoundException $fnfex) {
                        $this->addFlash('warning', $this->translate('TraineeRecordDocument.sendmail.notfound'));
                    }
                }
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
            $this->addFlash('warning', $this->translate('TraineeRecordDocument.sendmail.notfound'));
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
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_trainee_list');
        }

        $em = $this->getEntityManager();
        try {
            $traineeRecordDocument = $em->getRepository('IlcfrancePassportstagiaireDataBundle:TraineeRecordDocument')->find($id);

            if (null == $traineeRecordDocument) {
                $this->addFlash('warning', $this->translate('TraineeRecordDocument.notfound'));
                return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_trainee_list'));
            } else {
                if (!$this->isGranted('ROLE_ADMIN')) {
                    $user = $this->getSecurityTokenStorage()
                        ->getToken()
                        ->getUser();
                    if ($user->getId() != $traineeRecordDocument->getTraineeRecord()
                        ->getTeacher()
                        ->getId()) {
                        $this->addFlash('error', $this->translate('TraineeRecordDocument.edit.failure'));
                        return $this->redirect($urlFrom);
                    }
                }
                $traineeRecordDocumentUpdateDescriptionForm = $this->createForm(TraineeRecordDocumentUpdateDescriptionTForm::class, $traineeRecordDocument);
                $traineeRecordDocumentUpdateContentForm = $this->createForm(TraineeRecordDocumentUpdateContentTForm::class, $traineeRecordDocument);
                $traineeRecordDocumentUpdateOriginalNameForm = $this->createForm(TraineeRecordDocumentUpdateOriginalNameTForm::class, $traineeRecordDocument);

                $this->addTwigVar('tabActive', $this->getSession()
                    ->get('tabActive', 1));
                $this->getSession()->remove('tabActive');

                $this->addTwigVar('traineeRecordDocument', $traineeRecordDocument);
                $this->addTwigVar('TraineeRecordDocumentUpdateDescriptionForm', $traineeRecordDocumentUpdateDescriptionForm->createView());
                $this->addTwigVar('TraineeRecordDocumentUpdateContentForm', $traineeRecordDocumentUpdateContentForm->createView());
                $this->addTwigVar('TraineeRecordDocumentUpdateOriginalNameForm', $traineeRecordDocumentUpdateOriginalNameForm->createView());

                $this->addTwigVar('pageTitle', $this->translate('TraineeRecordDocument.pageTitle.admin.edit', array(
                    '%traineeRecordDocument%' => $traineeRecordDocument->getOriginalName()
                )));
                $this->setHtmlHeadPageTitle($this->translate('TraineeRecordDocument.htmlHeadPageTitle.admin.edit', array(
                    '%traineeRecordDocument%' => $traineeRecordDocument->getOriginalName()
                )) . ' - ' . $this->getParameter('sitename'));

                return $this->render('IlcfrancePassportstagiaireFrontBundle:TraineeRecordDocument:edit.html.twig', $this->getTwigVars());
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
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_trainee_list');
        }

        $em = $this->getEntityManager();
        try {
            $traineeRecordDocument = $em->getRepository('IlcfrancePassportstagiaireDataBundle:TraineeRecordDocument')->find($id);

            if (null == $traineeRecordDocument) {
                $this->addFlash('warning', $this->translate('TraineeRecordDocument.notfound'));
                return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_trainee_list'));
            } else {
                if (!$this->isGranted('ROLE_ADMIN')) {
                    $user = $this->getSecurityTokenStorage()
                        ->getToken()
                        ->getUser();
                    if ($user->getId() != $traineeRecordDocument->getTraineeRecord()
                        ->getTeacher()
                        ->getId()) {
                        $this->addFlash('error', $this->translate('TraineeRecordDocument.edit.failure'));
                        return $this->redirect($urlFrom);
                    }
                }
                $traineeRecordDocumentUpdateDescriptionForm = $this->createForm(TraineeRecordDocumentUpdateDescriptionTForm::class, $traineeRecordDocument);
                $traineeRecordDocumentUpdateContentForm = $this->createForm(TraineeRecordDocumentUpdateContentTForm::class, $traineeRecordDocument);
                $traineeRecordDocumentUpdateOriginalNameForm = $this->createForm(TraineeRecordDocumentUpdateOriginalNameTForm::class, $traineeRecordDocument);

                $this->addTwigVar('tabActive', $this->getSession()
                    ->get('tabActive', 1));
                $this->getSession()->remove('tabActive');
                $reqData = $request->request->all();

                if (isset($reqData['TraineeRecordDocumentUpdateDescriptionForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeRecordDocumentUpdateDescriptionForm->handleRequest($request);
                    if ($traineeRecordDocumentUpdateDescriptionForm->isValid()) {
                        $em->persist($traineeRecordDocument);
                        $em->flush();

                        $this->addFlash('success', $this->translate('TraineeRecordDocument.edit.success', array(
                            '%traineeRecordDocument%' => $traineeRecordDocument->getOriginalName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($traineeRecordDocument);

                        $this->addFlash('error', $this->translate('TraineeRecordDocument.edit.failure', array(
                            '%traineeRecordDocument%' => $traineeRecordDocument->getOriginalName()
                        )));
                    }
                } elseif (isset($reqData['TraineeRecordDocumentUpdateOriginalNameForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeRecordDocumentUpdateOriginalNameForm->handleRequest($request);
                    if ($traineeRecordDocumentUpdateOriginalNameForm->isValid()) {
                        $em->persist($traineeRecordDocument);
                        $em->flush();

                        $this->addFlash('success', $this->translate('TraineeRecordDocument.edit.success', array(
                            '%traineeRecordDocument%' => $traineeRecordDocument->getOriginalName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($traineeRecordDocument);

                        $this->addFlash('error', $this->translate('TraineeRecordDocument.edit.failure', array(
                            '%traineeRecordDocument%' => $traineeRecordDocument->getOriginalName()
                        )));
                    }
                } elseif (isset($reqData['TraineeRecordDocumentUpdateContentForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeRecordDocumentUpdateContentForm->handleRequest($request);
                    if ($traineeRecordDocumentUpdateContentForm->isValid()) {

                        $traineeRecordDocumentFile = $traineeRecordDocumentUpdateContentForm['file']->getData();

                        $traineeRecordDocumentDir = $this->getParameter('kernel.root_dir') . '/../web/res/traineerecorddocuments';

                        $originalName = $traineeRecordDocumentFile->getClientOriginalName();
                        $fileName = sha1(uniqid(mt_rand(), true)) . '.' . strtolower($traineeRecordDocumentFile->getClientOriginalExtension());
                        $mimeType = $traineeRecordDocumentFile->getMimeType();
                        $traineeRecordDocumentFile->move($traineeRecordDocumentDir, $fileName);

                        $size = filesize($traineeRecordDocumentDir . '/' . $fileName);
                        $md5 = md5_file($traineeRecordDocumentDir . '/' . $fileName);

                        $traineeRecordDocument->setFileName($fileName);
                        $traineeRecordDocument->setOriginalName($originalName);
                        $traineeRecordDocument->setSize($size);
                        $traineeRecordDocument->setMimeType($mimeType);
                        $traineeRecordDocument->setMd5($md5);

                        $em->persist($traineeRecordDocument);
                        $em->flush();

                        $this->addFlash('success', $this->translate('TraineeRecordDocument.edit.success', array(
                            '%traineeRecordDocument%' => $traineeRecordDocument->getOriginalName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {

                        $em->refresh($traineeRecordDocument);

                        $this->addFlash('error', $this->translate('TraineeRecordDocument.edit.failure', array(
                            '%traineeRecordDocument%' => $traineeRecordDocument->getOriginalName()
                        )));
                    }
                }

                $this->addTwigVar('doc', $traineeRecordDocument);
                $this->addTwigVar('TraineeRecordDocumentUpdateDescriptionForm', $traineeRecordDocumentUpdateDescriptionForm->createView());
                $this->addTwigVar('TraineeRecordDocumentUpdateContentForm', $traineeRecordDocumentUpdateContentForm->createView());
                $this->addTwigVar('TraineeRecordDocumentUpdateOriginalNameForm', $traineeRecordDocumentUpdateOriginalNameForm->createView());

                $this->addTwigVar('pageTitle', $this->translate('TraineeRecordDocument.pageTitle.admin.edit', array(
                    '%traineeRecordDocument%' => $traineeRecordDocument->getOriginalName()
                )));
                $this->setHtmlHeadPageTitle($this->translate('TraineeRecordDocument.htmlHeadPageTitle.admin.edit', array(
                    '%traineeRecordDocument%' => $traineeRecordDocument->getOriginalName()
                )) . ' - ' . $this->getParameter('sitename'));

                return $this->render('IlcfrancePassportstagiaireFrontBundle:TraineeRecordDocument:edit.html.twig', $this->getTwigVars());
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
        }

        return $this->redirect($urlFrom);
    }
}