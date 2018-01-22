<?php
namespace Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types;

/**
 * FloatArrayType
 *
 * @author sasedev <sinus@saseprod.net>
 */
class FloatArrayType extends AbstractArrayType
{

	/**
	 *
	 * @var string
	 */
	const FLOATARRAY = 'float[]';

	/**
	 *
	 * @var string
	 */
	protected $name = self::FLOATARRAY;

	/**
	 *
	 * @var string
	 */
	protected $innerTypeName = 'float';
}
