<?php
namespace Sasedev\SharedBundle\Doctrine\DBAL\Pgsql\Types;

/**
 * TextArrayType
 *
 * @author sasedev <sinus@saseprod.net>
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
