<?php
namespace Ilcfrance\Passportstagiaire\ResBundle\Controller;

use Sasedev\SharedBundle\Controller\BaseController;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 * @author sasedev
 */
class IlcfranceController extends BaseController
{

	/**
	 * Sets the container.
	 *
	 * @param ContainerInterface|null $container
	 *        	A ContainerInterface instance or null
	 */
	public function setContainer(ContainerInterface $container = null)
	{
		parent::setContainer($container);
		$this->setHtmlHeadPageTitle($this->getParameter('sitename'));
	}
}