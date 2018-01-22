<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Controller;

use Imagine\Imagick\Imagine;
use Imagine\Image\Point;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\User\AddTForm as UserAddTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\User\UpdateEmailTForm as UserUpdateEmailTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\User\UpdateIdTForm as UserUpdateIdTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\User\UpdateLocaleTForm as UserUpdateLocaleTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\User\UpdateLockoutTForm as UserUpdateLockoutTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\User\UpdateNameTForm as UserUpdateNameTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\User\UpdatePasswordTForm as UserUpdatePasswordTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\User\UpdateRolesTForm as UserUpdateRolesTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\UserPicture\CropPictureTForm as UserPictureCropPictureTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\UserPicture\UploadPictureTForm as UserPictureUploadPictureTForm;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\User;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\UserPicture;
use Ilcfrance\Passportstagiaire\ResBundle\Controller\IlcfranceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
class UserController extends IlcfranceController
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->addTwigVar('menu_active', 'admin');
        $this->addTwigVar('admmenu_active', 'users');
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_homepage'));
        }
        $em = $this->getEntityManager();
        $users = $em->getRepository('IlcfrancePassportstagiaireDataBundle:User')->findAll();
        $this->addTwigVar('users', $users);

        $this->addTwigVar('admmenu_active', 'users_list');
        $this->addTwigVar('pageTitle', $this->translate('User.pageTitle.admin.list'));
        $this->setHtmlHeadPageTitle($this->translate('User.htmlHeadPageTitle.admin.list') . ' - ' . $this->getParameter('sitename'));
        return $this->render('IlcfrancePassportstagiaireFrontBundle:User:list.html.twig', $this->getTwigVars());
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addGetAction(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_homepage'));
        }
        $user = new User();
        $userAddForm = $this->createForm(UserAddTForm::class, $user);
        $this->addTwigVar('user', $user);
        $this->addTwigVar('UserAddForm', $userAddForm->createView());

        $this->addTwigVar('admmenu_active', 'users_add');
        $this->addTwigVar('pageTitle', $this->translate('User.pageTitle.admin.add'));
        $this->setHtmlHeadPageTitle($this->translate('User.htmlHeadPageTitle.admin.add') . ' - ' . $this->getParameter('sitename'));
        return $this->render('IlcfrancePassportstagiaireFrontBundle:User:add.html.twig', $this->getTwigVars());
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addPostAction(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_homepage'));
        }
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_user_addGet'));
        }
        $user = new User();
        $userAddForm = $this->createForm(UserAddTForm::class, $user);

        $reqData = $request->request->all();

        if (isset($reqData['UserAddForm'])) {
            $userAddForm->handleRequest($request);
            if ($userAddForm->isValid()) {
                $em = $this->getEntityManager();
                $em->persist($user);
                $userPicture = $user->getPicture();
                if (null == $userPicture) {
                    $userPicture = new UserPicture($user);
                }
                $em->persist($userPicture);
                $em->flush();

                $this->addFlash('success', $this->translate('User.add.success', array(
                    '%user%' => $user->getId()
                )));

                return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_user_editGet', array(
                    'id' => $user->getId()
                )));
            } else {
                $this->addFlash('error', $this->translate('User.add.failure'));
            }
        }
        $this->addTwigVar('user', $user);
        $this->addTwigVar('UserAddForm', $userAddForm->createView());

        $this->addTwigVar('admmenu_active', 'users_add');
        $this->addTwigVar('pageTitle', $this->translate('User.pageTitle.admin.add'));
        $this->setHtmlHeadPageTitle($this->translate('User.htmlHeadPageTitle.admin.add') . ' - ' . $this->getParameter('sitename'));
        return $this->render('IlcfrancePassportstagiaireFrontBundle:User:add.html.twig', $this->getTwigVars());
    }

    /**
     *
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id, Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_homepage'));
        }
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_user_list');
        }
        $em = $this->getEntityManager();
        try {
            $user = $em->getRepository('IlcfrancePassportstagiaireDataBundle:User')->find($id);

            if (null == $user) {
                $this->addFlash('warning', $this->translate('User.notfound'));
            } else {
                $em->remove($user);
                $em->flush();

                $this->addFlash('success', $this->translate('User.delete.success', array(
                    '%user%' => $user->getId()
                )));
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());

            $this->addFlash('error', $this->translate('User.delete.failure'));
        }

        return $this->redirect($urlFrom);
    }

    /**
     *
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteUserPictureAction($id, Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_homepage'));
        }
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_user_list');
        }
        $em = $this->getEntityManager();
        try {
            $user = $em->getRepository('IlcfrancePassportstagiaireDataBundle:User')->find($id);

            if (null == $user) {
                $this->addFlash('warning', $this->translate('User.notfound'));
            } else {

                $userPicture = $user->getPicture();
                if (null == $userPicture) {
                    $userPicture = new UserPicture($user);
                    $em->persist($userPicture);
                    $em->flush();
                } else {
                    $userPicture->setUrl(null);
                    $em->persist($userPicture);
                    $em->flush();
                }

                $this->addFlash('success', $this->translate('UserPicture.delete.success', array(
                    '%user%' => $user->getId()
                )));
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());

            $this->addFlash('error', $this->translate('UserPicture.delete.failure'));
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
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_homepage'));
        }
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_user_list');
        }

        $em = $this->getEntityManager();
        try {
            $user = $em->getRepository('IlcfrancePassportstagiaireDataBundle:User')->find($id); // ->find($id);

            if (null == $user) {
                $this->addFlash('warning', $this->translate('User.notfound'));
                return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_user_list'));
            } else {
                $userPicture = $user->getPicture();
                if (null == $userPicture) {
                    $userPicture = new UserPicture($user);
                    $em->persist($userPicture);
                    $em->flush();
                }

                $userUpdateEmailForm = $this->createForm(UserUpdateEmailTForm::class, $user);
                $userUpdateIdForm = $this->createForm(UserUpdateIdTForm::class, $user);
                $userUpdateLocaleForm = $this->createForm(UserUpdateLocaleTForm::class, $user);
                $userUpdateLockoutForm = $this->createForm(UserUpdateLockoutTForm::class, $user);
                $userUpdateNameForm = $this->createForm(UserUpdateNameTForm::class, $user);
                $userUpdatePasswordForm = $this->createForm(UserUpdatePasswordTForm::class, $user);
                $userUpdateRolesForm = $this->createForm(UserUpdateRolesTForm::class, $user);
                $userPictureUploadPictureForm = $this->createForm(UserPictureUploadPictureTForm::class, $userPicture);
                $userPictureCropPictureForm = $this->createForm(UserPictureCropPictureTForm::class);

                $this->addTwigVar('tabActive', $this->getSession()
                    ->get('tabActive', 1));
                $this->getSession()->remove('tabActive');

                $this->addTwigVar('user', $user);
                $this->addTwigVar('UserUpdateEmailForm', $userUpdateEmailForm->createView());
                $this->addTwigVar('UserUpdateIdForm', $userUpdateIdForm->createView());
                $this->addTwigVar('UserUpdateLocaleForm', $userUpdateLocaleForm->createView());
                $this->addTwigVar('UserUpdateLockoutForm', $userUpdateLockoutForm->createView());
                $this->addTwigVar('UserUpdateNameForm', $userUpdateNameForm->createView());
                $this->addTwigVar('UserUpdatePasswordForm', $userUpdatePasswordForm->createView());
                $this->addTwigVar('UserUpdateRolesForm', $userUpdateRolesForm->createView());
                $this->addTwigVar('UserPictureUploadPictureForm', $userPictureUploadPictureForm->createView());
                $this->addTwigVar('UserPictureCropPictureForm', $userPictureCropPictureForm->createView());

                $this->addTwigVar('admmenu_active', 'users_edit');
                $this->addTwigVar('pageTitle', $this->translate('User.pageTitle.admin.edit', array(
                    '%user%' => $user->getId()
                )));
                $this->setHtmlHeadPageTitle($this->translate('User.htmlHeadPageTitle.admin.edit', array(
                    '%user%' => $user->getId()
                )) . ' - ' . $this->getParameter('sitename'));

                return $this->render('IlcfrancePassportstagiaireFrontBundle:User:edit.html.twig', $this->getTwigVars());
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
            return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_homepage'));
        }
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_user_list');
        }

        $em = $this->getEntityManager();
        try {
            $user = $em->getRepository('IlcfrancePassportstagiaireDataBundle:User')->find($id);

            if (null == $user) {
                $this->addFlash('warning', $this->translate('User.notfound'));
                return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_user_list'));
            } else {
                $userPicture = $user->getPicture();
                if (null == $userPicture) {
                    $userPicture = new UserPicture($user);
                    $em->persist($userPicture);
                    $em->flush();
                }

                $userUpdateEmailForm = $this->createForm(UserUpdateEmailTForm::class, $user);
                $userUpdateIdForm = $this->createForm(UserUpdateIdTForm::class, $user);
                $userUpdateLocaleForm = $this->createForm(UserUpdateLocaleTForm::class, $user);
                $userUpdateLockoutForm = $this->createForm(UserUpdateLockoutTForm::class, $user);
                $userUpdateNameForm = $this->createForm(UserUpdateNameTForm::class, $user);
                $userUpdatePasswordForm = $this->createForm(UserUpdatePasswordTForm::class, $user);
                $userUpdateRolesForm = $this->createForm(UserUpdateRolesTForm::class, $user);
                $userPictureUploadPictureForm = $this->createForm(UserPictureUploadPictureTForm::class, $userPicture);
                $userPictureCropPictureForm = $this->createForm(UserPictureCropPictureTForm::class);

                $this->addTwigVar('tabActive', $this->getSession()
                    ->get('tabActive', 1));
                $this->getSession()->remove('tabActive');
                $reqData = $request->request->all();

                if (isset($reqData['UserUpdateIdForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $userUpdateIdForm->handleRequest($request);
                    if ($userUpdateIdForm->isValid()) {
                        $logger = $this->getLogger();

                        $em->persist($user);
                        $em->flush();

                        $this->addFlash('success', $this->translate('User.edit.success', array(
                            '%user%' => $user->getId()
                        )));

                        return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_user_editGet', array(
                            'id' => $user->getId()
                        )));
                    } else {
                        $em->refresh($user);

                        $this->addFlash('error', $this->translate('User.edit.failure', array(
                            '%user%' => $user->getId()
                        )));
                    }
                } elseif (isset($reqData['UserUpdateEmailForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $userUpdateEmailForm->handleRequest($request);
                    if ($userUpdateEmailForm->isValid()) {
                        $em->persist($user);
                        $em->flush();

                        $this->addFlash('success', $this->translate('User.edit.success', array(
                            '%user%' => $user->getId()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($user);

                        $this->addFlash('error', $this->translate('User.edit.failure', array(
                            '%user%' => $user->getId()
                        )));
                    }
                } elseif (isset($reqData['UserUpdateLocaleForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $userUpdateLocaleForm->handleRequest($request);
                    if ($userUpdateLocaleForm->isValid()) {
                        $em->persist($user);
                        $em->flush();

                        $this->addFlash('success', $this->translate('User.edit.success', array(
                            '%user%' => $user->getId()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($user);

                        $this->addFlash('error', $this->translate('User.edit.failure', array(
                            '%user%' => $user->getId()
                        )));
                    }
                } elseif (isset($reqData['UserUpdateLockoutForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $userUpdateLockoutForm->handleRequest($request);
                    if ($userUpdateLockoutForm->isValid()) {
                        $em->persist($user);
                        $em->flush();

                        $this->addFlash('success', $this->translate('User.edit.success', array(
                            '%user%' => $user->getId()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($user);

                        $this->addFlash('error', $this->translate('User.edit.failure', array(
                            '%user%' => $user->getId()
                        )));
                    }
                } elseif (isset($reqData['UserUpdateNameForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $userUpdateNameForm->handleRequest($request);
                    if ($userUpdateNameForm->isValid()) {
                        $em->persist($user);
                        $em->flush();

                        $this->addFlash('success', $this->translate('User.edit.success', array(
                            '%user%' => $user->getId()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($user);

                        $this->addFlash('error', $this->translate('User.edit.failure', array(
                            '%user%' => $user->getId()
                        )));
                    }
                } elseif (isset($reqData['UserUpdatePasswordForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $userUpdatePasswordForm->handleRequest($request);
                    if ($userUpdatePasswordForm->isValid()) {
                        $em->persist($user);
                        $em->flush();

                        $this->addFlash('success', $this->translate('User.edit.success', array(
                            '%user%' => $user->getId()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($user);

                        $this->addFlash('error', $this->translate('User.edit.failure', array(
                            '%user%' => $user->getId()
                        )));
                    }
                } elseif (isset($reqData['UserUpdateRolesForm'])) {
                    $this->addTwigVar('tabActive', 2);
                    $this->getSession()->set('tabActive', 2);
                    $userUpdateRolesForm->handleRequest($request);
                    if ($userUpdateRolesForm->isValid()) {
                        $em->persist($user);
                        foreach ($user->getUserRoles() as $role) {
                            $em->persist($role);
                        }
                        $em->flush();

                        $this->addFlash('success', $this->translate('User.edit.success', array(
                            '%user%' => $user->getId()
                        )));

                        return $this->redirect($urlFrom);
                    } else {
                        $em->refresh($user);

                        $this->addFlash('error', $this->translate('User.edit.failure', array(
                            '%user%' => $user->getId()
                        )));
                    }
                } elseif (isset($reqData['UserPictureUploadPictureForm'])) {
                    $this->addTwigVar('tabActive', 3);
                    $this->getSession()->set('tabActive', 3);
                    $userPictureUploadPictureForm->handleRequest($request);
                    if ($userPictureUploadPictureForm->isValid()) {
                        $filename = $user->getId() . '_' . uniqid() . uniqid() . '.' . $userPictureUploadPictureForm['avatar']->getData()->guessExtension();
                        $userPictureUploadPictureForm['avatar']->getData()->move($this->getParameter('adapter_tmp_files'), $filename);
                        $userPictureCropPictureForm = $this->createForm(UserPictureCropPictureTForm::class, null, array(
                            'filename' => $filename
                        ));
                        $this->addTwigVar('UserPictureCropPictureForm', $userPictureCropPictureForm->createView());
                        $this->addTwigVar('tmp_avatar', $filename);
                        $this->addTwigVar('user', $user);

                        return $this->render('IlcfrancePassportstagiaireFrontBundle:User:cropPicture.html.twig', $this->getTwigVars());
                    } else {
                        $this->addTwigVar('UserPictureUploadPictureForm', $userPictureUploadPictureForm->createView());
                        $em->refresh($user);

                        return $this->render('IlcfrancePassportstagiaireFrontBundle:User:cropPictureError.html.twig', $this->getTwigVars());
                    }
                } elseif (isset($reqData['UserPictureCropPictureForm'])) {
                    $this->addTwigVar('tabActive', 3);
                    $this->getSession()->set('tabActive', 3);
                    $userPictureCropPictureForm->handleRequest($request);
                    if ($userPictureCropPictureForm->isValid()) {
                        try {
                            $filename = $userPictureCropPictureForm['avatar_tmp']->getData();
                            $path = $this->getParameter('adapter_tmp_files') . '/' . $filename;
                            $x1 = $userPictureCropPictureForm['x1']->getData();
                            $y1 = $userPictureCropPictureForm['y1']->getData();
                            $w = $userPictureCropPictureForm['w']->getData();
                            $h = $userPictureCropPictureForm['h']->getData();

                            $imagine = new Imagine();
                            $image = $imagine->open($path);

                            $firstpoint = new Point($x1, $y1);
                            $selbox = new Box($w, $h);
                            $lastbox = new Box(130, 130);
                            $mode = ImageInterface::THUMBNAIL_OUTBOUND;

                            $image->crop($firstpoint, $selbox)
                                ->thumbnail($lastbox, $mode)
                                ->save($path);

                            $file = new File($path);
                            $avatarDir = $this->getParameter('kernel.root_dir') . '/../web/res/UserPictures';
                            $file->move($avatarDir, $filename);

                            $userPicture->setUrl($filename);

                            $em->persist($user);
                            $em->flush();

                            $this->addFlash('success', $this->translate('UserPicture.edit.success', array(
                                '%user%' => $user->getId()
                            )));

                            $this->getSession()->remove('tabActive');

                            return $this->redirect($urlFrom);
                        } catch (\Exception $imgEx) {
                            $logger = $this->getLogger();
                            $logger->addCritical($imgEx->getLine() . ' ' . $imgEx->getMessage() . ' ' . $imgEx->getTraceAsString());

                            $this->addFlash('error', $this->translate('UserPicture.edit.failure', array(
                                '%user%' => $user->getId()
                            )));
                            $em->refresh($user);

                            return $this->redirect($urlFrom);
                        }
                    } else {
                        $this->addFlash('error', $this->translate('UserPicture.edit.failure', array(
                            '%user%' => $user->getId()
                        )));
                        $em->refresh($user);

                        return $this->redirect($urlFrom);
                    }
                }

                $this->addTwigVar('user', $user);
                $this->addTwigVar('UserUpdateEmailForm', $userUpdateEmailForm->createView());
                $this->addTwigVar('UserUpdateIdForm', $userUpdateIdForm->createView());
                $this->addTwigVar('UserUpdateLocaleForm', $userUpdateLocaleForm->createView());
                $this->addTwigVar('UserUpdateLockoutForm', $userUpdateLockoutForm->createView());
                $this->addTwigVar('UserUpdateNameForm', $userUpdateNameForm->createView());
                $this->addTwigVar('UserUpdatePasswordForm', $userUpdatePasswordForm->createView());
                $this->addTwigVar('UserUpdateRolesForm', $userUpdateRolesForm->createView());
                $this->addTwigVar('UserPictureUploadPictureForm', $userPictureUploadPictureForm->createView());
                $this->addTwigVar('UserPictureCropPictureForm', $userPictureCropPictureForm->createView());

                $this->addTwigVar('admmenu_active', 'users_edit');
                $this->addTwigVar('pageTitle', $this->translate('User.pageTitle.admin.edit', array(
                    '%user%' => $user->getId()
                )));
                $this->setHtmlHeadPageTitle($this->translate('User.htmlHeadPageTitle.admin.edit', array(
                    '%user%' => $user->getId()
                )) . ' - ' . $this->getParameter('sitename'));

                return $this->render('IlcfrancePassportstagiaireFrontBundle:User:edit.html.twig', $this->getTwigVars());
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
        }

        return $this->redirect($urlFrom);
    }
}