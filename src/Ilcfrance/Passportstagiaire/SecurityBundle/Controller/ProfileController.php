<?php
namespace Ilcfrance\Passportstagiaire\SecurityBundle\Controller;

use Imagine\Imagick\Imagine;
use Imagine\Image\Point;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Ilcfrance\Passportstagiaire\SecurityBundle\Form\UpdatePasswordTForm as UserUpdatePasswordTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\User\UpdateEmailTForm as UserUpdateEmailTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\User\UpdateLocaleTForm as UserUpdateLocaleTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\User\UpdateNameTForm as UserUpdateNameTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\UserPicture\CropPictureTForm as UserPictureCropPictureTForm;
use Ilcfrance\Passportstagiaire\FrontBundle\Form\UserPicture\UploadPictureTForm as UserPictureUploadPictureTForm;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\UserPicture;
use Ilcfrance\Passportstagiaire\ResBundle\Controller\IlcfranceController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\Encoder\Pbkdf2PasswordEncoder;
use Symfony\Component\Form\FormError;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
class ProfileController extends IlcfranceController
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->addTwigVar('menu_active', 'security');
    }

    /**
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function getAction(Request $request)
    {
        if (! $this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('_security_login'));
        }
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_homepage');
        }
        try {
            $user = $this->getSecurityTokenStorage()
                ->getToken()
                ->getUser();

            $userPicture = $user->getPicture();
            if (null == $userPicture) {
                $userPicture = new UserPicture($user);
                $em = $this->getEntityManager();
                $em->persist($userPicture);
                $em->flush();
            }

            $userUpdateEmailForm = $this->createForm(UserUpdateEmailTForm::class, $user);
            $userUpdateLocaleForm = $this->createForm(UserUpdateLocaleTForm::class, $user);
            $userUpdateNameForm = $this->createForm(UserUpdateNameTForm::class, $user);
            $userUpdatePasswordForm = $this->createForm(UserUpdatePasswordTForm::class, $user);
            $userPictureUploadPictureForm = $this->createForm(UserPictureUploadPictureTForm::class, $userPicture);
            $userPictureCropPictureForm = $this->createForm(UserPictureCropPictureTForm::class);

            $this->addTwigVar('tabActive', $this->getSession()
                ->get('tabActive', 1));
            $this->getSession()->remove('tabActive');

            $this->addTwigVar('user', $user);
            $this->addTwigVar('UserUpdateEmailForm', $userUpdateEmailForm->createView());
            $this->addTwigVar('UserUpdateLocaleForm', $userUpdateLocaleForm->createView());
            $this->addTwigVar('UserUpdateNameForm', $userUpdateNameForm->createView());
            $this->addTwigVar('UserUpdatePasswordForm', $userUpdatePasswordForm->createView());
            $this->addTwigVar('UserPictureUploadPictureForm', $userPictureUploadPictureForm->createView());
            $this->addTwigVar('UserPictureCropPictureForm', $userPictureCropPictureForm->createView());

            $this->addTwigVar('pageTitle', $this->translate('User.pageTitle.admin.profile'));
            $this->setHtmlHeadPageTitle($this->translate('User.htmlHeadPageTitle.admin.profile') . ' - ' . $this->getParameter('sitename'));

            return $this->render('IlcfrancePassportstagiaireSecurityBundle:Profile:edit.html.twig', $this->getTwigVars());
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
        }

        return $this->redirect($urlFrom);
    }

    /**
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function postAction(Request $request)
    {
        if (! $this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('_security_login'));
        }
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('ilcfrance_passportstagiaire_front_homepage');
        }
        try {
            $user = $this->getSecurityTokenStorage()
                ->getToken()
                ->getUser();
            $oldDbpass = $user->getPassword();
            $em = $this->getEntityManager();

            $userPicture = $user->getPicture();
            if (null == $userPicture) {
                $userPicture = new UserPicture($user);
                $em->persist($userPicture);
                $em->flush();
            }

            $userUpdateEmailForm = $this->createForm(UserUpdateEmailTForm::class, $user);
            $userUpdateLocaleForm = $this->createForm(UserUpdateLocaleTForm::class, $user);
            $userUpdateNameForm = $this->createForm(UserUpdateNameTForm::class, $user);
            $userUpdatePasswordForm = $this->createForm(UserUpdatePasswordTForm::class, $user);
            $userPictureUploadPictureForm = $this->createForm(UserPictureUploadPictureTForm::class, $userPicture);
            $userPictureCropPictureForm = $this->createForm(UserPictureCropPictureTForm::class);

            $this->addTwigVar('tabActive', $this->getSession()
                ->get('tabActive', 1));
            $this->getSession()->remove('tabActive');
            $reqData = $request->request->all();

            if (isset($reqData['UserUpdateEmailForm'])) {
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
                    $oldPassword = $userUpdatePasswordForm['oldPassword']->getData();
                    $encoder = new Pbkdf2PasswordEncoder('sha512', true, 1000);
                    $oldpassEncoded = $encoder->encodePassword($oldPassword, $user->getSalt());
                    if ($oldpassEncoded != $oldDbpass) {
                        $formError = new FormError($this->translate('User.oldPassword.incorrect'));

                        $userUpdatePasswordForm['oldPassword']->addError($formError);
                        $this->addFlash('error', $this->translate('User.edit.failure', array(
                            '%user%' => $user->getId()
                        )));
                    } else {
                        $em->persist($user);
                        $em->flush();
                        $this->addFlash('success', $this->translate('User.edit.success', array(
                            '%user%' => $user->getId()
                        )));

                        return $this->redirect($urlFrom);
                    }
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

                    return $this->render('IlcfrancePassportstagiaireSecurityBundle:Profile:cropPicture.html.twig', $this->getTwigVars());
                } else {
                    $this->addTwigVar('UserPictureUploadPictureForm', $userPictureUploadPictureForm->createView());
                    $em->refresh($user);

                    return $this->render('IlcfrancePassportstagiaireSecurityBundle:Profile:cropPictureError.html.twig', $this->getTwigVars());
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
            $this->addTwigVar('UserUpdateLocaleForm', $userUpdateLocaleForm->createView());
            $this->addTwigVar('UserUpdateNameForm', $userUpdateNameForm->createView());
            $this->addTwigVar('UserUpdatePasswordForm', $userUpdatePasswordForm->createView());
            $this->addTwigVar('UserPictureUploadPictureForm', $userPictureUploadPictureForm->createView());
            $this->addTwigVar('UserPictureCropPictureForm', $userPictureCropPictureForm->createView());

            $this->addTwigVar('pageTitle', $this->translate('User.pageTitle.admin.profile'));
            $this->setHtmlHeadPageTitle($this->translate('User.htmlHeadPageTitle.admin.profile') . ' - ' . $this->getParameter('sitename'));

            return $this->render('IlcfrancePassportstagiaireSecurityBundle:Profile:edit.html.twig', $this->getTwigVars());
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());
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
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('_security_homepage'));
        }
        $urlFrom = $this->getReferer($request);
        if (null == $urlFrom || trim($urlFrom) == '') {
            $urlFrom = $this->generateUrl('_security_myProfileGet');
        }
        $em = $this->getEntityManager();
        try {
            $user = $this->getSecurityTokenStorage()
                ->getToken()
                ->getUser();

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
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());

            $this->addFlash('error', $this->translate('UserPicture.delete.failure'));
        }

        return $this->redirect($urlFrom);
    }
}