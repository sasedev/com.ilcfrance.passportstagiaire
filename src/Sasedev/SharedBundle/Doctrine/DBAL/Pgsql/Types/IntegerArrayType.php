<?php
namespace Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types;

/**
 * IntegerArrayType
 *
 * @author sasedev <sinus@saseprod.net>
 */
class IntegerArrayType extends AbstractArrayType
{

	/**
	 *
	 * @var string
	 */
	const INTEGERARRAY = 'integer[]';

	/**
	 *
	 * @var string
	 */
	protected $name = self::INTEGERARRAY;

	/**
	 *
	 * @var string
	 */
	protected $innerTypeName = 'integer';
}
