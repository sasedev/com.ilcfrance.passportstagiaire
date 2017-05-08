<?php
namespace Ilcfrance\Passportstagiaire\ResBundle\Controller;

use Gaufrette\Adapter\Cache as CacheAdapter;
use Gaufrette\Adapter\Local as LocalAdapter;
use Gaufrette\Filesystem;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @author sasedev
 */
class VfsController extends IlcfranceController
{

	/**
	 * Get Temporary Virtual Files Action
	 *
	 * @param string $file
	 *
	 * @throws NotFoundHttpException
	 * @return Response
	 */
	public function tempfileAction($file = null, Request $request)
	{
		if (null == $file) {
			return $this->redirect($this->generateUrl('vfs_tmp_files', array(
				'file' => '/'
			)));
		}

		$cacheDirectory = $this->getParameter('adapter_cache_dir');
		$local = new LocalAdapter($cacheDirectory, true);
		$localadapter = new LocalAdapter($this->getParameter('adapter_tmp_files'));
		$adapter = new CacheAdapter($localadapter, $local, 3600);

		$fsAvatar = new Filesystem($adapter);
		if ($this->endswith($file, '/')) {
			if ($fsAvatar->has($file)) {

				$path = substr($file, 1);
				$fs = $fsAvatar->listKeys($path);

				$listfiles = array();
				foreach ($fs['dirs'] as $key) {
					$fulldir = $key . '/';
					$dirname = $key;
					if (substr($key, 0, strlen($path)) == $path) {
						$dirname = substr($dirname, strlen($path));
					}
					if (!strstr($dirname, '/')) {
						$listfiles[$fulldir] = $dirname;
					}
				}
				foreach ($fs['keys'] as $key) {
					$fullfile = $key;
					$filename = $key;
					if (substr($key, 0, strlen($path)) == $path) {
						$filename = substr($filename, strlen($path));
					}
					if (!strstr($filename, '/')) {
						$listfiles[$fullfile] = $filename;
					}
				}
				$this->addTwigVar('fs', $listfiles);

				$title = $this->translate('indexof', array(
					'%dir%' => $this->generateUrl('vfs_tmp_files', array(
						'file' => $file
					))
				));
				$this->addTwigVar('pageTitle', $title);
				$this->setHtmlHeadPageTitle($title);

				return $this->render('IlcfrancePassportstagiaireResBundle:Vfs:list_temp_files.html.twig', $this->getTwigVars());
			} else {
				throw new NotFoundHttpException();
			}
		}
		if ($fsAvatar->has($file)) {
			if ($fsAvatar->getAdapter()->isDirectory($file)) {
				$file .= '/';

				return $this->redirect($this->generateUrl('vfs_tmp_files', array(
					'file' => $file
				)));
			}
			$reqFile = $fsAvatar->get($file);
			$response = new Response();
			$response->headers->set('Content-Type', 'binary');
			$response->setContent($reqFile->getContent());

			return $response;
		} else {
			throw new NotFoundHttpException();
		}
	}

	/**
	 * Get Virtual Files Action
	 *
	 * @param string $file
	 *
	 * @throws NotFoundHttpException
	 * @return Response
	 */
	public function fileAction($file = null, Request $request)
	{
		if (null == $file) {
			return $this->redirect($this->generateUrl('vfs_files', array(
				'file' => '/'
			)));
		}

		$cacheDirectory = $this->getParameter('adapter_cache_dir');
		$local = new LocalAdapter($cacheDirectory, true);
		$localadapter = new LocalAdapter($this->getParameter('adapter_files'));
		$adapter = new CacheAdapter($localadapter, $local, 3600);

		$fsAvatar = new Filesystem($adapter);
		if ($this->endswith($file, '/')) {
			if ($fsAvatar->has($file)) {

				$path = substr($file, 1);
				$fs = $fsAvatar->listKeys($path);

				$listfiles = array();
				foreach ($fs['dirs'] as $key) {
					$fulldir = $key . '/';
					$dirname = $key;
					if (substr($key, 0, strlen($path)) == $path) {
						$dirname = substr($dirname, strlen($path));
					}
					if (!strstr($dirname, '/')) {
						$listfiles[$fulldir] = $dirname;
					}
				}
				foreach ($fs['keys'] as $key) {
					$fullfile = $key;
					$filename = $key;
					if (substr($key, 0, strlen($path)) == $path) {
						$filename = substr($filename, strlen($path));
					}
					if (!strstr($filename, '/')) {
						$listfiles[$fullfile] = $filename;
					}
				}
				$this->addTwigVar('fs', $listfiles);

				$title = $this->translate('indexof', array(
					'%dir%' => $this->generateUrl('vfs_files', array(
						'file' => $file
					))
				));
				$this->addTwigVar('pageTitle', $title);
				$this->setHtmlHeadPageTitle($title);

				return $this->render('IlcfrancePassportstagiaireResBundle:Vfs:list_files.html.twig', $this->getTwigVars());
			} else {
				throw new NotFoundHttpException();
			}
		}
		if ($fsAvatar->has($file)) {
			if ($fsAvatar->getAdapter()->isDirectory($file)) {
				$file .= '/';

				return $this->redirect($this->generateUrl('vfs_files', array(
					'file' => $file
				)));
			}
			$reqFile = $fsAvatar->get($file);
			$response = new Response();
			$response->headers->set('Content-Type', 'binary');
			$response->setContent($reqFile->getContent());

			return $response;
		} else {
			throw new NotFoundHttpException();
		}
	}
}