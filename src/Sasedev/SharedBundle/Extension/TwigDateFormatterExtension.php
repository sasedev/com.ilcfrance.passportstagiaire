<?php
namespace Sasedev\SharedBundle\Extension;

use Twig_Extension;
use Twig_SimpleFilter;
use Sasedev\SharedBundle\Util\DateFormatter;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
class TwigDateFormatterExtension extends Twig_Extension
{

	public function getFilters()
	{
		return array(
			new Twig_SimpleFilter('localeDate', array(
				$this,
				'localeDateFilter'
			))
		);
	}

	/**
	 * Translate a timestamp to a localized string representation.
	 * Parameters dateType and timeType defines a kind of format. Allowed values are (none|short|medium|long|full).
	 * Default is medium for the date and no time.
	 * Uses default system locale by default. Pass another locale string to force a different translation.
	 * You might not like the default formats, so you can pass a custom pattern as last argument.
	 *
	 * @param mixed $date
	 * @param string $dateType
	 * @param string $timeType
	 * @param mixed $locale
	 * @param string $pattern
	 *
	 * @return string The string representation
	 */
	public function localeDateFilter($date, $dateType = 'medium', $timeType = 'none', $locale = null, $pattern = null)
	{
		$formatter = new DateFormatter();
		return $formatter->format($date, $dateType, $timeType, $locale, $pattern);
	}

	/**
	 *
	 * {@inheritdoc} @see Twig_ExtensionInterface::getName()
	 */
	public function getName()
	{
		return 'Sasedev.Shared.TwigDateFormatter.Extension';
	}
}