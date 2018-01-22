<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeHistorical;

use Ilcfrance\Passportstagiaire\DataBundle\Entity\TraineeHistorical;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
class UpdateLevelTForm extends AbstractType
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
        $builder->add('level', TextType::class, array(
            'label' => 'TraineeHistorical.level.label'
        ));

        $builder->add('traineeOverride', ChoiceType::class, array(
            'label' => 'TraineeHistorical.traineeOverride.label',
            'choices' => TraineeHistorical::choiceTraineeOverride(),
            'attr' => array(
                'choice_label_trans' => true
            ),
            'expanded' => true,
            'mapped' => false
        ));
    }

    /**
     *
     * {@inheritdoc} @see FormTypeInterface::getName()
     * @return string
     */
    public function getName()
    {
        return 'TraineeHistoricalUpdateLevelForm';
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
                'level'
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