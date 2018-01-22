<?php
namespace Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types;

/**
 * BooleanArrayType
 *
 * @author sasedev <sinus@saseprod.net>
 */
class BooleanArrayType extends AbstractArrayType
{

	/**
	 *
	 * @var string
	 */
	const BOOLEANARRAY = 'boolean[]';

	/**
	 *
	 * @var string
	 */
	protected $name = self::BOOLEANARRAY;

	/**
	 *
	 * @var string
	 */
	protected $innerTypeName = 'boolean';
}
