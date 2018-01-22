<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeHistorical;

use Ilcfrance\Passportstagiaire\DataBundle\Entity\TraineeHistorical;
use Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\TraineeRepository;
use Sasedev\Form\EntityidBundle\Form\Type\EntityidType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @author sasedev <sinus@saseprod.net>
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
        $trainee = $options['trainee'];

        if (null == $trainee) {
            $builder->add('trainee', EntityType::class, array(
                'label' => 'TraineeHistorical.trainee.label',
                'class' => 'IlcfrancePassportstagiaireDataBundle:Trainee',
                'query_builder' => function (TraineeRepository $tr) {
                    return $tr->createQueryBuilder('t')
                        ->orderBy('t.lastName', 'ASC')
                        ->addOrderBy('t.firstName', 'ASC');
                },
                'choice_label' => 'fullName',
                'multiple' => false,
                'by_reference' => true,
                'required' => true
            ));
        } else {
            $builder->add('trainee', EntityidType::class, array(
                'label' => 'TraineeHistorical.trainee.label',
                'class' => 'IlcfrancePassportstagiaireDataBundle:Trainee',
                'query_builder' => function (TraineeRepository $tr) use ($trainee) {
                    return $tr->createQueryBuilder('t')
                        ->where('t.id = :id')
                        ->setParameter('id', $trainee->getId());
                },
                'choice_label' => 'id',
                'multiple' => false,
                'by_reference' => true,
                'required' => true
            ));
        }

        $builder->add('year', IntegerType::class, array(
            'label' => 'TraineeHistorical.year.label'
        ));

        $builder->add('origine', TextType::class, array(
            'label' => 'TraineeHistorical.origine.label'
        ));

        $builder->add('initLevel', TextType::class, array(
            'label' => 'TraineeHistorical.initLevel.label'
        ));

        $builder->add('level', TextType::class, array(
            'label' => 'TraineeHistorical.level.label',
            'required' => false
        ));

        $builder->add('needs', TextareaType::class, array(
            'label' => 'TraineeHistorical.needs.label'
        ));

        $builder->add('courses', TextType::class, array(
            'label' => 'TraineeHistorical.courses.label',
            'required' => false
        ));

        $builder->add('lockout', ChoiceType::class, array(
            'label' => 'TraineeHistorical.lockout.label',
            'choices' => TraineeHistorical::choiceLockout(),
            'attr' => array(
                'choice_label_trans' => true
            )
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
        return 'TraineeHistoricalAddForm';
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
                'trainee',
                'year',
                'origine',
                'initLevel',
                'level',
                'needs',
                'courses',
                'lockout'
            ),
            'trainee' => null
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

