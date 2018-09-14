<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecordHomework;

use Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\HomeworkRepository;
use Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\TraineeRecordRepository;
use Sasedev\Form\EntityidBundle\Form\Type\EntityidType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
        $traineeRecord = $options['traineeRecord'];
        $homework = $options['homework'];

        if (null == $traineeRecord) {
            $builder->add('traineeRecord', EntityType::class, array(
                'label' => 'TraineeRecordHomework.traineeRecord.label',
                'class' => 'IlcfrancePassportstagiaireDataBundle:TraineeRecord',
                'query_builder' => function (TraineeRecordRepository $trr) {
                    return $trr->createQueryBuilder('tr')
                        ->orderBy('tr.recordDate', 'ASC');
                },
                'choice_label' => 'recordDate',
                'multiple' => false,
                'by_reference' => true,
                'required' => true
            ));
        } else {
            $builder->add('traineeRecord', EntityidType::class, array(
                'label' => 'TraineeRecordHomework.traineeRecord.label',
                'class' => 'IlcfrancePassportstagiaireDataBundle:TraineeRecord',
                'query_builder' => function (TraineeRecordRepository $trr) use ($traineeRecord) {
                    return $trr->createQueryBuilder('tr')
                        ->where('tr.id = :id')
                        ->setParameter('id', $traineeRecord->getId());
                },
                'choice_label' => 'id',
                'multiple' => false,
                'by_reference' => true,
                'required' => true
            ));
        }

        if (null == $homework) {
            $builder->add('homework', EntityType::class, array(
                'label' => 'TraineeRecordHomework.homework.label',
                'class' => 'IlcfrancePassportstagiaireDataBundle:Homework',
                'query_builder' => function (HomeworkRepository $hr) {
                    return $hr->createQueryBuilder('hr')
                        ->join('hr.level', 'l')
                        ->orderBy('l.name', 'ASC')
                        ->addOrderBy('hr.fileName', 'ASC');
                },
                'group_by' => function ($choiceValue, $key, $value) {
                    return $choiceValue->getLevel()->getName();
                },
                'choice_label' => 'originalName',
                'multiple' => false,
                'by_reference' => true,
                'required' => true
            ));
        } else {
            $builder->add('homework', EntityidType::class, array(
                'label' => 'TraineeRecordDocument.homework.label',
                'class' => 'IlcfrancePassportstagiaireDataBundle:Homework',
                'query_builder' => function (HomeworkRepository $hr) use ($homework) {
                    return $hr->createQueryBuilder('hr')
                        ->where('hr.id = :id')
                        ->setParameter('id', $homework->getId());
                },
                'choice_label' => 'id',
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
        return 'TraineeRecordHomeworkAddForm';
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
                'traineeRecord',
                'homework'
            ),
            'traineeRecord' => null,
            'homework' => null
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
