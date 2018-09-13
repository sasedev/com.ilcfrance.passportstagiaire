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
class TraineeRecordHomeworkController extends IlcfranceController
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
            $traineeRecordHomework = $em->getRepository('IlcfrancePassportstagiaireDataBundle:TraineeRecordHomework')->find($id);

            if (null == $traineeRecordHomework) {
                $this->addFlash('warning', $this->translate('TraineeRecordHomework.notfound'));
            } else {
                if (!$this->isGranted('ROLE_ADMIN')) {
                    $user = $this->getSecurityTokenStorage()
                        ->getToken()
                        ->getUser();
                        if ($user->getId() != $traineeRecordHomework->getTraineeRecord()
                        ->getTeacher()
                        ->getId()) {
                        $this->addFlash('error', $this->translate('TraineeRecordHomework.delete.failure'));
                        return $this->redirect($urlFrom);
                    }
                }

                $em->remove($traineeRecordHomework);
                $em->flush();

                $this->addFlash('success', $this->translate('TraineeRecordHomework.delete.success', array(
                    '%traineeRecordHomework%' => $traineeRecordHomework->getHomework()->getOriginalName()
                )));
            }
        } catch (\Exception $e) {
            $logger = $this->getLogger();
            $logger->addCritical($e->getLine() . ' ' . $e->getMessage() . ' ' . $e->getTraceAsString());

            $this->addFlash('error', $this->translate('TraineeRecordHomework.delete.failure'));
        }

        return $this->redirect($urlFrom);
    }
}