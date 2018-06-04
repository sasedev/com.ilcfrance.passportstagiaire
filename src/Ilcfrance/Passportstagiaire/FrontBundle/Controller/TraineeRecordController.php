<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Controller;

use Ilcfrance\Passportstagiaire\ResBundle\Controller\IlcfranceController;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateCommentsTForm as TraineeRecordUpdateCommentsTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateHistoricalTForm as TraineeRecordUpdateHistoricalTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateHomeworksTForm as TraineeRecordUpdateHomeworksTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateRecordDateTForm as TraineeRecordUpdateRecordDateTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateTakeawaysTForm as TraineeRecordUpdateTakeawaysTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateTeacherTForm as TraineeRecordUpdateTeacherTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateTraineeTForm as TraineeRecordUpdateTraineeTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateWorksCoveredTForm as TraineeRecordUpdateWorksCoveredTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecordDocument\AddTForm as TraineeRecordDocumentAddTForm;
use Symfony\Component\HttpFoundation\Request;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\TraineeRecordDocument;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
class TraineeRecordController extends IlcfranceController
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
            $traineeRecord = $em->getRepository('IlcfrancePassportstagiaireDataBundle:TraineeRecord')->find($id);

            if (null == $traineeRecord) {
                $this->addFlash('warning', $this->translate('TraineeRecord.notfound'));
            } else {
                if (!$this->isGranted('ROLE_ADMIN')) {
                    $user = $this->getSecurityTokenStorage()
                        ->getToken()
                        ->getUser();
                    if ($user->getId() != $traineeRecord->getTeacher()->getId()) {
                        $this->addFlash('error', $this->translate('TraineeRecord.delete.failure'));
                        return $this->redirect($urlFrom);
                    }
                }

                $em->remove($traineeRecord);
                $em->flush();

                $this->addFlash('success', $this->translate('TraineeRecord.delete.success', array(
                    '%traineeRecord%' => $traineeRecord->getFullName()
                )));
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());

            $this->addFlash('error', $this->translate('TraineeRecord.delete.failure'));
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
            $traineeRecord = $em->getRepository('IlcfrancePassportstagiaireDataBundle:TraineeRecord')->find($id);

            if (null == $traineeRecord) {
                $this->addFlash('warning', $this->translate('TraineeRecord.notfound'));
                return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_trainee_list'));
            } else {
                if (!$this->isGranted('ROLE_ADMIN')) {
                    $user = $this->getSecurityTokenStorage()
                        ->getToken()
                        ->getUser();
                    if ($user->getId() != $traineeRecord->getTeacher()->getId()) {
                        $this->addFlash('error', $this->translate('TraineeRecord.edit.failure'));
                        return $this->redirect($urlFrom);
                    }
                }
                $traineeRecordUpdateCommentsForm = $this->createForm(TraineeRecordUpdateCommentsTForm::class, $traineeRecord);
                $traineeRecordUpdateHistoricalForm = $this->createForm(TraineeRecordUpdateHistoricalTForm::class, $traineeRecord, array(
                    'trainee' => $traineeRecord->getTrainee()
                ));
                $traineeRecordUpdateHomeworksForm = $this->createForm(TraineeRecordUpdateHomeworksTForm::class, $traineeRecord);
                $traineeRecordUpdateRecordDateForm = $this->createForm(TraineeRecordUpdateRecordDateTForm::class, $traineeRecord);
                $traineeRecordUpdateTakeawaysForm = $this->createForm(TraineeRecordUpdateTakeawaysTForm::class, $traineeRecord);
                $traineeRecordUpdateTeacherForm = $this->createForm(TraineeRecordUpdateTeacherTForm::class, $traineeRecord);
                $traineeRecordUpdateTraineeForm = $this->createForm(TraineeRecordUpdateTraineeTForm::class, $traineeRecord);
                $traineeRecordUpdateWorksCoveredForm = $this->createForm(TraineeRecordUpdateWorksCoveredTForm::class, $traineeRecord);

                $traineeRecordDocument = new TraineeRecordDocument();
                $traineeRecordDocument->setTraineeRecord($traineeRecord);
                $traineeRecordDocumentAddForm = $this->createForm(TraineeRecordDocumentAddTForm::class, $traineeRecordDocument, array(
                    'traineeRecord' => $traineeRecord
                ));

                $this->addTwigVar('tabActive', $this->getSession()
                    ->get('tabActive', 1));
                $this->getSession()->remove('tabActive');

                $this->addTwigVar('traineeRecord', $traineeRecord);
                $this->addTwigVar('TraineeRecordUpdateCommentsForm', $traineeRecordUpdateCommentsForm->createView());
                $this->addTwigVar('TraineeRecordUpdateHistoricalForm', $traineeRecordUpdateHistoricalForm->createView());
                $this->addTwigVar('TraineeRecordUpdateHomeworksForm', $traineeRecordUpdateHomeworksForm->createView());
                $this->addTwigVar('TraineeRecordUpdateRecordDateForm', $traineeRecordUpdateRecordDateForm->createView());
                $this->addTwigVar('TraineeRecordUpdateTakeawaysForm', $traineeRecordUpdateTakeawaysForm->createView());
                $this->addTwigVar('TraineeRecordUpdateTeacherForm', $traineeRecordUpdateTeacherForm->createView());
                $this->addTwigVar('TraineeRecordUpdateTraineeForm', $traineeRecordUpdateTraineeForm->createView());
                $this->addTwigVar('TraineeRecordUpdateWorksCoveredForm', $traineeRecordUpdateWorksCoveredForm->createView());

                $this->addTwigVar('TraineeRecordDocumentAddForm', $traineeRecordDocumentAddForm->createView());

                $this->addTwigVar('pageTitle', $this->translate('TraineeRecord.pageTitle.admin.edit', array(
                    '%traineeRecord%' => $traineeRecord->getFullName()
                )));
                $this->setHtmlHeadPageTitle($this->translate('TraineeRecord.htmlHeadPageTitle.admin.edit', array(
                    '%traineeRecord%' => $traineeRecord->getFullName()
                )) . ' - ' . $this->getParameter('sitename'));

                return $this->render('IlcfrancePassportstagiaireFrontBundle:TraineeRecord:edit.html.twig', $this->getTwigVars());
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
            $traineeRecord = $em->getRepository('IlcfrancePassportstagiaireDataBundle:TraineeRecord')->find($id);

            if (null == $traineeRecord) {
                $this->addFlash('warning', $this->translate('TraineeRecord.notfound'));
                return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_trainee_list'));
            } else {
                if (!$this->isGranted('ROLE_ADMIN')) {
                    $user = $this->getSecurityTokenStorage()
                        ->getToken()
                        ->getUser();
                    if ($user->getId() != $traineeRecord->getTeacher()->getId()) {
                        $this->addFlash('error', $this->translate('TraineeRecord.edit.failure'));
                        return $this->redirect($urlFrom);
                    }
                }
                $traineeRecordUpdateCommentsForm = $this->createForm(TraineeRecordUpdateCommentsTForm::class, $traineeRecord);
                $traineeRecordUpdateHistoricalForm = $this->createForm(TraineeRecordUpdateHistoricalTForm::class, $traineeRecord, array(
                    'trainee' => $traineeRecord->getTrainee()
                ));
                $traineeRecordUpdateHomeworksForm = $this->createForm(TraineeRecordUpdateHomeworksTForm::class, $traineeRecord);
                $traineeRecordUpdateRecordDateForm = $this->createForm(TraineeRecordUpdateRecordDateTForm::class, $traineeRecord);
                $traineeRecordUpdateTakeawaysForm = $this->createForm(TraineeRecordUpdateTakeawaysTForm::class, $traineeRecord);
                $traineeRecordUpdateTeacherForm = $this->createForm(TraineeRecordUpdateTeacherTForm::class, $traineeRecord);
                $traineeRecordUpdateTraineeForm = $this->createForm(TraineeRecordUpdateTraineeTForm::class, $traineeRecord);
                $traineeRecordUpdateWorksCoveredForm = $this->createForm(TraineeRecordUpdateWorksCoveredTForm::class, $traineeRecord);

                $traineeRecordDocument = new TraineeRecordDocument();
                $traineeRecordDocument->setTraineeRecord($traineeRecord);
                $traineeRecordDocumentAddForm = $this->createForm(TraineeRecordDocumentAddTForm::class, $traineeRecordDocument, array(
                    'traineeRecord' => $traineeRecord
                ));

                $this->addTwigVar('tabActive', $this->getSession()
                    ->get('tabActive', 1));
                $this->getSession()->remove('tabActive');
                $reqData = $request->request->all();

                if (isset($reqData['TraineeRecordUpdateCommentsForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeRecordUpdateCommentsForm->handleRequest($request);
                    if ($traineeRecordUpdateCommentsForm->isValid()) {
                        $em->persist($traineeRecord);
                        $em->flush();

                        $this->addFlash('success', $this->translate('TraineeRecord.edit.success', array(
                            '%traineeRecord%' => $traineeRecord->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($traineeRecord);

                        $this->addFlash('error', $this->translate('TraineeRecord.edit.failure', array(
                            '%traineeRecord%' => $traineeRecord->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeRecordUpdateHistoricalForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeRecordUpdateHistoricalForm->handleRequest($request);
                    if ($traineeRecordUpdateHistoricalForm->isValid()) {
                        $em->persist($traineeRecord);
                        $em->flush();

                        $this->addFlash('success', $this->translate('TraineeRecord.edit.success', array(
                            '%traineeRecord%' => $traineeRecord->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($traineeRecord);

                        $this->addFlash('error', $this->translate('TraineeRecord.edit.failure', array(
                            '%traineeRecord%' => $traineeRecord->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeRecordUpdateHomeworksForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeRecordUpdateHomeworksForm->handleRequest($request);
                    if ($traineeRecordUpdateHomeworksForm->isValid()) {
                        $em->persist($traineeRecord);
                        $em->flush();

                        $this->addFlash('success', $this->translate('TraineeRecord.edit.success', array(
                            '%traineeRecord%' => $traineeRecord->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($traineeRecord);

                        $this->addFlash('error', $this->translate('TraineeRecord.edit.failure', array(
                            '%traineeRecord%' => $traineeRecord->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeRecordUpdateRecordDateForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeRecordUpdateRecordDateForm->handleRequest($request);
                    if ($traineeRecordUpdateRecordDateForm->isValid()) {
                        $em->persist($traineeRecord);
                        $em->flush();

                        $this->addFlash('success', $this->translate('TraineeRecord.edit.success', array(
                            '%traineeRecord%' => $traineeRecord->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($traineeRecord);

                        $this->addFlash('error', $this->translate('TraineeRecord.edit.failure', array(
                            '%traineeRecord%' => $traineeRecord->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeRecordUpdateTakeawaysForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeRecordUpdateTakeawaysForm->handleRequest($request);
                    if ($traineeRecordUpdateTakeawaysForm->isValid()) {
                        $em->persist($traineeRecord);
                        $em->flush();

                        $this->addFlash('success', $this->translate('TraineeRecord.edit.success', array(
                            '%traineeRecord%' => $traineeRecord->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($traineeRecord);

                        $this->addFlash('error', $this->translate('TraineeRecord.edit.failure', array(
                            '%traineeRecord%' => $traineeRecord->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeRecordUpdateTeacherForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeRecordUpdateTeacherForm->handleRequest($request);
                    if ($traineeRecordUpdateTeacherForm->isValid()) {
                        $em->persist($traineeRecord);
                        $em->flush();

                        $this->addFlash('success', $this->translate('TraineeRecord.edit.success', array(
                            '%traineeRecord%' => $traineeRecord->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($traineeRecord);

                        $this->addFlash('error', $this->translate('TraineeRecord.edit.failure', array(
                            '%traineeRecord%' => $traineeRecord->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeRecordUpdateTraineeForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeRecordUpdateTraineeForm->handleRequest($request);
                    if ($traineeRecordUpdateTraineeForm->isValid()) {
                        $em->persist($traineeRecord);
                        $em->flush();

                        $this->addFlash('success', $this->translate('TraineeRecord.edit.success', array(
                            '%traineeRecord%' => $traineeRecord->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($traineeRecord);

                        $this->addFlash('error', $this->translate('TraineeRecord.edit.failure', array(
                            '%traineeRecord%' => $traineeRecord->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeRecordUpdateWorksCoveredForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeRecordUpdateWorksCoveredForm->handleRequest($request);
                    if ($traineeRecordUpdateWorksCoveredForm->isValid()) {
                        $em->persist($traineeRecord);
                        $em->flush();

                        $this->addFlash('success', $this->translate('TraineeRecord.edit.success', array(
                            '%traineeRecord%' => $traineeRecord->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($traineeRecord);

                        $this->addFlash('error', $this->translate('TraineeRecord.edit.failure', array(
                            '%traineeRecord%' => $traineeRecord->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeRecordDocumentAddForm'])) {
                    $this->addTwigVar('tabActive', 3);
                    $this->getSession()->set('tabActive', 3);
                    $traineeRecordDocumentAddForm->handleRequest($request);
                    if ($traineeRecordDocumentAddForm->isValid()) {

                        $traineeRecordDocumentFile = $traineeRecordDocumentAddForm['file']->getData();

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

                        $this->addFlash('success', $this->translate('TraineeRecordDocument.add.success', array(
                            '%traineeRecordDocument%' => $traineeRecordDocument->getOriginalName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($traineeRecord);

                        $this->addFlash('error', $this->translate('Trainee.edit.failure', array(
                            '%traineeRecord%' => $traineeRecord->getFullName()
                        )));
                    }
                }

                $this->addTwigVar('traineeRecord', $traineeRecord);
                $this->addTwigVar('TraineeRecordUpdateCommentsForm', $traineeRecordUpdateCommentsForm->createView());
                $this->addTwigVar('TraineeRecordUpdateHistoricalForm', $traineeRecordUpdateHistoricalForm->createView());
                $this->addTwigVar('TraineeRecordUpdateHomeworksForm', $traineeRecordUpdateHomeworksForm->createView());
                $this->addTwigVar('TraineeRecordUpdateRecordDateForm', $traineeRecordUpdateRecordDateForm->createView());
                $this->addTwigVar('TraineeRecordUpdateTakeawaysForm', $traineeRecordUpdateTakeawaysForm->createView());
                $this->addTwigVar('TraineeRecordUpdateTeacherForm', $traineeRecordUpdateTeacherForm->createView());
                $this->addTwigVar('TraineeRecordUpdateTraineeForm', $traineeRecordUpdateTraineeForm->createView());
                $this->addTwigVar('TraineeRecordUpdateWorksCoveredForm', $traineeRecordUpdateWorksCoveredForm->createView());

                $this->addTwigVar('TraineeRecordDocumentAddForm', $traineeRecordDocumentAddForm->createView());

                $this->addTwigVar('pageTitle', $this->translate('TraineeRecord.pageTitle.admin.edit', array(
                    '%traineeRecord%' => $traineeRecord->getFullName()
                )));
                $this->setHtmlHeadPageTitle($this->translate('TraineeRecord.htmlHeadPageTitle.admin.edit', array(
                    '%traineeRecord%' => $traineeRecord->getFullName()
                )) . ' - ' . $this->getParameter('sitename'));

                return $this->render('IlcfrancePassportstagiaireFrontBundle:TraineeRecord:edit.html.twig', $this->getTwigVars());
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
        }

        return $this->redirect($urlFrom);
    }
}