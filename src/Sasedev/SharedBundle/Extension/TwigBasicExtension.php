<?php
namespace Sasedev\SharedBundle\Extension;

use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
class TwigBasicExtension extends Twig_Extension
{

	/**
	 *
	 * {@inheritdoc} @see Twig_Extension::getFilters()
	 */
	public function getFilters()
	{
		$options = array(
			'pre_escape' => 'html',
			'is_safe' => array(
				'html'
			)
		);

		return array(
			new Twig_SimpleFilter('parse_icons_fa', array(
				$this,
				'parseIconsFaFilter'
			), $options),
			new Twig_SimpleFilter('parse_icons_glyph', array(
				$this,
				'parseIconsGlyphFilter'
			), $options),
			new Twig_SimpleFilter('parse_icons_half', array(
				$this,
				'parseIconsHalfFilter'
			), $options),
			new Twig_SimpleFilter('parse_icons_social', array(
				$this,
				'parseIconsSocialFilter'
			), $options),
			new Twig_SimpleFilter('parse_icons_ft', array(
				$this,
				'parseIconsFtFilter'
			), $options)
		);
	}

	/**
	 *
	 * {@inheritdoc} @see Twig_Extension::getFunctions()
	 */
	public function getFunctions()
	{
		$options = array(
			'pre_escape' => 'html',
			'is_safe' => array(
				'html'
			)
		);

		return array(
			new Twig_SimpleFunction('faIco', array(
				$this,
				'iconFaFunction'
			), $options),
			new Twig_SimpleFunction('glyphIco', array(
				$this,
				'iconGlyphFunction'
			), $options),
			new Twig_SimpleFunction('halfIco', array(
				$this,
				'iconHalfFunction'
			), $options),
			new Twig_SimpleFunction('socialIco', array(
				$this,
				'iconSocialFunction'
			), $options),
			new Twig_SimpleFunction('ftIco', array(
				$this,
				'iconFtFunction'
			), $options),
			new Twig_SimpleFunction('bsLabel', array(
				$this,
				'labelFunction'
			), $options),
			new Twig_SimpleFunction('bsLabelPrimary', array(
				$this,
				'labelFunction'
			), $options),
			new Twig_SimpleFunction('bsLabelSuccess', array(
				$this,
				'labelFunction'
			), $options),
			new Twig_SimpleFunction('bsLabelInfo', array(
				$this,
				'labelFunction'
			), $options),
			new Twig_SimpleFunction('bsLabelWarning', array(
				$this,
				'labelFunction'
			), $options),
			new Twig_SimpleFunction('bsLabelDanger', array(
				$this,
				'labelFunction'
			), $options),
			new Twig_SimpleFunction('bsBadge', array(
				$this,
				'labelFunction'
			), $options)
		);
	}

	/**
	 * Parses the given string and replaces all occurrences of .
	 * icon-[name] with the corresponding icon.
	 *
	 * @param string $text
	 *        	The text to parse
	 * @return string The HTML code with the icons
	 */
	public function parseIconsFaFilter($text)
	{
		$that = $this;
		return preg_replace_callback('/\.faIco-([a-z0-9-]+)/', function ($matches) use ($that) {
			return $that->iconFaFunction($matches[1]);
		}, $text);
	}

	/**
	 * Parses the given string and replaces all occurrences of .
	 * icon-[name] with the corresponding icon.
	 *
	 * @param string $text
	 *        	The text to parse
	 * @return string The HTML code with the icons
	 */
	public function parseIconsGlyphFilter($text)
	{
		$that = $this;
		return preg_replace_callback('/\.glyphIco-([a-z0-9-]+)/', function ($matches) use ($that) {
			return $that->iconGlyphFunction($matches[1]);
		}, $text);
	}

	/**
	 * Parses the given string and replaces all occurrences of .
	 * icon-[name] with the corresponding icon.
	 *
	 * @param string $text
	 *        	The text to parse
	 * @return string The HTML code with the icons
	 */
	public function parseIconsHalfFilter($text)
	{
		$that = $this;
		return preg_replace_callback('/\.halfIco-([a-z0-9-]+)/', function ($matches) use ($that) {
			return $that->iconHalfFunction($matches[1]);
		}, $text);
	}

