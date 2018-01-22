<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Controller;

use Ilcfrance\Passportstagiaire\ResBundle\Controller\IlcfranceController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends IlcfranceController
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->addTwigVar('menu_active', 'admin');
        $this->addTwigVar('admmenu_active', 'home');
    }

    /**
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function indexAction(Request $request)
    {
        return $this->redirect($this->generateUrl('ilcfrance_passportstagiaire_front_trainee_list'));
    }
}
