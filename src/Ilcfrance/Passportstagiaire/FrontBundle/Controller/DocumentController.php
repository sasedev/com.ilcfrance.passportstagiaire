<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Controller;

use Ilcfrance\Passportstagiaire\FrontBundle\Form\Document\AddTForm as DocumentAddTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Document\UpdateContentTForm as DocumentUpdateContentTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Document\UpdateDescriptionTForm as DocumentUpdateDescriptionTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Document\UpdateOriginalNameTForm as DocumentUpdateOriginalNameTForm;
use Ilcfrance\Passportstagiaire\ResBundle\Controller\IlcfranceController;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\Document;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class DocumentController extends IlcfranceController
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->addTwigVar('menu_active', 'admin');
		$this->addTwigVar('admmenu_active', 'documents');
	}

	/**
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function listAction(Request $request)
	{
		$em = $this->getEntityManager();
		$documents = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Document')->getAll();
		$this->addTwigVar('documents', $documents);

		$this->addTwigVar('admmenu_active', 'documents_list');
		$this->addTwigVar('pageTitle', $this->translate('Document.pageTitle.admin.list'));
		$this->setHtmlHeadPageTitle($this->translate('Document.htmlHeadPageTitle.admin.list') . ' - ' . $this->getParameter('sitename'));
		return $this->render('IlcfrancePassportstagiaireFrontBundle:Document:list.html.twig', $this->getTwigVars());
	}

	/**
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function addGetAction(Request $request)
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_document_list'));
		}
		$document = new Document();
		$documentAddForm = $this->createForm(DocumentAddTForm::class, $document);
		$this->addTwigVar('document', $document);
		$this->addTwigVar('DocumentAddForm', $documentAddForm->createView());

		$this->addTwigVar('admmenu_active', 'documents_add');
		$this->addTwigVar('pageTitle', $this->translate('Document.pageTitle.admin.add'));
		$this->setHtmlHeadPageTitle($this->translate('Document.htmlHeadPageTitle.admin.add') . ' - ' . $this->getParameter('sitename'));
		return $this->render('IlcfrancePassportstagiaireFrontBundle:Document:add.html.twig', $this->getTwigVars());
	}

	/**
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function addPostAction(Request $request)
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_document_list'));
		}
		$urlFrom = $this->getReferer($request);
		if (null == $urlFrom || trim($urlFrom) == '') {
			return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_document_addGet'));
		}
		$document = new Document();
		$documentAddForm = $this->createForm(DocumentAddTForm::class, $document);

		$reqData = $request->request->all();

		if (isset($reqData['DocumentAddForm'])) {
			$documentAddForm->handleRequest($request);
			if ($documentAddForm->isValid()) {

				$em = $this->getEntityManager();

				$documentFile = $documentAddForm['file']->getData();

				$documentDir = $this->getParameter('kernel.root_dir') . '/../web/res/documents';

				$originalName = $documentFile->getClientOriginalName();
				$fileName = sha1(uniqid(mt_rand(), true)) . '.' . strtolower($documentFile->getClientOriginalExtension());
				$mimeType = $documentFile->getMimeType();
				$documentFile->move($documentDir, $fileName);

				$size = filesize($documentDir . '/' . $fileName);
				$md5 = md5_file($documentDir . '/' . $fileName);

				$document->setFileName($fileName);
				$document->setOriginalName($originalName);
				$document->setSize($size);
				$document->setMimeType($mimeType);
				$document->setMd5($md5);

				$em->persist($document);
				$em->flush();

				$cacheDriver = $em->getConfiguration()->getResultCacheImpl();
				$cacheDriver->delete('Document_getAllQuery');

				$this->addFlash('success', $this->translate('Document.add.success', array(
					'%document%' => $document->getOriginalName()
				)));

				return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_document_editGet', array(
					'id' => $document->getId()
				)));
			} else {
				$this->addFlash('error', $this->translate('Document.add.failure'));
			}
		}
		$this->addTwigVar('document', $document);
		$this->addTwigVar('DocumentAddForm', $documentAddForm->createView());

		$this->addTwigVar('admmenu_active', 'documents_add');
		$this->addTwigVar('pageTitle', $this->translate('Document.pageTitle.admin.add'));
		$this->setHtmlHeadPageTitle($this->translate('Document.htmlHeadPageTitle.admin.add') . ' - ' . $this->getParameter('sitename'));
		return $this->render('IlcfrancePassportstagiaireFrontBundle:Document:add.html.twig', $this->getTwigVars());
	}

	/**
	 *
	 * @param string $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function deleteAction($id, Request $request)
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_document_list'));
		}
		$urlFrom = $this->getReferer($request);
		if (null == $urlFrom || trim($urlFrom) == '') {
			$urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_document_list');
		}
		$em = $this->getEntityManager();
		try {
			$document = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Document')->getOneById($id);

			if (null == $document) {
				$this->addFlash('warning', $this->translate('Document.notfound'));
			} else {
				$em->remove($document);
				$em->flush();

				$cacheDriver = $em->getConfiguration()->getResultCacheImpl();
				$cacheDriver->delete('Document_getAllQuery');

				$this->addFlash('success', $this->translate('Document.delete.success', array(
					'%document%' => $document->getOriginalName()
				)));
			}
		} catch (\Exception $e) {
			$logger = $this->getLogger();
			$logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());

			$this->addFlash('error', $this->translate('Document.delete.failure'));
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
			$urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_document_list');
		}
		$em = $this->getEntityManager();
		try {
			$document = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Document')->getOneById($id);

			if (null == $document) {
				$this->addFlash('warning', $this->translate('Document.download.notfound'));
			} else {
				$documentDir = $this->getParameter('kernel.root_dir') . '/../web/res/documents';
				$fileName = $document->getFileName();

				try {
					$dlFile = new File($documentDir . '/' . $fileName);
					$response = new StreamedResponse(function () use ($dlFile) {
						$handle = fopen($dlFile->getRealPath(), 'r');
						while (!feof($handle)) {
							$buffer = fread($handle, 1024);
							echo $buffer;
							flush();
						}
						fclose($handle);
					});

					$response->headers->set('Content-Type', $document->getMimeType());
					$response->headers->set('Cache-Control', '');
					$response->headers->set('Content-Length', $document->getSize());
					$response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s', $document->getDtUpdate()->getTimestamp()));
					$fallback = $this->normalize($document->getOriginalName());

					$contentDisposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $document->getOriginalName(), $fallback);
					$response->headers->set('Content-Disposition', $contentDisposition);

					$document->setNbrDownloads($document->getNbrDownloads() + 1);
					$em->persist($document);
					$em->flush();

					return $response;
				} catch (FileNotFoundException $fnfex) {
					$this->addFlash('warning', $this->translate('Document.download.notfound'));
				}
			}
		} catch (\Exception $e) {
			$logger = $this->getLogger();
			$logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
			$this->addFlash('warning', $this->translate('Document.download.notfound'));
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
			return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_document_list'));
		}
		$urlFrom = $this->getReferer($request);
		if (null == $urlFrom || trim($urlFrom) == '') {
			$urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_document_list');
		}

		$em = $this->getEntityManager();
		try {
			$document = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Document')->getOneById($id);

			if (null == $document) {
				$this->addFlash('warning', $this->translate('Document.notfound'));
			} else {
				$documentUpdateDescriptionForm = $this->createForm(DocumentUpdateDescriptionTForm::class, $document);
				$documentUpdateContentForm = $this->createForm(DocumentUpdateContentTForm::class, $document);
				$documentUpdateOriginalNameForm = $this->createForm(DocumentUpdateOriginalNameTForm::class, $document);

				$this->addTwigVar('tabActive', $this->getSession()->get('tabActive', 1));
				$this->getSession()->remove('tabActive');

				$this->addTwigVar('document', $document);
				$this->addTwigVar('DocumentUpdateDescriptionForm', $documentUpdateDescriptionForm->createView());
				$this->addTwigVar('DocumentUpdateContentForm', $documentUpdateContentForm->createView());
				$this->addTwigVar('DocumentUpdateOriginalNameForm', $documentUpdateOriginalNameForm->createView());

				$this->addTwigVar('admmenu_active', 'documents_edit');
				$this->addTwigVar('pageTitle', $this->translate('Document.pageTitle.admin.edit', array(
					'%document%' => $document->getOriginalName()
				)));
				$this->setHtmlHeadPageTitle($this->translate('Document.htmlHeadPageTitle.admin.edit', array(
					'%document%' => $document->getOriginalName()
				)) . ' - ' . $this->getParameter('sitename'));

				return $this->render('IlcfrancePassportstagiaireFrontBundle:Document:edit.html.twig', $this->getTwigVars());
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
			return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_document_list'));
		}
		$urlFrom = $this->getReferer($request);
		if (null == $urlFrom || trim($urlFrom) == '') {
			$urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_document_list');
		}

		$em = $this->getEntityManager();
		try {
			$document = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Document')->getOneById($id);

			if (null == $document) {
				$this->addFlash('warning', $this->translate('Document.notfound'));
			} else {
				$documentUpdateDescriptionForm = $this->createForm(DocumentUpdateDescriptionTForm::class, $document);
				$documentUpdateContentForm = $this->createForm(DocumentUpdateContentTForm::class, $document);
				$documentUpdateOriginalNameForm = $this->createForm(DocumentUpdateOriginalNameTForm::class, $document);

				$this->addTwigVar('tabActive', $this->getSession()->get('tabActive', 1));
				$this->getSession()->remove('tabActive');
				$reqData = $request->request->all();

				if (isset($reqData['DocumentUpdateDescriptionForm'])) {
					$this->addTwigVar('tabActive', 2);
					$this->getSession()->set('tabActive', 2);
					$documentUpdateDescriptionForm->handleRequest($request);
					if ($documentUpdateDescriptionForm->isValid()) {
						$em->persist($document);
						$em->flush();

						$cacheDriver = $em->getConfiguration()->getResultCacheImpl();
						$cacheDriver->delete('Document_getAllQuery');

						$cacheId = 'Document_getOneByIdQuery' . $id;
						$cacheDriver->delete($cacheId);

						$this->addFlash('success', $this->translate('Document.edit.success', array(
							'%document%' => $document->getOriginalName()
						)));

						return $this->redirect($urlFrom);
					} else {
						$em->refresh($document);

						$this->addFlash('error', $this->translate('Document.edit.failure', array(
							'%document%' => $document->getOriginalName()
						)));
					}
				} elseif (isset($reqData['DocumentUpdateOriginalNameForm'])) {
					$this->addTwigVar('tabActive', 2);
					$this->getSession()->set('tabActive', 2);
					$documentUpdateOriginalNameForm->handleRequest($request);
					if ($documentUpdateOriginalNameForm->isValid()) {
						$em->persist($document);
						$em->flush();

						$cacheDriver = $em->getConfiguration()->getResultCacheImpl();
						$cacheDriver->delete('Document_getAllQuery');

						$cacheId = 'Document_getOneByIdQuery' . $id;
						$cacheDriver->delete($cacheId);

						$this->addFlash('success', $this->translate('Document.edit.success', array(
							'%document%' => $document->getOriginalName()
						)));

						return $this->redirect($urlFrom);
					} else {
						$em->refresh($document);

						$this->addFlash('error', $this->translate('Document.edit.failure', array(
							'%document%' => $document->getOriginalName()
						)));
					}
				} elseif (isset($reqData['DocumentUpdateContentForm'])) {
					$this->addTwigVar('tabActive', 2);
					$this->getSession()->set('tabActive', 2);
					$documentUpdateContentForm->handleRequest($request);
					if ($documentUpdateContentForm->isValid()) {

						$documentFile = $documentUpdateContentForm['file']->getData();

						$documentDir = $this->getParameter('kernel.root_dir') . '/../web/res/documents';

						$originalName = $documentFile->getClientOriginalName();
						$fileName = sha1(uniqid(mt_rand(), true)) . '.' . strtolower($documentFile->getClientOriginalExtension());
						$mimeType = $documentFile->getMimeType();
						$documentFile->move($documentDir, $fileName);

						$size = filesize($documentDir . '/' . $fileName);
						$md5 = md5_file($documentDir . '/' . $fileName);

						$document->setFileName($fileName);
						$document->setOriginalName($originalName);
						$document->setSize($size);
						$document->setMimeType($mimeType);
						$document->setMd5($md5);

						$em->persist($document);
						$em->flush();

						$cacheDriver = $em->getConfiguration()->getResultCacheImpl();
						$cacheDriver->delete('Document_getAllQuery');

						$cacheId = 'Document_getOneByIdQuery' . $id;
						$cacheDriver->delete($cacheId);

						$this->addFlash('success', $this->translate('Document.edit.success', array(
							'%document%' => $document->getOriginalName()
						)));

						return $this->redirect($urlFrom);
					} else {

						$em->refresh($document);

						$this->addFlash('error', $this->translate('Document.edit.failure', array(
							'%document%' => $document->getOriginalName()
						)));
					}
				}

				$this->addTwigVar('document', $document);
				$this->addTwigVar('DocumentUpdateDescriptionForm', $documentUpdateDescriptionForm->createView());
				$this->addTwigVar('DocumentUpdateContentForm', $documentUpdateContentForm->createView());
				$this->addTwigVar('DocumentUpdateOriginalNameForm', $documentUpdateOriginalNameForm->createView());

				$this->addTwigVar('admmenu_active', 'documents_edit');
				$this->addTwigVar('pageTitle', $this->translate('Document.pageTitle.admin.edit', array(
					'%document%' => $document->getOriginalName()
				)));
				$this->setHtmlHeadPageTitle($this->translate('Document.htmlHeadPageTitle.admin.edit', array(
					'%document%' => $document->getOriginalName()
				)) . ' - ' . $this->getParameter('sitename'));

				return $this->render('IlcfrancePassportstagiaireFrontBundle:Document:edit.html.twig', $this->getTwigVars());
			}
		} catch (\Exception $e) {
			$logger = $this->getLogger();
			$logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
		}

		return $this->redirect($urlFrom);
	}
}