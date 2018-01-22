<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Form\User;

use Ilcfrance\Passportstagiaire\DataBundle\Entity\Locale;
use Ilcfrance\Passportstagiaire\DataBundle\Entity\User;
use Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\LocaleRepository;
use Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\RoleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
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
            'label' => 'User.id.label'
        ));

        $builder->add('clearPassword', PasswordType::class, array(
            'label' => 'User.clearPassword.label'
        ));

        $builder->add('email', EmailType::class, array(
            'label' => 'User.email.label'
        ));

        $builder->add('sexe', ChoiceType::class, array(
            'label' => 'User.sexe.label',
            'choices' => User::choiceSexe(),
            'attr' => array(
                'choice_label_trans' => true
            ),
            'placeholder' => 'User.sexe.placeholder'
        ));

        $builder->add('lastName', TextType::class, array(
            'label' => 'User.lastName.label',
            'required' => false
        ));

        $builder->add('firstName', TextType::class, array(
            'label' => 'User.firstName.label',
            'required' => false
        ));

        $builder->add('lockout', ChoiceType::class, array(
            'label' => 'User.lockout.label',
            'choices' => User::choiceLockout(),
            'attr' => array(
                'choice_label_trans' => true
            ),
            'placeholder' => 'User.lockout.placeholder'
        ));

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

        $builder->add('locale', EntityType::class, array(
            'label' => 'User.locale.label',
            'class' => 'IlcfrancePassportstagiaireDataBundle:Locale',
            'query_builder' => function (LocaleRepository $lr) {
                return $lr->createQueryBuilder('l')
                    ->where('l.status = :status')
                    ->setParameter('status', Locale::ST_ENABLED)
                    ->orderBy('l.id', 'ASC');
            },
            'choice_label' => 'languageName',
            'multiple' => false,
            'by_reference' => false,
            'required' => false,
            'placeholder' => 'User.locale.placeholder',
            'empty_data' => null
        ));
    }

    /**
     *
     * {@inheritdoc} @see FormTypeInterface::getName()
     * @return string
     */
    public function getName()
    {
        return 'UserAddForm';
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
                'clearPassword',
                'email',
                'sexe',
                'lastName',
                'firstName',
                'lockout',
                'userRoles',
                'locale'
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