<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Form\Homework;

use Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\LevelRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
        $builder->add('level', EntityType::class, array(
            'label' => 'Homework.level.label',
            'class' => 'IlcfrancePassportstagiaireDataBundle:Level',
            'query_builder' => function (LevelRepository $lr) {
                return $lr->createQueryBuilder('lr')
                    ->orderBy('lr.name', 'ASC');
            },
            'choice_label' => 'name',
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
        return 'HomeworkUpdateLevelForm';
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