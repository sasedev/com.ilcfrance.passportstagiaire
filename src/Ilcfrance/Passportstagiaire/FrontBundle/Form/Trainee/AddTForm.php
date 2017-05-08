<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Form\Trainee;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
		$builder->add('lastName', TextType::class, array(
			'label' => 'Trainee.lastName.label'
		));

		$builder->add('firstName', TextType::class, array(
			'label' => 'Trainee.firstName.label'
		));

		$builder->add('address', TextType::class, array(
			'label' => 'Trainee.address.label'
		));

		$builder->add('town', TextType::class, array(
			'label' => 'Trainee.town.label'
		));

		$builder->add('email', EmailType::class, array(
			'label' => 'Trainee.email.label',
			'required' => false
		));

		$builder->add('phone', TextType::class, array(
			'label' => 'Trainee.phone.label',
			'required' => false
		));

		$builder->add('mobile', TextType::class, array(
			'label' => 'Trainee.mobile.label',
			'required' => false
		));

		$builder->add('job', TextType::class, array(
			'label' => 'Trainee.job.label'
		));

		$builder->add('level', TextType::class, array(
			'label' => 'Trainee.level.label'
		));

		$builder->add('needs', TextareaType::class, array(
			'label' => 'Trainee.needs.label'
		));

		$builder->add('courses', TextType::class, array(
			'label' => 'Trainee.courses.label',
			'required' => false
		));

		$builder->add('origine', TextType::class, array(
			'label' => 'Trainee.origine.label',
			'required' => false
		));
	}

	/**
	 *
	 * {@inheritdoc} @see FormTypeInterface::getName()
	 * @return string
	 */
	public function getName()
	{
		return 'TraineeAddForm';
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
				'lastName',
				'firstName',
				'address',
				'town',
				'email',
				'phone',
				'mobile',
				'job',
				'initLevel',
				'level',
				'needs',
				'courses'
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