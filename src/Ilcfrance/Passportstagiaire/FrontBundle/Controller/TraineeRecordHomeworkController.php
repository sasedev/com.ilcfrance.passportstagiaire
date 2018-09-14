<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Controller;

use Ilcfrance\Passportstagiaire\ResBundle\Controller\IlcfranceController;
use Symfony\Component\HttpFoundation\Request;

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