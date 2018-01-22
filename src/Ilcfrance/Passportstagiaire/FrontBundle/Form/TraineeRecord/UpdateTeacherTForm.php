<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Form\TraineeRecord;

use Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
class UpdateTeacherTForm extends AbstractType
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
        $builder->add('teacher', EntityType::class, array(
            'label' => 'TraineeRecord.teacher.label',
            'class' => 'IlcfrancePassportstagiaireDataBundle:User',
            'query_builder' => function (UserRepository $ur) {
                return $ur->createQueryBuilder('u')
                    ->orderBy('u.id', 'ASC');
            },
            'choice_label' => 'fullName',
            'multiple' => false,
            'by_reference' => true,
            'required' => true
        ));
    }

    /**
     *
     * {@inheritdoc} @see FormTypeInterface::getName()
     * @return string
     */
    public function getName()
    {
        return 'TraineeRecordUpdateTeacherForm';
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
                'teacher'
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