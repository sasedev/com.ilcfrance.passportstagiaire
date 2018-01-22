<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord;

use Ilcfrance\Passportstagiaire\DataBundle\Entity\TraineeHistorical;
use Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\TraineeHistoricalRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
class UpdateHistoricalTForm extends AbstractType
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
    }

    /**
     *
     * {@inheritdoc} @see FormTypeInterface::getName()
     * @return string
     */
    public function getName()
    {
        return 'TraineeRecordUpdateHistoricalForm';
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
                'historical'
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