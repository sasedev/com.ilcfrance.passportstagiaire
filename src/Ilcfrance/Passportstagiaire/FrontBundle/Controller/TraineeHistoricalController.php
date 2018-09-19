<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Controller;

use Ilcfrance\Passportstagiaire\DataBundle\Entity\TraineeHistorical;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\TraineeRecord;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeHistorical\UpdateCoursesTForm as TraineeHistoricalUpdateCoursesTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeHistorical\UpdateInitLevelTForm as TraineeHistoricalUpdateInitLevelTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeHistorical\UpdateLevelTForm as TraineeHistoricalUpdateLevelTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeHistorical\UpdateLockoutTForm as TraineeHistoricalUpdateLockoutTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeHistorical\UpdateNeedsTForm as TraineeHistoricalUpdateNeedsTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeHistorical\UpdateOrigineTForm as TraineeHistoricalUpdateOrigineTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeHistorical\UpdateTraineeTForm as TraineeHistoricalUpdateTraineeTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeHistorical\UpdateYearTForm as TraineeHistoricalUpdateYearTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\AddTForm as TraineeRecordAddTForm;
use Ilcfrance\Passportstagiaire\ResBundle\Controller\IlcfranceController;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
class TraineeHistoricalController extends IlcfranceController
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
            $traineeHistorical = $em->getRepository('IlcfrancePassportstagiaireDataBundle:TraineeHistorical')->find($id);

            if (null == $traineeHistorical) {
                $this->addFlash('warning', $this->translate('TraineeHistorical.notfound'));
            } else {
                if (!$this->isGranted('ROLE_ADMIN')) {
                    $this->addFlash('error', $this->translate('TraineeHistorical.delete.failure'));
                    return $this->redirect($urlFrom);
                }

                $em->remove($traineeHistorical);
                $em->flush();

                $this->addFlash('success', $this->translate('TraineeHistorical.delete.success', array(
                    '%traineeHistorical%' => $traineeHistorical->getFullName()
                )));
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());

            $this->addFlash('error', $this->translate('TraineeHistorical.delete.failure'));
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
            $traineeHistorical = $em->getRepository('IlcfrancePassportstagiaireDataBundle:TraineeHistorical')->find($id);

            if (null == $traineeHistorical) {
                $this->addFlash('warning', $this->translate('TraineeHistorical.notfound'));
                return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_trainee_list'));
            } else {
                $traineeHistoricalUpdateCoursesForm = $this->createForm(TraineeHistoricalUpdateCoursesTForm::class, $traineeHistorical);
                $traineeHistoricalUpdateInitLevelForm = $this->createForm(TraineeHistoricalUpdateInitLevelTForm::class, $traineeHistorical);
                $traineeHistoricalUpdateLevelForm = $this->createForm(TraineeHistoricalUpdateLevelTForm::class, $traineeHistorical);
                $traineeHistoricalUpdateLockoutForm = $this->createForm(TraineeHistoricalUpdateLockoutTForm::class, $traineeHistorical);
                $traineeHistoricalUpdateNeedsForm = $this->createForm(TraineeHistoricalUpdateNeedsTForm::class, $traineeHistorical);
                $traineeHistoricalUpdateOrigineForm = $this->createForm(TraineeHistoricalUpdateOrigineTForm::class, $traineeHistorical);
                $traineeHistoricalUpdateTraineeForm = $this->createForm(TraineeHistoricalUpdateTraineeTForm::class, $traineeHistorical);
                $traineeHistoricalUpdateYearForm = $this->createForm(TraineeHistoricalUpdateYearTForm::class, $traineeHistorical);

                $traineeRecord = new TraineeRecord();
                $teacher = $this->getSecurityTokenStorage()
                    ->getToken()
                    ->getUser();
                $traineeRecord->setTeacher($teacher);
                $traineeRecord->setTeacherName($teacher->getFullName());
                $now = new \DateTime();
                $hour = \date('H');
                $now->setTime($hour, 0, 0);
                $traineeRecord->setRecordDate($now);
                $traineeRecord->setHistorical($traineeHistorical);
                $traineeRecordAddForm = $this->createForm(TraineeRecordAddTForm::class, $traineeRecord, array(
                    'trainee' => $traineeHistorical->getTrainee(),
                    'historical' => $traineeHistorical
                ));

                $this->addTwigVar('tabActive', $this->getSession()
                    ->get('tabActive', 1));
                $this->getSession()->remove('tabActive');

                $this->addTwigVar('traineeHistorical', $traineeHistorical);
                $this->addTwigVar('traineeRecord', $traineeRecord);
                $this->addTwigVar('TraineeHistoricalUpdateCoursesForm', $traineeHistoricalUpdateCoursesForm->createView());
                $this->addTwigVar('TraineeHistoricalUpdateInitLevelForm', $traineeHistoricalUpdateInitLevelForm->createView());
                $this->addTwigVar('TraineeHistoricalUpdateLevelForm', $traineeHistoricalUpdateLevelForm->createView());
                $this->addTwigVar('TraineeHistoricalUpdateLockoutForm', $traineeHistoricalUpdateLockoutForm->createView());
                $this->addTwigVar('TraineeHistoricalUpdateNeedsForm', $traineeHistoricalUpdateNeedsForm->createView());
                $this->addTwigVar('TraineeHistoricalUpdateOrigineForm', $traineeHistoricalUpdateOrigineForm->createView());
                $this->addTwigVar('TraineeHistoricalUpdateTraineeForm', $traineeHistoricalUpdateTraineeForm->createView());
                $this->addTwigVar('TraineeHistoricalUpdateYearForm', $traineeHistoricalUpdateYearForm->createView());
                $this->addTwigVar('TraineeRecordAddForm', $traineeRecordAddForm->createView());

                $this->addTwigVar('pageTitle', $this->translate('TraineeHistorical.pageTitle.admin.edit', array(
                    '%traineeHistorical%' => $traineeHistorical->getFullName()
                )));
                $this->setHtmlHeadPageTitle($this->translate('TraineeHistorical.htmlHeadPageTitle.admin.edit', array(
                    '%traineeHistorical%' => $traineeHistorical->getFullName()
                )) . ' - ' . $this->getParameter('sitename'));

                return $this->render('IlcfrancePassportstagiaireFrontBundle:TraineeHistorical:edit.html.twig', $this->getTwigVars());
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
            $traineeHistorical = $em->getRepository('IlcfrancePassportstagiaireDataBundle:TraineeHistorical')->find($id);

            if (null == $traineeHistorical) {
                $this->addFlash('warning', $this->translate('TraineeHistorical.notfound'));
                return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_trainee_list'));
            } else {
                $traineeHistoricalUpdateCoursesForm = $this->createForm(TraineeHistoricalUpdateCoursesTForm::class, $traineeHistorical);
                $traineeHistoricalUpdateInitLevelForm = $this->createForm(TraineeHistoricalUpdateInitLevelTForm::class, $traineeHistorical);
                $traineeHistoricalUpdateLevelForm = $this->createForm(TraineeHistoricalUpdateLevelTForm::class, $traineeHistorical);
                $traineeHistoricalUpdateLockoutForm = $this->createForm(TraineeHistoricalUpdateLockoutTForm::class, $traineeHistorical);
                $traineeHistoricalUpdateNeedsForm = $this->createForm(TraineeHistoricalUpdateNeedsTForm::class, $traineeHistorical);
                $traineeHistoricalUpdateOrigineForm = $this->createForm(TraineeHistoricalUpdateOrigineTForm::class, $traineeHistorical);
                $traineeHistoricalUpdateTraineeForm = $this->createForm(TraineeHistoricalUpdateTraineeTForm::class, $traineeHistorical);
                $traineeHistoricalUpdateYearForm = $this->createForm(TraineeHistoricalUpdateYearTForm::class, $traineeHistorical);

                $traineeRecord = new TraineeRecord();
                $teacher = $this->getSecurityTokenStorage()
                    ->getToken()
                    ->getUser();
                $traineeRecord->setTeacher($teacher);
                $traineeRecord->setTeacherName($teacher->getFullName());
                $now = new \DateTime();
                $hour = \date('H');
                $now->setTime($hour, 0, 0);
                $traineeRecord->setRecordDate($now);
                $traineeRecord->setHistorical($traineeHistorical);
                $traineeRecordAddForm = $this->createForm(TraineeRecordAddTForm::class, $traineeRecord, array(
                    'trainee' => $traineeHistorical->getTrainee(),
                    'historical' => $traineeHistorical
                ));

                $this->addTwigVar('tabActive', $this->getSession()
                    ->get('tabActive', 1));
                $this->getSession()->remove('tabActive');
                $reqData = $request->request->all();

                if (isset($reqData['TraineeHistoricalUpdateCoursesForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeHistoricalUpdateCoursesForm->handleRequest($request);
                    if ($traineeHistoricalUpdateCoursesForm->isValid()) {
                        $updateTrainee = $traineeHistoricalUpdateCoursesForm['traineeOverride']->getData();
                        if ($updateTrainee == TraineeHistorical::TRAINEE_OVERRIDE_YES) {
                            $trainee = $traineeHistorical->getTrainee();
                            $trainee->setOrigine($traineeHistorical->getOrigine());
                            $trainee->setInitLevel($traineeHistorical->getInitLevel());
                            $trainee->setLevel($traineeHistorical->getLevel());
                            $trainee->setCourses($traineeHistorical->getCourses());
                            $trainee->setNeeds($traineeHistorical->getNeeds());

                            $em->persist($trainee);
                        }
                        $em->persist($traineeHistorical);
                        $em->flush();

                        $this->addFlash('success', $this->translate('TraineeHistorical.edit.success', array(
                            '%traineeHistorical%' => $traineeHistorical->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($traineeHistorical);

                        $this->addFlash('error', $this->translate('TraineeHistorical.edit.failure', array(
                            '%traineeHistorical%' => $traineeHistorical->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeHistoricalUpdateInitLevelForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeHistoricalUpdateInitLevelForm->handleRequest($request);
                    if ($traineeHistoricalUpdateInitLevelForm->isValid()) {
                        $updateTrainee = $traineeHistoricalUpdateInitLevelForm['traineeOverride']->getData();
                        if ($updateTrainee == TraineeHistorical::TRAINEE_OVERRIDE_YES) {
                            $trainee = $traineeHistorical->getTrainee();
                            $trainee->setOrigine($traineeHistorical->getOrigine());
                            $trainee->setInitLevel($traineeHistorical->getInitLevel());
                            $trainee->setLevel($traineeHistorical->getLevel());
                            $trainee->setCourses($traineeHistorical->getCourses());
                            $trainee->setNeeds($traineeHistorical->getNeeds());

                            $em->persist($trainee);
                        }
                        $em->persist($traineeHistorical);
                        $em->flush();

                        $this->addFlash('success', $this->translate('TraineeHistorical.edit.success', array(
                            '%traineeHistorical%' => $traineeHistorical->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($traineeHistorical);

                        $this->addFlash('error', $this->translate('TraineeHistorical.edit.failure', array(
                            '%traineeHistorical%' => $traineeHistorical->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeHistoricalUpdateLevelForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeHistoricalUpdateLevelForm->handleRequest($request);
                    if ($traineeHistoricalUpdateLevelForm->isValid()) {
                        $updateTrainee = $traineeHistoricalUpdateLevelForm['traineeOverride']->getData();
                        if ($updateTrainee == TraineeHistorical::TRAINEE_OVERRIDE_YES) {
                            $trainee = $traineeHistorical->getTrainee();
                            $trainee->setOrigine($traineeHistorical->getOrigine());
                            $trainee->setInitLevel($traineeHistorical->getInitLevel());
                            $trainee->setLevel($traineeHistorical->getLevel());
                            $trainee->setCourses($traineeHistorical->getCourses());
                            $trainee->setNeeds($traineeHistorical->getNeeds());

                            $em->persist($trainee);
                        }
                        $em->persist($traineeHistorical);
                        $em->flush();

                        $this->addFlash('success', $this->translate('TraineeHistorical.edit.success', array(
                            '%traineeHistorical%' => $traineeHistorical->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($traineeHistorical);

                        $this->addFlash('error', $this->translate('TraineeHistorical.edit.failure', array(
                            '%traineeHistorical%' => $traineeHistorical->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeHistoricalUpdateLockoutForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeHistoricalUpdateLockoutForm->handleRequest($request);
                    if ($traineeHistoricalUpdateLockoutForm->isValid()) {
                        $em->persist($traineeHistorical);
                        $em->flush();

                        $this->addFlash('success', $this->translate('TraineeHistorical.edit.success', array(
                            '%traineeHistorical%' => $traineeHistorical->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($traineeHistorical);

                        $this->addFlash('error', $this->translate('TraineeHistorical.edit.failure', array(
                            '%traineeHistorical%' => $traineeHistorical->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeHistoricalUpdateNeedsForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeHistoricalUpdateNeedsForm->handleRequest($request);
                    if ($traineeHistoricalUpdateNeedsForm->isValid()) {
                        $updateTrainee = $traineeHistoricalUpdateNeedsForm['traineeOverride']->getData();
                        if ($updateTrainee == TraineeHistorical::TRAINEE_OVERRIDE_YES) {
                            $trainee = $traineeHistorical->getTrainee();
                            $trainee->setOrigine($traineeHistorical->getOrigine());
                            $trainee->setInitLevel($traineeHistorical->getInitLevel());
                            $trainee->setLevel($traineeHistorical->getLevel());
                            $trainee->setCourses($traineeHistorical->getCourses());
                            $trainee->setNeeds($traineeHistorical->getNeeds());

                            $em->persist($trainee);
                        }
                        $em->persist($traineeHistorical);
                        $em->flush();

                        $this->addFlash('success', $this->translate('TraineeHistorical.edit.success', array(
                            '%traineeHistorical%' => $traineeHistorical->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($traineeHistorical);

                        $this->addFlash('error', $this->translate('TraineeHistorical.edit.failure', array(
                            '%traineeHistorical%' => $traineeHistorical->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeHistoricalUpdateOrigineForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeHistoricalUpdateOrigineForm->handleRequest($request);
                    if ($traineeHistoricalUpdateOrigineForm->isValid()) {
                        $updateTrainee = $traineeHistoricalUpdateOrigineForm['traineeOverride']->getData();
                        if ($updateTrainee == TraineeHistorical::TRAINEE_OVERRIDE_YES) {
                            $trainee = $traineeHistorical->getTrainee();
                            $trainee->setOrigine($traineeHistorical->getOrigine());
                            $trainee->setInitLevel($traineeHistorical->getInitLevel());
                            $trainee->setLevel($traineeHistorical->getLevel());
                            $trainee->setCourses($traineeHistorical->getCourses());
                            $trainee->setNeeds($traineeHistorical->getNeeds());

                            $em->persist($trainee);
                        }
                        $em->persist($traineeHistorical);
                        $em->flush();

                        $this->addFlash('success', $this->translate('TraineeHistorical.edit.success', array(
                            '%traineeHistorical%' => $traineeHistorical->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($traineeHistorical);

                        $this->addFlash('error', $this->translate('TraineeHistorical.edit.failure', array(
                            '%traineeHistorical%' => $traineeHistorical->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeHistoricalUpdateTraineeForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeHistoricalUpdateTraineeForm->handleRequest($request);
                    if ($traineeHistoricalUpdateTraineeForm->isValid()) {
                        $em->persist($traineeHistorical);
                        $em->flush();

                        $this->addFlash('success', $this->translate('TraineeHistorical.edit.success', array(
                            '%traineeHistorical%' => $traineeHistorical->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($traineeHistorical);

                        $this->addFlash('error', $this->translate('TraineeHistorical.edit.failure', array(
                            '%traineeHistorical%' => $traineeHistorical->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeHistoricalUpdateYearForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $traineeHistoricalUpdateYearForm->handleRequest($request);
                    if ($traineeHistoricalUpdateYearForm->isValid()) {
                        $em->persist($traineeHistorical);
                        $em->flush();

                        $this->addFlash('success', $this->translate('TraineeHistorical.edit.success', array(
                            '%traineeHistorical%' => $traineeHistorical->getFullName()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($traineeHistorical);

                        $this->addFlash('error', $this->translate('TraineeHistorical.edit.failure', array(
                            '%traineeHistorical%' => $traineeHistorical->getFullName()
                        )));
                    }
                } elseif (isset($reqData['TraineeRecordAddForm'])) {
                    $this->addTwigVar('tabActive', 3);
                    $this->getSession()->set('tabActive', 3);
                    $traineeRecordAddForm->handleRequest($request);
                    if ($traineeRecordAddForm->isValid()) {
                        $em->persist($traineeRecord);
                        $em->flush();

                        $this->addFlash('success', $this->translate('TraineeRecord.add.success', array(
                            '%traineeRecord%' => $traineeRecord->getFullName()
                        )));
                        $this->addTwigVar('tabActive', 2);
                        $this->getSession()->set('tabActive', 2);

                        return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_trainee_record_editGet', array('id' => $traineeRecord->getId())));
                    } else {
                        $em->refresh($traineeHistorical);

                        $this->addFlash('error', $this->translate('TraineeRecord.add.failure'));
                    }
                }

                $this->addTwigVar('traineeHistorical', $traineeHistorical);
                $this->addTwigVar('traineeRecord', $traineeRecord);
                $this->addTwigVar('TraineeHistoricalUpdateCoursesForm', $traineeHistoricalUpdateCoursesForm->createView());
                $this->addTwigVar('TraineeHistoricalUpdateInitLevelForm', $traineeHistoricalUpdateInitLevelForm->createView());
                $this->addTwigVar('TraineeHistoricalUpdateLevelForm', $traineeHistoricalUpdateLevelForm->createView());
                $this->addTwigVar('TraineeHistoricalUpdateLockoutForm', $traineeHistoricalUpdateLockoutForm->createView());
                $this->addTwigVar('TraineeHistoricalUpdateNeedsForm', $traineeHistoricalUpdateNeedsForm->createView());
                $this->addTwigVar('TraineeHistoricalUpdateOrigineForm', $traineeHistoricalUpdateOrigineForm->createView());
                $this->addTwigVar('TraineeHistoricalUpdateTraineeForm', $traineeHistoricalUpdateTraineeForm->createView());
                $this->addTwigVar('TraineeHistoricalUpdateYearForm', $traineeHistoricalUpdateYearForm->createView());
                $this->addTwigVar('TraineeRecordAddForm', $traineeRecordAddForm->createView());

                $this->addTwigVar('pageTitle', $this->translate('TraineeHistorical.pageTitle.admin.edit', array(
                    '%traineeHistorical%' => $traineeHistorical->getFullName()
                )));
                $this->setHtmlHeadPageTitle($this->translate('TraineeHistorical.htmlHeadPageTitle.admin.edit', array(
                    '%traineeHistorical%' => $traineeHistorical->getFullName()
                )) . ' - ' . $this->getParameter('sitename'));

                return $this->render('IlcfrancePassportstagiaireFrontBundle:TraineeHistorical:edit.html.twig', $this->getTwigVars());
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
        }

        return $this->redirect($urlFrom);
    }
}