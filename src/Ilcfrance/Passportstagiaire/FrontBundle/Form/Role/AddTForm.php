<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Form\Role;

use Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\RoleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        $builder->add('id', TextType::class, array(
            'label' => 'Role.id.label'
        ));

        $builder->add('description', TextareaType::class, array(
            'label' => 'Role.description.label',
            'required' => false
        ));

        $builder->add('parents', EntityType::class, array(
            'label' => 'Role.parents.label',
            'class' => 'IlcfrancePassportstagiaireDataBundle:Role',
            'query_builder' => function (RoleRepository $rr) {
                return $rr->createQueryBuilder('r')
                    ->orderBy('r.id', 'ASC');
            },
            'choice_label' => 'id',
            'multiple' => true,
            'by_reference' => false,
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
        return 'RoleAddForm';
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
                'id',
                'description',
                'parents'
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