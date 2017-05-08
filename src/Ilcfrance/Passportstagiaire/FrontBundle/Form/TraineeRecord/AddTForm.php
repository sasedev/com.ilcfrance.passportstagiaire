<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
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
		$builder->add('recordDate', DateTimeType::class, array(
			'label' => 'TraineeRecord.recordDate.label',
			'widget' => 'single_text',
			'date_format' => 'Y-m-d H:i:s'
		));

		$builder->add('worksCovered', TextareaType::class, array(
			'label' => 'TraineeRecord.worksCovered.label',
			'required' => false
		));

		$builder->add('takeaways', TextType::class, array(
			'label' => 'TraineeRecord.takeaways.label',
			'required' => false
		));

		$builder->add('comments', TextareaType::class, array(
			'label' => 'TraineeRecord.comments.label',
			'required' => false
		));

		$builder->add('homeworks', TextareaType::class, array(
			'label' => 'TraineeRecord.homeworks.label',
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
		return 'TraineeRecordAddForm';
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
				'recordDate',
				'worksCovered',
				'takeaways',
				'comments',
				'homeworks'
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