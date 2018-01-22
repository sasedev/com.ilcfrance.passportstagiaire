<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeHistorical;

use Ilcfrance\Passportstagiaire\DataBundle\Entity\TraineeHistorical;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
class UpdateNeedsTForm extends AbstractType
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
        $builder->add('needs', TextareaType::class, array(
            'label' => 'TraineeHistorical.needs.label'
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
        return 'TraineeHistoricalUpdateNeedsForm';
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
                'needs'
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