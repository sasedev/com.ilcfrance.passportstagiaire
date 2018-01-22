<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Form\Role;

use Ilcfrance\Passportstagiaire\DataBundle\Entity\Role;
use Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\RoleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
class UpdateParentsTForm extends AbstractType
{

    /**
     *
     * @var Role
     */
    private $role;

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
        $this->role = $options['role'];
        $role = $this->role;

        $builder->add('parents', EntityType::class, array(
            'label' => 'Role.parents.label',
            'class' => 'IlcfrancePassportstagiaireDataBundle:Role',
            'query_builder' => function (RoleRepository $rr) use ($role) {
                return $rr->createQueryBuilder('r')
                    ->where('r.id != :id')
                    ->setParameter('id', $role->getId())
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
        return 'RoleUpdateParentsForm';
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
                'description'
            ),
            'role' => null
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