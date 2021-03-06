<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord;

use Ilcfrance\Passportstagiaire\DataBundle\Entity\TraineeHistorical;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\TraineeRecord;
use Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\TraineeHistoricalRepository;
use Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\TraineeRepository;
use Sasedev\Form\EntityidBundle\Form\Type\EntityidType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
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
                'label' => 'TraineeRecord.trainee.label',
                'class' => 'IlcfrancePassportstagiaireDataBundle:Trainee',
                'query_builder' => function (TraineeRepository $tr) {
                    return $tr->createQueryBuilder('t')
                        ->orderBy('t.lastName', 'ASC')
                        ->addOrderBy('t.firstName', 'ASC');
                },
                'choice_label' => 'fullName',
                'multiple' => false,
                'by_reference' => true
            ));
        } else {
            $builder->add('trainee', EntityidType::class, array(
                'label' => 'TraineeRecord.trainee.label',
                'class' => 'IlcfrancePassportstagiaireDataBundle:Trainee',
                'query_builder' => function (TraineeRepository $tr) use ($trainee) {
                    return $tr->createQueryBuilder('t')
                        ->where('t.id = :id')
                        ->setParameter('id', $trainee->getId());
                },
                'choice_label' => 'id',
                'multiple' => false,
                'by_reference' => true
            ));
        }

        $historical = $options['historical'];

        if (null == $historical) {
            if (null == $trainee) {
                $builder->add('historical', EntityType::class, array(
                    'label' => 'TraineeRecord.historical.label',
                    'class' => 'IlcfrancePassportstagiaireDataBundle:TraineeHistorical',
                    'query_builder' => function (TraineeHistoricalRepository $thr) {
                        return $thr->createQueryBuilder('th')
                            ->where('th.lockout = :lockout')
                            ->setParameter('lockout', TraineeHistorical::LOCKOUT_UNLOCKED)
                            ->orderBy('th.year', 'ASC')
                            ->addOrderBy('th.origine', 'ASC');
                    },
                    'choice_label' => 'fullName',
                    'multiple' => false,
                    'by_reference' => true
                ));
            } else {
                $builder->add('historical', EntityType::class, array(
                    'label' => 'TraineeRecord.historical.label',
                    'class' => 'IlcfrancePassportstagiaireDataBundle:TraineeHistorical',
                    'query_builder' => function (TraineeHistoricalRepository $thr) use ($trainee) {
                        return $thr->createQueryBuilder('th')
                            ->where('th.lockout = :lockout')
                            ->andWhere('th.trainee = :trainee')
                            ->setParameter('lockout', TraineeHistorical::LOCKOUT_UNLOCKED)
                            ->setParameter('trainee', $trainee)
                            ->orderBy('th.year', 'ASC')
                            ->addOrderBy('th.origine', 'ASC');
                    },
                    'choice_label' => 'fullName',
                    'multiple' => false,
                    'by_reference' => true
                ));
            }
        } else {
            $builder->add('historical', EntityidType::class, array(
                'label' => 'TraineeRecord.historical.label',
                'class' => 'IlcfrancePassportstagiaireDataBundle:TraineeHistorical',
                'query_builder' => function (TraineeHistoricalRepository $thr) use ($historical) {
                    return $thr->createQueryBuilder('th')
                        ->where('th.id = :id')
                        ->setParameter('id', $historical->getId());
                },
                'choice_label' => 'id',
                'multiple' => false,
                'by_reference' => true
            ));
        }

        $builder->add('recordDate', DateTimeType::class, array(
            'label' => 'TraineeRecord.recordDate.label',
            'widget' => 'single_text',
            'date_format' => 'Y-m-d H:i:s',
            'html5' => false
        ));
        
        $builder->add('recordType', ChoiceType::class, array(
            'label' => 'TraineeRecord.recordType.label',
            'choices' => TraineeRecord::choiceRecordType(),
            'attr' => array(
                'choice_label_trans' => true
            ),
            'placeholder' => 'TraineeRecord.recordType.placeholder'
        ));
        
        $builder->add('correctionVocabulairy', TextareaType::class, array(
            'label' => 'TraineeRecord.correctionVocabulairy.label',
            'required' => false
        ));
        
        $builder->add('correctionStructure', TextareaType::class, array(
            'label' => 'TraineeRecord.correctionStructure.label',
            'required' => false
        ));
        
        $builder->add('correctionPrononciation', TextareaType::class, array(
            'label' => 'TraineeRecord.correctionPrononciation.label',
            'required' => false
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
                'trainee',
                'historical',
                'recordDate',
                'worksCovered',
                'takeaways',
                'comments',
                'homeworks',
                'correctionVocabulairy',
                'correctionStructure',
                'correctionPrononciation'
            ),
            'trainee' => null,
            'historical' => null,
            'data_class' => TraineeRecord::class,
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