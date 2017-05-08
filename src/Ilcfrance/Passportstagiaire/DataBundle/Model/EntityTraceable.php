<?php
namespace Ilcfrance\Passportstagiaire\DataBundle\Model;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
interface EntityTraceable extends \JsonSerializable
{

	/**
	 *
	 * @return mixed
	 */
	public function getId();

	/**
	 *
	 * @return array
	 */
	public function getRelated();
}