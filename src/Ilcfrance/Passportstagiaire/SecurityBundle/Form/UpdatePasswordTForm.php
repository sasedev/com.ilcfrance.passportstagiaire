<?php
namespace Ilcfrance\Passportstagiaire\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
class UpdatePasswordTForm extends AbstractType
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
        $builder->add('oldPassword', PasswordType::class, array(
            'label' => 'User.oldPassword.label',
            'mapped' => false
        ));

        $builder->add('clearPassword', RepeatedType::class, array(
            'type' => PasswordType::class,
            'invalid_message' => 'User.newPassword.repeat.notequal',
            'first_options' => array(
                'label' => 'User.newPassword.first',
                'attr' => array(
                    'label_col' => 3,
                    'widget_col' => 5
                )
            ),
            'second_options' => array(
                'label' => 'User.newPassword.second',
                'attr' => array(
                    'label_col' => 3,
                    'widget_col' => 5
                )
            )
        ));
    }

    public function getName()
    {
        return 'UserUpdatePasswordForm';
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
                'clearPassword'
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