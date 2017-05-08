<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Controller;

use Ilcfrance\Passportstagiaire\FrontBundle\Form\Role\AddTForm as RoleAddTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Role\UpdateIdTForm as RoleUpdateIdTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Role\UpdateDescriptionTForm as RoleUpdateDescriptionTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Role\UpdateParentsTForm as RoleUpdateParentsTForm;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\Role;
use Ilcfrance\Passportstagiaire\ResBundle\Controller\IlcfranceController;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class RoleController extends IlcfranceController
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->addTwigVar('menu_active', 'admin');
		$this->addTwigVar('admmenu_active', 'roles');
	}

	/**
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function listAction(Request $request)
	{
		if (!$this->isGranted('ROLE_SUPERADMIN')) {
			return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_homepage'));
		}
		$em = $this->getEntityManager();
		$roles = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Role')->getAll();
		$this->addTwigVar('roles', $roles);

		$this->addTwigVar('admmenu_active', 'roles_list');
		$this->addTwigVar('pageTitle', $this->translate('Role.pageTitle.admin.list'));
		$this->setHtmlHeadPageTitle($this->translate('Role.htmlHeadPageTitle.admin.list') . ' - ' . $this->getParameter('sitename'));
		return $this->render('IlcfrancePassportstagiaireFrontBundle:Role:list.html.twig', $this->getTwigVars());
	}

	/**
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function addGetAction(Request $request)
	{
		if (!$this->isGranted('ROLE_SUPERADMIN')) {
			return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_homepage'));
		}
		$role = new Role();
		$roleAddForm = $this->createForm(RoleAddTForm::class, $role);
		$this->addTwigVar('role', $role);
		$this->addTwigVar('RoleAddForm', $roleAddForm->createView());

		$this->addTwigVar('admmenu_active', 'roles_add');
		$this->addTwigVar('pageTitle', $this->translate('Role.pageTitle.admin.add'));
		$this->setHtmlHeadPageTitle($this->translate('Role.htmlHeadPageTitle.admin.add') . ' - ' . $this->getParameter('sitename'));
		return $this->render('IlcfrancePassportstagiaireFrontBundle:Role:add.html.twig', $this->getTwigVars());
	}

	/**
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function addPostAction(Request $request)
	{
		if (!$this->isGranted('ROLE_SUPERADMIN')) {
			return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_homepage'));
		}
		$urlFrom = $this->getReferer($request);
		if (null == $urlFrom || trim($urlFrom) == '') {
			return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_role_addGet'));
		}
		$role = new Role();
		$roleAddForm = $this->createForm(RoleAddTForm::class, $role);

		$reqData = $request->request->all();

		if (isset($reqData['RoleAddForm'])) {
			$roleAddForm->handleRequest($request);
			if ($roleAddForm->isValid()) {
				$em = $this->getEntityManager();
				$em->persist($role);
				$em->flush();

				$cacheDriver = $em->getConfiguration()->getResultCacheImpl();
				$cacheDriver->delete('Role_getAllQuery');

				$this->addFlash('success', $this->translate('Role.add.success', array(
					'%role%' => $role->getId()
				)));

				return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_role_editGet', array(
					'id' => $role->getId()
				)));
			} else {
				$this->addFlash('error', $this->translate('Role.add.failure'));
			}
		}
		$this->addTwigVar('role', $role);
		$this->addTwigVar('RoleAddForm', $roleAddForm->createView());

		$this->addTwigVar('admmenu_active', 'roles_add');
		$this->addTwigVar('pageTitle', $this->translate('Role.pageTitle.admin.add'));
		$this->setHtmlHeadPageTitle($this->translate('Role.htmlHeadPageTitle.admin.add') . ' - ' . $this->getParameter('sitename'));
		return $this->render('IlcfrancePassportstagiaireFrontBundle:Role:add.html.twig', $this->getTwigVars());
	}

	/**
	 *
	 * @param string $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function deleteAction($id, Request $request)
	{
		if (!$this->isGranted('ROLE_SUPERADMIN')) {
			return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_homepage'));
		}
		$urlFrom = $this->getReferer($request);
		if (null == $urlFrom || trim($urlFrom) == '') {
			$urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_role_list');
		}
		$em = $this->getEntityManager();
		try {
			$role = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Role')->getOneById($id);

			if (null == $role) {
				$this->addFlash('warning', $this->translate('Role.notfound'));
			} else {
				$em->remove($role);
				$em->flush();

				$cacheDriver = $em->getConfiguration()->getResultCacheImpl();
				$cacheDriver->delete('Role_getAllQuery');

				$this->addFlash('success', $this->translate('Role.delete.success', array(
					'%role%' => $role->getId()
				)));
			}
		} catch (\Exception $e) {
			$logger = $this->getLogger();
			$logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());

			$this->addFlash('error', $this->translate('Role.delete.failure'));
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
		if (!$this->isGranted('ROLE_SUPERADMIN')) {
			return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_homepage'));
		}
		$urlFrom = $this->getReferer($request);
		if (null == $urlFrom || trim($urlFrom) == '') {
			$urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_role_list');
		}

		$em = $this->getEntityManager();
		try {
			$role = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Role')->getOneById($id);

			if (null == $role) {
				$this->addFlash('warning', $this->translate('Role.notfound'));
			} else {
				$roleUpdateIdForm = $this->createForm(RoleUpdateIdTForm::class, $role);
				$roleUpdateDescriptionForm = $this->createForm(RoleUpdateDescriptionTForm::class, $role);
				$roleUpdateParentsForm = $this->createForm(RoleUpdateParentsTForm::class, $role, array(
					'role' => $role
				));

				$this->addTwigVar('tabActive', $this->getSession()->get('tabActive', 1));
				$this->getSession()->remove('tabActive');

				$this->addTwigVar('role', $role);
				$this->addTwigVar('RoleUpdateIdForm', $roleUpdateIdForm->createView());
				$this->addTwigVar('RoleUpdateDescriptionForm', $roleUpdateDescriptionForm->createView());
				$this->addTwigVar('RoleUpdateParentsForm', $roleUpdateParentsForm->createView());

				$this->addTwigVar('admmenu_active', 'roles_edit');
				$this->addTwigVar('pageTitle', $this->translate('Role.pageTitle.admin.edit', array(
					'%role%' => $role->getId()
				)));
				$this->setHtmlHeadPageTitle($this->translate('Role.htmlHeadPageTitle.admin.edit', array(
					'%role%' => $role->getId()
				)) . ' - ' . $this->getParameter('sitename'));

				return $this->render('IlcfrancePassportstagiaireFrontBundle:Role:edit.html.twig', $this->getTwigVars());
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
		if (!$this->isGranted('ROLE_SUPERADMIN')) {
			return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_homepage'));
		}
		$urlFrom = $this->getReferer($request);
		if (null == $urlFrom || trim($urlFrom) == '') {
			$urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_role_list');
		}

		$em = $this->getEntityManager();
		try {
			$role = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Role')->getOneById($id);

			if (null == $role) {
				$this->addFlash('warning', $this->translate('Role.notfound'));
			} else {
				$roleUpdateIdForm = $this->createForm(RoleUpdateIdTForm::class, $role);
				$roleUpdateDescriptionForm = $this->createForm(RoleUpdateDescriptionTForm::class, $role);
				$roleUpdateParentsForm = $this->createForm(RoleUpdateParentsTForm::class, $role, array(
					'role' => $role
				));
				$this->addTwigVar('tabActive', $this->getSession()->get('tabActive', 1));
				$this->getSession()->remove('tabActive');
				$reqData = $request->request->all();

				if (isset($reqData['RoleUpdateIdForm'])) {
					$this->addTwigVar('tabActive', 2);
					$this->getSession()->set('tabActive', 2);
					$roleUpdateIdForm->handleRequest($request);
					if ($roleUpdateIdForm->isValid()) {
						$em->persist($role);
						$em->flush();

						$cacheDriver = $em->getConfiguration()->getResultCacheImpl();
						$cacheDriver->delete('Role_getAllQuery');

						$cacheId = 'Role_getOneByIdQuery' . $id;
						$cacheDriver->delete($cacheId);

						$this->addFlash('success', $this->translate('Role.edit.success', array(
							'%role%' => $role->getId()
						)));

						return $this->redirect($urlFrom);
					} else {
						$em->refresh($role);

						$this->addFlash('error', $this->translate('Role.edit.failure', array(
							'%role%' => $role->getId()
						)));
					}
				} elseif (isset($reqData['RoleUpdateDescriptionForm'])) {
					$this->addTwigVar('tabActive', 2);
					$this->getSession()->set('tabActive', 2);
					$roleUpdateDescriptionForm->handleRequest($request);
					if ($roleUpdateDescriptionForm->isValid()) {
						$em->persist($role);
						$em->flush();

						$cacheDriver = $em->getConfiguration()->getResultCacheImpl();
						$cacheDriver->delete('Role_getAllQuery');

						$cacheId = 'Role_getOneByIdQuery' . $id;
						$cacheDriver->delete($cacheId);

						$this->addFlash('success', $this->translate('Role.edit.success', array(
							'%role%' => $role->getId()
						)));

						return $this->redirect($urlFrom);
					} else {
						$em->refresh($role);

						$this->addFlash('error', $this->translate('Role.edit.failure', array(
							'%role%' => $role->getId()
						)));
					}
				} elseif (isset($reqData['RoleUpdateParentsForm'])) {
					$this->addTwigVar('tabActive', 2);
					$this->getSession()->set('tabActive', 2);
					$roleUpdateParentsForm->handleRequest($request);
					if ($roleUpdateParentsForm->isValid()) {
						$em->persist($role);
						$em->flush();

						$cacheDriver = $em->getConfiguration()->getResultCacheImpl();
						$cacheDriver->delete('Role_getAllQuery');

						$cacheId = 'Role_getOneByIdQuery' . $id;
						$cacheDriver->delete($cacheId);

						$this->addFlash('success', $this->translate('Role.edit.success', array(
							'%role%' => $role->getId()
						)));

						return $this->redirect($urlFrom);
					} else {
						$em->refresh($role);

						$this->addFlash('error', $this->translate('Role.edit.failure', array(
							'%role%' => $role->getId()
						)));
					}
				}

				$this->addTwigVar('role', $role);
				$this->addTwigVar('RoleUpdateIdForm', $roleUpdateIdForm->createView());
				$this->addTwigVar('RoleUpdateDescriptionForm', $roleUpdateDescriptionForm->createView());
				$this->addTwigVar('RoleUpdateParentsForm', $roleUpdateParentsForm->createView());

				$this->addTwigVar('admmenu_active', 'roles_edit');
				$this->addTwigVar('pageTitle', $this->translate('Role.pageTitle.admin.edit', array(
					'%role%' => $role->getId()
				)));
				$this->setHtmlHeadPageTitle($this->translate('Role.htmlHeadPageTitle.admin.edit', array(
					'%role%' => $role->getId()
				)) . ' - ' . $this->getParameter('sitename'));

				return $this->render('IlcfrancePassportstagiaireFrontBundle:Role:edit.html.twig', $this->getTwigVars());
			}
		} catch (\Exception $e) {
			$logger = $this->getLogger();
			$logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
		}

		return $this->redirect($urlFrom);
	}
}