<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Form\User;

use Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\RoleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
class UpdateRolesTForm extends AbstractType
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
        $builder->add('userRoles', EntityType::class, array(
            'label' => 'User.userRoles.label',
            'class' => 'IlcfrancePassportstagiaireDataBundle:Role',
            'query_builder' => function (RoleRepository $rr) {
                return $rr->createQueryBuilder('r')
                    ->orderBy('r.id', 'ASC');
            },
            'choice_label' => 'id',
            'multiple' => true,
            'by_reference' => false,
            'placeholder' => 'User.userRoles.placeholder'
        ));
    }

    /**
     *
     * {@inheritdoc} @see FormTypeInterface::getName()
     * @return string
     */
    public function getName()
    {
        return 'UserUpdateRolesForm';
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
                'userRoles'
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