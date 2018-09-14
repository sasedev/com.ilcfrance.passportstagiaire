<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Form\Homework;

use Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\LevelRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

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

        $builder->add('file', FileType::class, array(
            'label' => 'Homework.fileName.label',
            'constraints' => array(
                new File(array(
                    'maxSize' => '20480k'
                ))
            ),
            'mapped' => false
        ));

        $builder->add('description', TextType::class, array(
            'label' => 'Homework.description.label',
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
        return 'HomeworkAddForm';
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
                'level',
                'file',
                'description'
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