	/**
	 * Parses the given string and replaces all occurrences of .
	 * icon-[name] with the corresponding icon.
	 *
	 * @param string $text
	 *        	The text to parse
	 * @return string The HTML code with the icons
	 */
	public function parseIconsSocialFilter($text)
	{
		$that = $this;
		return preg_replace_callback('/\.socialIco-([a-z0-9-]+)/', function ($matches) use ($that) {
			return $that->iconSocialFunction($matches[1]);
		}, $text);
	}

	/**
	 * Parses the given string and replaces all occurrences of .
	 * icon-[name] with the corresponding icon.
	 *
	 * @param string $text
	 *        	The text to parse
	 * @return string The HTML code with the icons
	 */
	public function parseIconsFtFilter($text)
	{
		$that = $this;
		return preg_replace_callback('/\.ftIco-([a-z0-9-]+)/', function ($matches) use ($that) {
			return $that->iconFtFunction($matches[1]);
		}, $text);
	}

	/**
	 * Returns the HTML code for the given icon.
	 *
	 * @param string $icon
	 *        	The name of the icon
	 * @return string The HTML code for the icon
	 */
	public function iconFaFunction($icon)
	{
		return sprintf('<span class="fa fa-%s"></span>', $icon);
	}

	/**
	 * Returns the HTML code for the given icon.
	 *
	 * @param string $icon
	 *        	The name of the icon
	 * @return string The HTML code for the icon
	 */
	public function iconGlyphFunction($icon)
	{
		return sprintf('<span class="glyphicons glyphicons-%s"></span>', $icon);
	}

	/**
	 * Returns the HTML code for the given icon.
	 *
	 * @param string $icon
	 *        	The name of the icon
	 * @return string The HTML code for the icon
	 */
	public function iconHalfFunction($icon)
	{
		return sprintf('<span class="halflings halflings-%s"></span>', $icon);
	}

	/**
	 * Returns the HTML code for the given icon.
	 *
	 * @param string $icon
	 *        	The name of the icon
	 * @return string The HTML code for the icon
	 */
	public function iconSocialFunction($icon)
	{
		return sprintf('<span class="social social-%s"></span>', $icon);
	}

	/**
	 * Returns the HTML code for the given icon.
	 *
	 * @param string $icon
	 *        	The name of the icon
	 * @return string The HTML code for the icon
	 */
	public function iconFtFunction($icon)
	{
		return sprintf('<span class="filetypes filetypes-%s"></span>', $icon);
	}

	/**
	 * Returns the HTML code for a label.
	 *
	 * @param string $text
	 *        	The text of the label
	 * @param string $type
	 *        	The type of label
	 * @return string The HTML code of the label
	 */
	public function labelFunction($text, $type = 'default')
	{
		return sprintf('<span class="label%s">%s</span>', ($type ? ' label-' . $type : ''), $text);
	}

	/**
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	public function labelPrimaryFunction($text)
	{
		return $this->labelFunction($text, 'primary');
	}

	/**
	 * Returns the HTML code for a success label.
	 *
	 * @param string $text
	 *        	The text of the label
	 * @return string The HTML code of the label
	 */
	public function labelSuccessFunction($text)
	{
		return $this->labelFunction($text, 'success');
	}

	/**
	 * Returns the HTML code for a warning label.
	 *
	 * @param string $text
	 *        	The text of the label
	 * @return string The HTML code of the label
	 */
	public function labelWarningFunction($text)
	{
		return $this->labelFunction($text, 'warning');
	}

	/**
	 * Returns the HTML code for a important label.
	 *
	 * @param string $text
	 *        	The text of the label
	 * @return string The HTML code of the label
	 */
	public function labelDangerFunction($text)
	{
		return $this->labelFunction($text, 'danger');
	}

	/**
	 * Returns the HTML code for a info label.
	 *
	 * @param string $text
	 *        	The text of the label
	 * @return string The HTML code of the label
	 */
	public function labelInfoFunction($text)
	{
		return $this->labelFunction($text, 'info');
	}

	/**
	 * Returns the HTML code for a badge.
	 *
	 * @param string $text
	 *        	The text of the badge
	 * @return string The HTML code of the badge
	 */
	public function badgeFunction($text)
	{
		return sprintf('<span class="badge">%s</span>', $text);
	}

	/**
	 *
	 * {@inheritdoc} @see Twig_ExtensionInterface::getName()
	 */
	public function getName()
	{
		return 'Sasedev.Shared.TwigBasic.Extension';
	}
}