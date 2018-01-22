<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecordDocument;

use Sasedev\Form\EntityidBundle\Form\Type\EntityidType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\TraineeRecordRepository;

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

        if (null == $traineeRecord) {
            $builder->add('traineeRecord', EntityType::class, array(
                'label' => 'TraineeRecordDocument.traineeRecord.label',
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
                'label' => 'TraineeRecordDocument.traineeRecord.label',
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

        $builder->add('file', FileType::class, array(
            'label' => 'TraineeRecordDocument.fileName.label',
            'constraints' => array(
                new File(array(
                    'maxSize' => '20480k'
                ))
            ),
            'mapped' => false
        ));

        $builder->add('description', TextareaType::class, array(
            'label' => 'TraineeRecordDocument.description.label',
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
        return 'TraineeRecordDocumentAddForm';
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
                'file',
                'description'
            ),
            'traineeRecord' => null
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
