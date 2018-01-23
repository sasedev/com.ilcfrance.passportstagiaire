<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord;

use Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\TraineeHistoricalRepository;
use Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\TraineeRepository;
use Sasedev\Form\EntityidBundle\Form\Type\EntityidType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\TraineeHistorical;

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
                'by_reference' => true,
                'required' => true
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
                'by_reference' => true,
                'required' => true
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
                    'by_reference' => true,
                    'required' => true
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
                    'by_reference' => true,
                    'required' => true
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
                'by_reference' => true,
                'required' => true
            ));
        }

        $builder->add('recordDate', DateTimeType::class, array(
            'label' => 'TraineeRecord.recordDate.label',
            'widget' => 'single_text',
            'date_format' => 'Y-m-d H:i:s'
        ));

        $builder->add('worksCovered', TextareaType::class, array(
            'label' => 'TraineeRecord.worksCovered.label'
        ));

        $builder->add('takeaways', TextType::class, array(
            'label' => 'TraineeRecord.takeaways.label'
        ));

        $builder->add('comments', TextareaType::class, array(
            'label' => 'TraineeRecord.comments.label'
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
                'homeworks'
            ),
            'trainee' => null,
            'historical' => null
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