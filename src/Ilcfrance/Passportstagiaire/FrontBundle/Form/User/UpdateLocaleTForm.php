<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Form\User;

use Ilcfrance\Passportstagiaire\DataBundle\Entity\Locale;
use Ilcfrance\Passportstagiaire\DataBundle\EntityRepository\LocaleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
class UpdateLocaleTForm extends AbstractType
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
            'by_reference' => true,
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
        return 'UserUpdateLocaleForm';
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