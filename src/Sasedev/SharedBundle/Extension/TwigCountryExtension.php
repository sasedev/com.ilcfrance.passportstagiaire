<?php
namespace Sasedev\SharedBundle\Extension;

use Twig_Extension;
use Twig_SimpleFilter;
use Symfony\Component\Intl\Intl;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
class TwigCountryExtension extends Twig_Extension
{

	public function getFilters()
	{
		return array(
			new Twig_SimpleFilter('country', array(
				$this,
				'countryFilter'
			))
		);
	}

	/**
	 * Translate a country indicator to its locale full name
	 * Uses default system locale by default.
	 * Pass another locale string to force a different translation
	 *
	 * @param string $country
	 *        	The contry indicator
	 * @param string $default
	 *        	The default value is the country does not exist (optionnal)
	 * @param mixed $locale
	 * @return string The localized string
	 */
	public function countryFilter($country, $default = '', $locale = null)
	{
		$locale = $locale == null ? \Locale::getDefault() : $locale;
		$countries = Intl::getRegionBundle()->getCountryNames($locale);
		return array_key_exists($country, $countries) ? $countries[$country] : $default;
	}

	/**
	 *
	 * {@inheritdoc} @see Twig_ExtensionInterface::getName()
	 */
	public function getName()
	{
		return 'Sasedev.Shared.TwigCountryFormatter.Extension';
	}
}