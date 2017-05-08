<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Form\Locale;

use Ilcfrance\Passportstagiaire\DataBundle\Entity\Locale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class AddTForm extends AbstractType
{

	/**
	 * Form builder
	 *
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 *
	 * @return null
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('id', TextType::class, array(
			'label' => 'Locale.id.label'
		));

		$builder->add('direction', ChoiceType::class, array(
			'label' => 'Locale.direction.label',
			'choices' => Locale::choiceDirection(),
			'attr' => array(
				'choice_label_trans' => true
			),
			'placeholder' => 'Locale.direction.placeholder'
		));

		$builder->add('status', ChoiceType::class, array(
			'label' => 'Locale.status.label',
			'choices' => Locale::choiceStatus(),
			'attr' => array(
				'choice_label_trans' => true
			),
			'placeholder' => 'Locale.status.placeholder'
		));
	}

	/**
	 *
	 * {@inheritdoc} @see FormTypeInterface::getName()
	 * @return string
	 */
	public function getName()
	{
		return 'LocaleAddForm';
	}

	/**
	 *
	 * {@inheritdoc} @see AbstractType::getBlockPrefix()
	 */
	public function getBlockPrefix()
	{
		return $this->getName();
	}

	/**
	 * get the default options
	 *
	 * @return multitype:string multitype:string
	 */
	public function getDefaultOptions()
	{
		return array(
			'validation_groups' => array(
				'id',
				'direction',
				'status'
			)
		);
	}

	/**
	 *
	 * {@inheritdoc} @see AbstractType::configureOptions()
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults($this->getDefaultOptions());
	}
}