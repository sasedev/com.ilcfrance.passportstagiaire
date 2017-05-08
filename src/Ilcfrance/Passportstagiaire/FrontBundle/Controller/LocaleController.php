<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Controller;

use Ilcfrance\Passportstagiaire\FrontBundle\Form\Locale\AddTForm as LocaleAddTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Locale\UpdateDirectionTForm as LocaleUpdateDirectionTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Locale\UpdateIdTForm as LocaleUpdateIdTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\Locale\UpdateStatusTForm as LocaleUpdateStatusTForm;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\Locale;
use Ilcfrance\Passportstagiaire\ResBundle\Controller\IlcfranceController;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class LocaleController extends IlcfranceController
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->addTwigVar('menu_active', 'admin');
		$this->addTwigVar('admmenu_active', 'locales');
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
		$locales = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Locale')->getAll();
		$this->addTwigVar('locales', $locales);

		$this->addTwigVar('admmenu_active', 'locales_list');
		$this->addTwigVar('pageTitle', $this->translate('Locale.pageTitle.admin.list'));
		$this->setHtmlHeadPageTitle($this->translate('Locale.htmlHeadPageTitle.admin.list') . ' - ' . $this->getParameter('sitename'));
		return $this->render('IlcfrancePassportstagiaireFrontBundle:Locale:list.html.twig', $this->getTwigVars());
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
		$locale = new Locale();
		$localeAddForm = $this->createForm(LocaleAddTForm::class, $locale);
		$this->addTwigVar('locale', $locale);
		$this->addTwigVar('LocaleAddForm', $localeAddForm->createView());

		$this->addTwigVar('admmenu_active', 'locales_add');
		$this->addTwigVar('pageTitle', $this->translate('Locale.pageTitle.admin.add'));
		$this->setHtmlHeadPageTitle($this->translate('Locale.htmlHeadPageTitle.admin.add') . ' - ' . $this->getParameter('sitename'));
		return $this->render('IlcfrancePassportstagiaireFrontBundle:Locale:add.html.twig', $this->getTwigVars());
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
			return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_locale_addGet'));
		}
		$locale = new Locale();
		$localeAddForm = $this->createForm(LocaleAddTForm::class, $locale);

		$reqData = $request->request->all();

		if (isset($reqData['LocaleAddForm'])) {
			$localeAddForm->handleRequest($request);
			if ($localeAddForm->isValid()) {
				$em = $this->getEntityManager();
				$em->persist($locale);
				$em->flush();

				$cacheDriver = $em->getConfiguration()->getResultCacheImpl();
				$cacheDriver->delete('Locale_getAllQuery');

				$this->addFlash('success', $this->translate('Locale.add.success', array(
					'%locale%' => $locale->getLanguageName()
				)));

				return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_locale_editGet', array(
					'id' => $locale->getId()
				)));
			} else {
				$this->addFlash('error', $this->translate('Locale.add.failure'));
			}
		}
		$this->addTwigVar('locale', $locale);
		$this->addTwigVar('LocaleAddForm', $localeAddForm->createView());

		$this->addTwigVar('admmenu_active', 'locales_add');
		$this->addTwigVar('pageTitle', $this->translate('Locale.pageTitle.admin.add'));
		$this->setHtmlHeadPageTitle($this->translate('Locale.htmlHeadPageTitle.admin.add') . ' - ' . $this->getParameter('sitename'));
		return $this->render('IlcfrancePassportstagiaireFrontBundle:Locale:add.html.twig', $this->getTwigVars());
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
			$urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_locale_list');
		}
		$em = $this->getEntityManager();
		try {
			$locale = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Locale')->getOneById($id);

			if (null == $locale) {
				$this->addFlash('warning', $this->translate('Locale.notfound'));
			} else {
				$em->remove($locale);
				$em->flush();

				$cacheDriver = $em->getConfiguration()->getResultCacheImpl();
				$cacheDriver->delete('Locale_getAllQuery');

				$this->addFlash('success', $this->translate('Locale.delete.success', array(
					'%locale%' => $locale->getLanguageName()
				)));
			}
		} catch (\Exception $e) {
			$logger = $this->getLogger();
			$logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());

			$this->addFlash('error', $this->translate('Locale.delete.failure'));
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
			$urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_locale_list');
		}

		$em = $this->getEntityManager();
		try {
			$locale = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Locale')->getOneById($id);

			if (null == $locale) {
				$this->addFlash('warning', $this->translate('Locale.notfound'));
			} else {
				$localeUpdateDirectionForm = $this->createForm(LocaleUpdateDirectionTForm::class, $locale);
				$localeUpdateIdForm = $this->createForm(LocaleUpdateIdTForm::class, $locale);
				$localeUpdateStatusForm = $this->createForm(LocaleUpdateStatusTForm::class, $locale);

				$this->addTwigVar('tabActive', $this->getSession()->get('tabActive', 1));
				$this->getSession()->remove('tabActive');

				$this->addTwigVar('locale', $locale);
				$this->addTwigVar('LocaleUpdateDirectionForm', $localeUpdateDirectionForm->createView());
				$this->addTwigVar('LocaleUpdateIdForm', $localeUpdateIdForm->createView());
				$this->addTwigVar('LocaleUpdateStatusForm', $localeUpdateStatusForm->createView());

				$this->addTwigVar('admmenu_active', 'locales_edit');
				$this->addTwigVar('pageTitle', $this->translate('Locale.pageTitle.admin.edit', array(
					'%locale%' => $locale->getLanguageName()
				)));
				$this->setHtmlHeadPageTitle($this->translate('Locale.htmlHeadPageTitle.admin.edit', array(
					'%locale%' => $locale->getLanguageName()
				)) . ' - ' . $this->getParameter('sitename'));

				return $this->render('IlcfrancePassportstagiaireFrontBundle:Locale:edit.html.twig', $this->getTwigVars());
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
			$urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_locale_list');
		}

		$em = $this->getEntityManager();
		try {
			$locale = $em->getRepository('IlcfrancePassportstagiaireDataBundle:Locale')->getOneById($id);

			if (null == $locale) {
				$this->addFlash('warning', $this->translate('Locale.notfound'));
			} else {
				$localeUpdateDirectionForm = $this->createForm(LocaleUpdateDirectionTForm::class, $locale);
				$localeUpdateIdForm = $this->createForm(LocaleUpdateIdTForm::class, $locale);
				$localeUpdateStatusForm = $this->createForm(LocaleUpdateStatusTForm::class, $locale);

				$this->addTwigVar('tabActive', $this->getSession()->get('tabActive', 1));
				$this->getSession()->remove('tabActive');
				$reqData = $request->request->all();

				if (isset($reqData['LocaleUpdateDirectionForm'])) {
					$this->addTwigVar('tabActive', 2);
					$this->getSession()->set('tabActive', 2);
					$localeUpdateDirectionForm->handleRequest($request);
					if ($localeUpdateDirectionForm->isValid()) {
						$em->persist($locale);
						$em->flush();

						$cacheDriver = $em->getConfiguration()->getResultCacheImpl();
						$cacheDriver->delete('Locale_getAllQuery');

						$cacheId = 'Locale_getOneByIdQuery' . $id;
						$cacheDriver->delete($cacheId);

						$this->addFlash('success', $this->translate('Locale.edit.success', array(
							'%locale%' => $locale->getLanguageName()
						)));

						return $this->redirect($urlFrom);
					} else {
						$em->refresh($locale);

						$this->addFlash('error', $this->translate('Locale.edit.failure', array(
							'%locale%' => $locale->getLanguageName()
						)));
					}
				} elseif (isset($reqData['LocaleUpdateIdForm'])) {
					$this->addTwigVar('tabActive', 2);
					$this->getSession()->set('tabActive', 2);
					$localeUpdateIdForm->handleRequest($request);
					if ($localeUpdateIdForm->isValid()) {
						$em->persist($locale);
						$em->flush();

						$cacheDriver = $em->getConfiguration()->getResultCacheImpl();
						$cacheDriver->delete('Locale_getAllQuery');

						$cacheId = 'Locale_getOneByIdQuery' . $id;
						$cacheDriver->delete($cacheId);

						$this->addFlash('success', $this->translate('Locale.edit.success', array(
							'%locale%' => $locale->getLanguageName()
						)));

						return $this->redirect($urlFrom);
					} else {
						$em->refresh($locale);

						$this->addFlash('error', $this->translate('Locale.edit.failure', array(
							'%locale%' => $locale->getLanguageName()
						)));
					}
				} elseif (isset($reqData['LocaleUpdateStatusForm'])) {
					$this->addTwigVar('tabActive', 2);
					$this->getSession()->set('tabActive', 2);
					$localeUpdateStatusForm->handleRequest($request);
					if ($localeUpdateStatusForm->isValid()) {
						$em->persist($locale);
						$em->flush();

						$cacheDriver = $em->getConfiguration()->getResultCacheImpl();
						$cacheDriver->delete('Locale_getAllQuery');

						$cacheId = 'Locale_getOneByIdQuery' . $id;
						$cacheDriver->delete($cacheId);

						$this->addFlash('success', $this->translate('Locale.edit.success', array(
							'%locale%' => $locale->getLanguageName()
						)));

						return $this->redirect($urlFrom);
					} else {
						$em->refresh($locale);

						$this->addFlash('error', $this->translate('Locale.edit.failure', array(
							'%locale%' => $locale->getLanguageName()
						)));
					}
				}

				$this->addTwigVar('locale', $locale);
				$this->addTwigVar('LocaleUpdateDirectionForm', $localeUpdateDirectionForm->createView());
				$this->addTwigVar('LocaleUpdateIdForm', $localeUpdateIdForm->createView());
				$this->addTwigVar('LocaleUpdateStatusForm', $localeUpdateStatusForm->createView());

				$this->addTwigVar('admmenu_active', 'locales_edit');
				$this->addTwigVar('pageTitle', $this->translate('Locale.pageTitle.admin.edit', array(
					'%locale%' => $locale->getLanguageName()
				)));
				$this->setHtmlHeadPageTitle($this->translate('Locale.htmlHeadPageTitle.admin.edit', array(
					'%locale%' => $locale->getLanguageName()
				)) . ' - ' . $this->getParameter('sitename'));

				return $this->render('IlcfrancePassportstagiaireFrontBundle:Locale:edit.html.twig', $this->getTwigVars());
			}
		} catch (\Exception $e) {
			$logger = $this->getLogger();
			$logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
		}

		return $this->redirect($urlFrom);
	}
}