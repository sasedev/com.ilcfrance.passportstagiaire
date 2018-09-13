<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Controller;

use Ilcfrance\Passportstagiaire\DataBundle\Entity\TraineeRecordDocument;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\TraineeRecordHomework;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateCommentsTForm as TraineeRecordUpdateCommentsTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateHistoricalTForm as TraineeRecordUpdateHistoricalTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateHomeworksTForm as TraineeRecordUpdateHomeworksTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateRecordDateTForm as TraineeRecordUpdateRecordDateTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateRecordTypeTForm as TraineeRecordUpdateRecordTypeTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateTakeawaysTForm as TraineeRecordUpdateTakeawaysTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateTeacherTForm as TraineeRecordUpdateTeacherTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateTraineeTForm as TraineeRecordUpdateTraineeTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateWorksCoveredTForm as TraineeRecordUpdateWorksCoveredTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateMailCommentsTForm as TraineeRecordUpdateMailCommentsTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateCorrectionVocabulairyTForm as TraineeRecordUpdateCorrectionVocabulairyTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateCorrectionStructureTForm as TraineeRecordUpdateCorrectionStructureTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateCorrectionPrononciationTForm as TraineeRecordUpdateCorrectionPrononciationTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecordDocument\AddTForm as TraineeRecordDocumentAddTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecordHomework\AddTForm as TraineeRecordHomeworkAddTForm;
use Ilcfrance\Passportstagiaire\ResBundle\Controller\IlcfranceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\TraineeRecord;

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
                $traineeRecordUpdateRecordTypeForm = $this->createForm(TraineeRecordUpdateRecordTypeTForm::class, $traineeRecord);
                $traineeRecordUpdateTakeawaysForm = $this->createForm(TraineeRecordUpdateTakeawaysTForm::class, $traineeRecord);
                $traineeRecordUpdateTeacherForm = $this->createForm(TraineeRecordUpdateTeacherTForm::class, $traineeRecord);
                $traineeRecordUpdateTraineeForm = $this->createForm(TraineeRecordUpdateTraineeTForm::class, $traineeRecord);
                $traineeRecordUpdateWorksCoveredForm = $this->createForm(TraineeRecordUpdateWorksCoveredTForm::class, $traineeRecord);
                $traineeRecordUpdateCorrectionVocabulairyForm = $this->createForm(TraineeRecordUpdateCorrectionVocabulairyTForm::class, $traineeRecord);
                $traineeRecordUpdateCorrectionStructureForm = $this->createForm(TraineeRecordUpdateCorrectionStructureTForm::class, $traineeRecord);
                $traineeRecordUpdateCorrectionPrononciationForm = $this->createForm(TraineeRecordUpdateCorrectionPrononciationTForm::class, $traineeRecord);
                $traineeRecordUpdateMailCommentsForm = $this->createForm(TraineeRecordUpdateMailCommentsTForm::class, $traineeRecord);

                $traineeRecordDocument = new TraineeRecordDocument();
                $traineeRecordDocument->setTraineeRecord($traineeRecord);
                $traineeRecordDocumentAddForm = $this->createForm(TraineeRecordDocumentAddTForm::class, $traineeRecordDocument, array(
                    'traineeRecord' => $traineeRecord
                ));
                
                $traineeRecordHomework = new TraineeRecordHomework();
                $traineeRecordHomework->setTraineeRecord($traineeRecord);
                $traineeRecordHomeworkAddForm = $this->createForm(TraineeRecordHomeworkAddTForm::class, $traineeRecordHomework, array(
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
                $this->addTwigVar('TraineeRecordUpdateRecordTypeForm', $traineeRecordUpdateRecordTypeForm->createView());
                $this->addTwigVar('TraineeRecordUpdateTakeawaysForm', $traineeRecordUpdateTakeawaysForm->createView());
                $this->addTwigVar('TraineeRecordUpdateTeacherForm', $traineeRecordUpdateTeacherForm->createView());
                $this->addTwigVar('TraineeRecordUpdateTraineeForm', $traineeRecordUpdateTraineeForm->createView());
                $this->addTwigVar('TraineeRecordUpdateWorksCoveredForm', $traineeRecordUpdateWorksCoveredForm->createView());
                $this->addTwigVar('TraineeRecordUpdateCorrectionVocabulairyForm', $traineeRecordUpdateCorrectionVocabulairyForm->createView());
                $this->addTwigVar('TraineeRecordUpdateCorrectionStructureForm', $traineeRecordUpdateCorrectionStructureForm->createView());
                $this->addTwigVar('TraineeRecordUpdateCorrectionPrononciationForm', $traineeRecordUpdateCorrectionPrononciationForm->createView());
                $this->addTwigVar('TraineeRecordUpdateMailCommentsForm', $traineeRecordUpdateMailCommentsForm->createView());

                $this->addTwigVar('TraineeRecordDocumentAddForm', $traineeRecordDocumentAddForm->createView());
                $this->addTwigVar('TraineeRecordHomeworkAddForm', $traineeRecordHomeworkAddForm->createView());

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
                $traineeRecordUpdateRecordTypeForm = $this->createForm(TraineeRecordUpdateRecordTypeTForm::class, $traineeRecord);
                $traineeRecordUpdateTakeawaysForm = $this->createForm(TraineeRecordUpdateTakeawaysTForm::class, $traineeRecord);
                $traineeRecordUpdateTeacherForm = $this->createForm(TraineeRecordUpdateTeacherTForm::class, $traineeRecord);
                $traineeRecordUpdateTraineeForm = $this->createForm(TraineeRecordUpdateTraineeTForm::class, $traineeRecord);
                $traineeRecordUpdateWorksCoveredForm = $this->createForm(TraineeRecordUpdateWorksCoveredTForm::class, $traineeRecord);
                $traineeRecordUpdateCorrectionVocabulairyForm = $this->createForm(TraineeRecordUpdateCorrectionVocabulairyTForm::class, $traineeRecord);
                $traineeRecordUpdateCorrectionStructureForm = $this->createForm(TraineeRecordUpdateCorrectionStructureTForm::class, $traineeRecord);
                $traineeRecordUpdateCorrectionPrononciationForm = $this->createForm(TraineeRecordUpdateCorrectionPrononciationTForm::class, $traineeRecord);
                $traineeRecordUpdateMailCommentsForm = $this->createForm(TraineeRecordUpdateMailCommentsTForm::class, $traineeRecord);

                $traineeRecordDocument = new TraineeRecordDocument();
                $traineeRecordDocument->setTraineeRecord($traineeRecord);
                $traineeRecordDocumentAddForm = $this->createForm(TraineeRecordDocumentAddTForm::class, $traineeRecordDocument, array(
                    'traineeRecord' => $traineeRecord
                ));
                
                $traineeRecordHomework = new TraineeRecordHomework();
                $traineeRecordHomework->setTraineeRecord($traineeRecord);
                $traineeRecordHomeworkAddForm = $this->createForm(TraineeRecordHomeworkAddTForm::class, $traineeRecordHomework, array(
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
                } elseif (isset($reqData['TraineeRecordUpdateCorrectionVocabulairyForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeRecordUpdateCorrectionVocabulairyForm->handleRequest($request);
                    if ($traineeRecordUpdateCorrectionVocabulairyForm->isValid()) {
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
                } elseif (isset($reqData['TraineeRecordUpdateCorrectionStructureForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeRecordUpdateCorrectionStructureForm->handleRequest($request);
                    if ($traineeRecordUpdateCorrectionStructureForm->isValid()) {
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
                } elseif (isset($reqData['TraineeRecordUpdateCorrectionPrononciationForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeRecordUpdateCorrectionPrononciationForm->handleRequest($request);
                    if ($traineeRecordUpdateCorrectionPrononciationForm->isValid()) {
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
                } elseif (isset($reqData['TraineeRecordUpdateMailCommentsForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeRecordUpdateMailCommentsForm->handleRequest($request);
                    if ($traineeRecordUpdateMailCommentsForm->isValid()) {
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
                } elseif (isset($reqData['TraineeRecordUpdateRecordTypeForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeRecordUpdateRecordTypeForm->handleRequest($request);
                    if ($traineeRecordUpdateRecordTypeForm->isValid()) {
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
                } elseif (isset($reqData['TraineeRecordHomeworkAddForm'])) {
                    $this->addTwigVar('tabActive', 4);
                    $this->getSession()->set('tabActive', 4);
                    $traineeRecordHomeworkAddForm->handleRequest($request);
                    if ($traineeRecordHomeworkAddForm->isValid()) {
                        
                        $em->persist($traineeRecordHomework);
                        $em->flush();
                        
                        $this->addFlash('success', $this->translate('TraineeRecordHomework.add.success', array(
                            '%traineeRecordHomework%' => $traineeRecordHomework->getHomework()->getOriginalName()
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
                $this->addTwigVar('TraineeRecordUpdateRecordTypeForm', $traineeRecordUpdateRecordTypeForm->createView());
                $this->addTwigVar('TraineeRecordUpdateTakeawaysForm', $traineeRecordUpdateTakeawaysForm->createView());
                $this->addTwigVar('TraineeRecordUpdateTeacherForm', $traineeRecordUpdateTeacherForm->createView());
                $this->addTwigVar('TraineeRecordUpdateTraineeForm', $traineeRecordUpdateTraineeForm->createView());
                $this->addTwigVar('TraineeRecordUpdateWorksCoveredForm', $traineeRecordUpdateWorksCoveredForm->createView());
                $this->addTwigVar('TraineeRecordUpdateCorrectionVocabulairyForm', $traineeRecordUpdateCorrectionVocabulairyForm->createView());
                $this->addTwigVar('TraineeRecordUpdateCorrectionStructureForm', $traineeRecordUpdateCorrectionStructureForm->createView());
                $this->addTwigVar('TraineeRecordUpdateCorrectionPrononciationForm', $traineeRecordUpdateCorrectionPrononciationForm->createView());
                $this->addTwigVar('TraineeRecordUpdateMailCommentsForm', $traineeRecordUpdateMailCommentsForm->createView());

                $this->addTwigVar('TraineeRecordDocumentAddForm', $traineeRecordDocumentAddForm->createView());
                $this->addTwigVar('TraineeRecordHomeworkAddForm', $traineeRecordHomeworkAddForm->createView());

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
    
    public function sendmailAction($id, Request $request) {
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
                $user = $this->getSecurityTokenStorage()
                ->getToken()
                ->getUser();
                if (!$this->isGranted('ROLE_ADMIN')) {
                    if ($user->getId() != $traineeRecord->getTeacher()->getId()) {
                        $this->addFlash('error', $this->translate('TraineeRecord.sendmail.notfound'));
                        return $this->redirect($urlFrom);
                    }
                }
                if($traineeRecord->getRecordType() != TraineeRecord::RT_PHONE) {
                    $this->addFlash('error', $this->translate('TraineeRecord.sendmail.notfound'));
                    return $this->redirect($urlFrom);
                }
                $email = $user->getEmail();
                if (null == $email || \trim($email) == '') {
                    $this->addFlash('warning', $this->translate('TraineeRecord.sendmail.notemail'));
                    return $this->redirect($urlFrom);
                }
                
                $this->addTwigVar('traineeRecord', $traineeRecord);
                
                $this->addTwigVar('pageTitle', $this->translate('TraineeRecord.pageTitle.admin.edit', array(
                    '%traineeRecord%' => $traineeRecord->getFullName()
                )));
                $this->setHtmlHeadPageTitle($this->translate('TraineeRecord.htmlHeadPageTitle.admin.edit', array(
                    '%traineeRecord%' => $traineeRecord->getFullName()
                )) . ' - ' . $this->getParameter('sitename'));
                
                //return $this->render('IlcfrancePassportstagiaireFrontBundle:TraineeRecord:phonecorrection.html.twig', $this->getTwigVars());
                /*
                return new \TFox\MpdfPortBundle\Response\PDFResponse($this->get('t_fox_mpdf_port.pdf')->generatePdf($this->renderView('IlcfrancePassportstagiaireFrontBundle:TraineeRecord:phonecorrection.html.twig', $this->getTwigVars()), array(
                    'format' => 'A4' // A4 page, landscape orientation
                )));
                //*/
                
                
                $correction = $this->get('t_fox_mpdf_port.pdf')->generatePdf($this->renderView('IlcfrancePassportstagiaireFrontBundle:TraineeRecord:phonecorrection.html.twig', $this->getTwigVars()), array(
                    'format' => 'A4' // A4 page, landscape orientation
                ));
                $correctionfname = $this->normalize($this->translate('TraineeRecord.htmlHeadPageTitle.admin.edit', array(
                    '%traineeRecord%' => $traineeRecord->getRecordDate()->format('Y-m-d') . ' ' . $traineeRecord->getRecordDate()->format('H:i:s') . ' '.$traineeRecord->getTrainee()->getFullName()
                )));
                
                $correctionattachment = new \Swift_Attachment($correction, $correctionfname.'.pdf', 'application/pdf');
                
                $user = $traineeRecord->getTrainee();
                $mvars = array();
                $mvars['user'] = $user;
                $mvars['traineeRecord'] = $traineeRecord;
                
                $from = $this->getParameter('mail_from');
                $fromName = $this->getParameter('mail_from_name');
                $subject = $this->translate('_mail.TraineeRecord_subject', array());
                $message = \Swift_Message::newInstance();
                
                $message->setFrom($from, $fromName)
                ->setTo($user->getEmail(), $user->getFullname())
                ->setSubject($subject)
                ->setBody($this->renderView('IlcfrancePassportstagiaireFrontBundle:TraineeRecord:sendmail.html.twig', $mvars), 'text/html');
                
                $message->attach($correctionattachment);
                
                foreach ($traineeRecord->getHws() as $hw) {
                    
                    $fileName = $hw->getHomework()->getFileName();
                    $hwDir = $this->getParameter('kernel.root_dir') . '/../web/res/homeworks';
                    
                    $dlFile = new File($hwDir . '/' . $fileName);
                    $hwFilename = $this->normalize($hw->getHomework()->getOriginalName());
                    
                    $hwAttachement = \Swift_Attachment::fromPath($dlFile->getRealPath());
                    $hwAttachement->setFilename($hwFilename);
                    $hwAttachement->setContentType($hw->getHomework()->getMimeType());
                    $hwAttachement->setDisposition('attachement');
                    
                    $message->attach($hwAttachement);
                }
                
                $this->sendmail($message);
                
                $traineeRecord->setNbrEmails($traineeRecord->getNbrEmails() + 1);
                $em->persist($traineeRecord);
                $em->flush();
                $this->addFlash('success', $this->translate('TraineeRecord.sendmail.success'));
                
                
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
        }
        
        return $this->redirect($urlFrom);
    }
}