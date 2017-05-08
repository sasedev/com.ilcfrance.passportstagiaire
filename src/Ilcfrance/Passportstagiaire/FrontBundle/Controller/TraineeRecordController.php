<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Controller;

use Ilcfrance\Passportstagiaire\ResBundle\Controller\IlcfranceController;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateCommentsTForm as TraineeRecordUpdateCommentsTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateHomeworksTForm as TraineeRecordUpdateHomeworksTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateRecordDateTForm as TraineeRecordUpdateRecordDateTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateTakeawaysTForm as TraineeRecordUpdateTakeawaysTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord\UpdateWorksCoveredTForm as TraineeRecordUpdateWorksCoveredTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecordDocument\AddTForm as TraineeRecordDocumentAddTForm;
use Symfony\Component\HttpFoundation\Request;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\Trainee;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\TraineeRecord;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\TraineeRecordDocument;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
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
			$traineeRecord = $em->getRepository('IlcfrancePassportstagiaireDataBundle:TraineeRecord')->getOneById($id);

			if (null == $traineeRecord) {
				$this->addFlash('warning', $this->translate('TraineeRecord.notfound'));
			} else {
				if (!$this->isGranted('ROLE_ADMIN')) {
					$user = $this->getSecurityTokenStorage()->getToken()->getUser();
					if ($user->getId() != $traineeRecord->getTeacher()->getId()) {
						$this->addFlash('error', $this->translate('TraineeRecord.delete.failure'));
						return $this->redirect($urlFrom);
					}
				}

				$em->remove($traineeRecord);
				$em->flush();

				$cacheDriver = $em->getConfiguration()->getResultCacheImpl();
				$cacheDriver->delete('Trainee_getAllQuery');

				$cacheId = 'Trainee_getOneByIdQuery' . $traineeRecord->getTrainee()->getId();
				$cacheDriver->delete($cacheId);

				$classname = \str_replace('\\', '.', \strtolower(Trainee::class));

				$cacheId = 'region_Trainee_' . $classname . '_' . $traineeRecord->getTrainee()->getId();
				$cacheDriver->delete($cacheId);
				$cacheId = 'region_Trainee_records_' . $classname . '_' . $traineeRecord->getTrainee()->getId() . '__records';
				$cacheDriver->delete($cacheId);

				$this->addFlash('success', $this->translate('TraineeRecord.delete.success', array(
					'%trainee%' => $traineeRecord->getTrainee()->getFullName(),
					'%dtStart%' => $traineeRecord->getRecordDate()->format('Y-m-d'),
					'%hStart%' => $traineeRecord->getRecordDate()->format('H:i:s')
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
			$traineeRecord = $em->getRepository('IlcfrancePassportstagiaireDataBundle:TraineeRecord')->getOneById($id);

			if (null == $traineeRecord) {
				$this->addFlash('warning', $this->translate('TraineeRecord.notfound'));
			} else {
				if (!$this->isGranted('ROLE_ADMIN')) {
					$user = $this->getSecurityTokenStorage()->getToken()->getUser();
					if ($user->getId() != $traineeRecord->getTeacher()->getId()) {
						$this->addFlash('error', $this->translate('TraineeRecord.edit.failure'));
						return $this->redirect($urlFrom);
					}
				}
				$traineeRecordUpdateCommentsForm = $this->createForm(TraineeRecordUpdateCommentsTForm::class, $traineeRecord);
				$traineeRecordUpdateHomeworksForm = $this->createForm(TraineeRecordUpdateHomeworksTForm::class, $traineeRecord);
				$traineeRecordUpdateRecordDateForm = $this->createForm(TraineeRecordUpdateRecordDateTForm::class, $traineeRecord);
				$traineeRecordUpdateWorksCoveredForm = $this->createForm(TraineeRecordUpdateWorksCoveredTForm::class, $traineeRecord);
				$traineeRecordUpdateTakeawaysForm = $this->createForm(TraineeRecordUpdateTakeawaysTForm::class, $traineeRecord);

				$traineeRecordDocument = new TraineeRecordDocument();
				$traineeRecordDocument->setTraineeRecord($traineeRecord);
				$traineeRecordDocumentAddForm = $this->createForm(TraineeRecordDocumentAddTForm::class, $traineeRecordDocument);

				$this->addTwigVar('tabActive', $this->getSession()->get('tabActive', 1));
				$this->getSession()->remove('tabActive');

				$this->addTwigVar('traineeRecord', $traineeRecord);
				$this->addTwigVar('TraineeRecordUpdateCommentsForm', $traineeRecordUpdateCommentsForm->createView());
				$this->addTwigVar('TraineeRecordUpdateHomeworksForm', $traineeRecordUpdateHomeworksForm->createView());
				$this->addTwigVar('TraineeRecordUpdateRecordDateForm', $traineeRecordUpdateRecordDateForm->createView());
				$this->addTwigVar('TraineeRecordUpdateWorksCoveredForm', $traineeRecordUpdateWorksCoveredForm->createView());
				$this->addTwigVar('TraineeRecordUpdateTakeawaysForm', $traineeRecordUpdateTakeawaysForm->createView());
				$this->addTwigVar('TraineeRecordDocumentAddForm', $traineeRecordDocumentAddForm->createView());

				$this->addTwigVar('pageTitle', $this->translate('TraineeRecord.pageTitle.admin.edit', array(
					'%trainee%' => $traineeRecord->getTrainee()->getFullName(),
					'%dtStart%' => $traineeRecord->getRecordDate()->format('Y-m-d'),
					'%hStart%' => $traineeRecord->getRecordDate()->format('H:i:s')
				)));
				$this->setHtmlHeadPageTitle($this->translate('TraineeRecord.htmlHeadPageTitle.admin.edit', array(
					'%trainee%' => $traineeRecord->getTrainee()->getFullName(),
					'%dtStart%' => $traineeRecord->getRecordDate()->format('Y-m-d'),
					'%hStart%' => $traineeRecord->getRecordDate()->format('H:i:s')
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
			$traineeRecord = $em->getRepository('IlcfrancePassportstagiaireDataBundle:TraineeRecord')->getOneById($id);

			if (null == $traineeRecord) {
				$this->addFlash('warning', $this->translate('TraineeRecord.notfound'));
			} else {
				if (!$this->isGranted('ROLE_ADMIN')) {
					$user = $this->getSecurityTokenStorage()->getToken()->getUser();
					if ($user->getId() != $traineeRecord->getTeacher()->getId()) {
						$this->addFlash('error', $this->translate('TraineeRecord.edit.failure'));
						return $this->redirect($urlFrom);
					}
				}
				$traineeRecordUpdateCommentsForm = $this->createForm(TraineeRecordUpdateCommentsTForm::class, $traineeRecord);
				$traineeRecordUpdateHomeworksForm = $this->createForm(TraineeRecordUpdateHomeworksTForm::class, $traineeRecord);
				$traineeRecordUpdateRecordDateForm = $this->createForm(TraineeRecordUpdateRecordDateTForm::class, $traineeRecord);
				$traineeRecordUpdateWorksCoveredForm = $this->createForm(TraineeRecordUpdateWorksCoveredTForm::class, $traineeRecord);
				$traineeRecordUpdateTakeawaysForm = $this->createForm(TraineeRecordUpdateTakeawaysTForm::class, $traineeRecord);

				$traineeRecordDocument = new TraineeRecordDocument();
				$traineeRecordDocument->setTraineeRecord($traineeRecord);
				$traineeRecordDocumentAddForm = $this->createForm(TraineeRecordDocumentAddTForm::class, $traineeRecordDocument);

				$this->addTwigVar('tabActive', $this->getSession()->get('tabActive', 1));
				$this->getSession()->remove('tabActive');
				$reqData = $request->request->all();

				if (isset($reqData['TraineeRecordUpdateCommentsForm'])) {
					$this->addTwigVar('tabActive', 2);
					$this->getSession()->set('tabActive', 2);
					$traineeRecordUpdateCommentsForm->handleRequest($request);
					if ($traineeRecordUpdateCommentsForm->isValid()) {
						$em->persist($traineeRecord);
						$em->flush();

						$cacheDriver = $em->getConfiguration()->getResultCacheImpl();
						$cacheDriver->delete('Trainee_getAllQuery');

						$cacheId = 'Trainee_getOneByIdQuery' . $traineeRecord->getTrainee()->getId();
						$cacheDriver->delete($cacheId);

						$cacheId = 'TraineeRecord_getOneByIdQuery' . $id;
						$cacheDriver->delete($cacheId);

						$classname = \str_replace('\\', '.', \strtolower(Trainee::class));

						$cacheId = 'region_Trainee_' . $classname . '_' . $traineeRecord->getTrainee()->getId();
						$cacheDriver->delete($cacheId);
						$cacheId = 'region_Trainee_records_' . $classname . '_' . $traineeRecord->getTrainee()->getId() . '__records';
						$cacheDriver->delete($cacheId);
						$classname = \str_replace('\\', '.', \strtolower(TraineeRecord::class));

						$cacheId = 'region_TraineeRecord_' . $classname . '_' . $id;
						$cacheDriver->delete($cacheId);

						$this->addFlash('success', $this->translate('Trainee.edit.success', array(
							'%trainee%' => $traineeRecord->getTrainee()->getFullName(),
							'%dtStart%' => $traineeRecord->getRecordDate()->format('Y-m-d'),
							'%hStart%' => $traineeRecord->getRecordDate()->format('H:i:s')
						)));

						return $this->redirect($urlFrom);
					} else {
						$em->refresh($traineeRecord);

						$this->addFlash('error', $this->translate('Trainee.edit.failure', array(
							'%trainee%' => $traineeRecord->getTrainee()->getFullName(),
							'%dtStart%' => $traineeRecord->getRecordDate()->format('Y-m-d'),
							'%hStart%' => $traineeRecord->getRecordDate()->format('H:i:s')
						)));
					}
				} elseif (isset($reqData['TraineeRecordUpdateHomeworksForm'])) {
					$this->addTwigVar('tabActive', 2);
					$this->getSession()->set('tabActive', 2);
					$traineeRecordUpdateHomeworksForm->handleRequest($request);
					if ($traineeRecordUpdateHomeworksForm->isValid()) {
						$em->persist($traineeRecord);
						$em->flush();

						$cacheDriver = $em->getConfiguration()->getResultCacheImpl();
						$cacheDriver->delete('Trainee_getAllQuery');

						$cacheId = 'Trainee_getOneByIdQuery' . $traineeRecord->getTrainee()->getId();
						$cacheDriver->delete($cacheId);

						$cacheId = 'TraineeRecord_getOneByIdQuery' . $id;
						$cacheDriver->delete($cacheId);

						$classname = \str_replace('\\', '.', \strtolower(Trainee::class));

						$cacheId = 'region_Trainee_' . $classname . '_' . $traineeRecord->getTrainee()->getId();
						$cacheDriver->delete($cacheId);

						$cacheId = 'region_Trainee_records_' . $classname . '_' . $traineeRecord->getTrainee()->getId() . '__records';
						$cacheDriver->delete($cacheId);
						$classname = \str_replace('\\', '.', \strtolower(TraineeRecord::class));

						$cacheId = 'region_TraineeRecord_' . $classname . '_' . $id;
						$cacheDriver->delete($cacheId);

						$this->addFlash('success', $this->translate('Trainee.edit.success', array(
							'%trainee%' => $traineeRecord->getTrainee()->getFullName(),
							'%dtStart%' => $traineeRecord->getRecordDate()->format('Y-m-d'),
							'%hStart%' => $traineeRecord->getRecordDate()->format('H:i:s')
						)));

						return $this->redirect($urlFrom);
					} else {
						$em->refresh($traineeRecord);

						$this->addFlash('error', $this->translate('Trainee.edit.failure', array(
							'%trainee%' => $traineeRecord->getTrainee()->getFullName(),
							'%dtStart%' => $traineeRecord->getRecordDate()->format('Y-m-d'),
							'%hStart%' => $traineeRecord->getRecordDate()->format('H:i:s')
						)));
					}
				} elseif (isset($reqData['TraineeRecordUpdateRecordDateForm'])) {
					$this->addTwigVar('tabActive', 2);
					$this->getSession()->set('tabActive', 2);
					$traineeRecordUpdateRecordDateForm->handleRequest($request);
					if ($traineeRecordUpdateRecordDateForm->isValid()) {
						$em->persist($traineeRecord);
						$em->flush();

						$cacheDriver = $em->getConfiguration()->getResultCacheImpl();
						$cacheDriver->delete('Trainee_getAllQuery');

						$cacheId = 'Trainee_getOneByIdQuery' . $traineeRecord->getTrainee()->getId();
						$cacheDriver->delete($cacheId);

						$cacheId = 'TraineeRecord_getOneByIdQuery' . $id;
						$cacheDriver->delete($cacheId);

						$classname = \str_replace('\\', '.', \strtolower(Trainee::class));

						$cacheId = 'region_Trainee_' . $classname . '_' . $traineeRecord->getTrainee()->getId();
						$cacheDriver->delete($cacheId);
						$cacheId = 'region_Trainee_records_' . $classname . '_' . $traineeRecord->getTrainee()->getId() . '__records';
						$cacheDriver->delete($cacheId);
						$classname = \str_replace('\\', '.', \strtolower(TraineeRecord::class));

						$cacheId = 'region_TraineeRecord_' . $classname . '_' . $id;
						$cacheDriver->delete($cacheId);

						$this->addFlash('success', $this->translate('Trainee.edit.success', array(
							'%trainee%' => $traineeRecord->getTrainee()->getFullName(),
							'%dtStart%' => $traineeRecord->getRecordDate()->format('Y-m-d'),
							'%hStart%' => $traineeRecord->getRecordDate()->format('H:i:s')
						)));

						return $this->redirect($urlFrom);
					} else {
						$em->refresh($traineeRecord);

						$this->addFlash('error', $this->translate('Trainee.edit.failure', array(
							'%trainee%' => $traineeRecord->getTrainee()->getFullName(),
							'%dtStart%' => $traineeRecord->getRecordDate()->format('Y-m-d'),
							'%hStart%' => $traineeRecord->getRecordDate()->format('H:i:s')
						)));
					}
				} elseif (isset($reqData['TraineeRecordUpdateWorksCoveredForm'])) {
					$this->addTwigVar('tabActive', 2);
					$this->getSession()->set('tabActive', 2);
					$traineeRecordUpdateWorksCoveredForm->handleRequest($request);
					if ($traineeRecordUpdateWorksCoveredForm->isValid()) {
						$em->persist($traineeRecord);
						$em->flush();

						$cacheDriver = $em->getConfiguration()->getResultCacheImpl();
						$cacheDriver->delete('Trainee_getAllQuery');

						$cacheId = 'Trainee_getOneByIdQuery' . $traineeRecord->getTrainee()->getId();
						$cacheDriver->delete($cacheId);

						$cacheId = 'TraineeRecord_getOneByIdQuery' . $id;
						$cacheDriver->delete($cacheId);

						$classname = \str_replace('\\', '.', \strtolower(Trainee::class));

						$cacheId = 'region_Trainee_' . $classname . '_' . $traineeRecord->getTrainee()->getId();
						$cacheDriver->delete($cacheId);
						$cacheId = 'region_Trainee_records_' . $classname . '_' . $traineeRecord->getTrainee()->getId() . '__records';
						$cacheDriver->delete($cacheId);
						$classname = \str_replace('\\', '.', \strtolower(TraineeRecord::class));

						$cacheId = 'region_TraineeRecord_' . $classname . '_' . $id;
						$cacheDriver->delete($cacheId);

						$this->addFlash('success', $this->translate('Trainee.edit.success', array(
							'%trainee%' => $traineeRecord->getTrainee()->getFullName(),
							'%dtStart%' => $traineeRecord->getRecordDate()->format('Y-m-d'),
							'%hStart%' => $traineeRecord->getRecordDate()->format('H:i:s')
						)));

						return $this->redirect($urlFrom);
					} else {
						$em->refresh($traineeRecord);

						$this->addFlash('error', $this->translate('Trainee.edit.failure', array(
							'%trainee%' => $traineeRecord->getTrainee()->getFullName(),
							'%dtStart%' => $traineeRecord->getRecordDate()->format('Y-m-d'),
							'%hStart%' => $traineeRecord->getRecordDate()->format('H:i:s')
						)));
					}
				} elseif (isset($reqData['TraineeRecordUpdateTakeawaysForm'])) {
					$this->addTwigVar('tabActive', 2);
					$this->getSession()->set('tabActive', 2);
					$traineeRecordUpdateTakeawaysForm->handleRequest($request);
					if ($traineeRecordUpdateTakeawaysForm->isValid()) {
						$em->persist($traineeRecord);
						$em->flush();

						$cacheDriver = $em->getConfiguration()->getResultCacheImpl();
						$cacheDriver->delete('Trainee_getAllQuery');

						$cacheId = 'Trainee_getOneByIdQuery' . $traineeRecord->getTrainee()->getId();
						$cacheDriver->delete($cacheId);

						$cacheId = 'TraineeRecord_getOneByIdQuery' . $id;
						$cacheDriver->delete($cacheId);

						$classname = \str_replace('\\', '.', \strtolower(Trainee::class));

						$cacheId = 'region_Trainee_' . $classname . '_' . $traineeRecord->getTrainee()->getId();
						$cacheDriver->delete($cacheId);
						$cacheId = 'region_Trainee_records_' . $classname . '_' . $traineeRecord->getTrainee()->getId() . '__records';
						$cacheDriver->delete($cacheId);
						$classname = \str_replace('\\', '.', \strtolower(TraineeRecord::class));

						$cacheId = 'region_TraineeRecord_' . $classname . '_' . $id;
						$cacheDriver->delete($cacheId);

						$this->addFlash('success', $this->translate('Trainee.edit.success', array(
							'%trainee%' => $traineeRecord->getTrainee()->getFullName(),
							'%dtStart%' => $traineeRecord->getRecordDate()->format('Y-m-d'),
							'%hStart%' => $traineeRecord->getRecordDate()->format('H:i:s')
						)));

						return $this->redirect($urlFrom);
					} else {
						$em->refresh($traineeRecord);

						$this->addFlash('error', $this->translate('Trainee.edit.failure', array(
							'%trainee%' => $traineeRecord->getTrainee()->getFullName(),
							'%dtStart%' => $traineeRecord->getRecordDate()->format('Y-m-d'),
							'%hStart%' => $traineeRecord->getRecordDate()->format('H:i:s')
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

						$cacheDriver = $em->getConfiguration()->getResultCacheImpl();
						$cacheDriver->delete('Trainee_getAllQuery');

						$cacheId = 'Trainee_getOneByIdQuery' . $traineeRecord->getTrainee()->getId();
						$cacheDriver->delete($cacheId);

						$cacheId = 'TraineeRecord_getOneByIdQuery' . $id;
						$cacheDriver->delete($cacheId);

						$classname = \str_replace('\\', '.', \strtolower(Trainee::class));

						$cacheId = 'region_Trainee_' . $classname . '_' . $traineeRecord->getTrainee()->getId();
						$cacheDriver->delete($cacheId);
						$cacheId = 'region_Trainee_records_' . $classname . '_' . $traineeRecord->getTrainee()->getId() . '__records';
						$cacheDriver->delete($cacheId);

						$classname = \str_replace('\\', '.', \strtolower(TraineeRecord::class));
						$cacheId = 'region_TraineeRecord_' . $classname . '_' . $id;
						$cacheDriver->delete($cacheId);

						$cacheId = 'region_TraineeRecord_docs_' . $classname . '_' . $id . '__docs';
						$cacheDriver->delete($cacheId);

						$this->addFlash('success', $this->translate('TraineeRecordDocument.add.success', array(
							'%traineeRecordDocument%' => $traineeRecordDocument->getOriginalName()
						)));

						return $this->redirect($urlFrom);
					} else {
						$em->refresh($traineeRecord);

						$this->addFlash('error', $this->translate('Trainee.edit.failure', array(
							'%trainee%' => $traineeRecord->getTrainee()->getFullName(),
							'%dtStart%' => $traineeRecord->getRecordDate()->format('Y-m-d'),
							'%hStart%' => $traineeRecord->getRecordDate()->format('H:i:s')
						)));
					}
				}

				$this->addTwigVar('traineeRecord', $traineeRecord);
				$this->addTwigVar('TraineeRecordUpdateCommentsForm', $traineeRecordUpdateCommentsForm->createView());
				$this->addTwigVar('TraineeRecordUpdateHomeworksForm', $traineeRecordUpdateHomeworksForm->createView());
				$this->addTwigVar('TraineeRecordUpdateRecordDateForm', $traineeRecordUpdateRecordDateForm->createView());
				$this->addTwigVar('TraineeRecordUpdateWorksCoveredForm', $traineeRecordUpdateWorksCoveredForm->createView());
				$this->addTwigVar('TraineeRecordUpdateTakeawaysForm', $traineeRecordUpdateTakeawaysForm->createView());
				$this->addTwigVar('TraineeRecordDocumentAddForm', $traineeRecordDocumentAddForm->createView());

				$this->addTwigVar('pageTitle', $this->translate('TraineeRecord.pageTitle.admin.edit', array(
					'%trainee%' => $traineeRecord->getTrainee()->getFullName(),
					'%dtStart%' => $traineeRecord->getRecordDate()->format('Y-m-d'),
					'%hStart%' => $traineeRecord->getRecordDate()->format('H:i:s')
				)));
				$this->setHtmlHeadPageTitle($this->translate('TraineeRecord.htmlHeadPageTitle.admin.edit', array(
					'%trainee%' => $traineeRecord->getTrainee()->getFullName(),
					'%dtStart%' => $traineeRecord->getRecordDate()->format('Y-m-d'),
					'%hStart%' => $traineeRecord->getRecordDate()->format('H:i:s')
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