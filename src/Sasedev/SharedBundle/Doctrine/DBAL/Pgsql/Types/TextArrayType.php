<?php
namespace Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types;

/**
 * TextArrayType
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class TextArrayType extends AbstractArrayType
{

	/**
	 *
	 * @var string
	 */
	const TEXTARRAY = 'text[]';

	/**
	 *
	 * @var string
	 */
	protected $name = self::TEXTARRAY;

	/**
	 *
	 * @var string
	 */
	protected $innerTypeName = 'text';
}
