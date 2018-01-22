<?php
namespace Ilcfrance\Passportstagiaire\FrontBundle\Form\UserPicture;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @author sasedev <sinus@saseprod.net>
 */
class CropPictureTForm extends AbstractType
{

    private $filename;

    /**
     * Form builder
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->filename = $options['filename'];

        $builder->add('x1', HiddenType::class, array(
            'required' => true
        ));

        $builder->add('y1', HiddenType::class, array(
            'required' => true
        ));

        $builder->add('w', HiddenType::class, array(
            'required' => true
        ));

        $builder->add('h', HiddenType::class, array(
            'required' => true
        ));

        $builder->add('avatar_tmp', HiddenType::class, array(
            'data' => $this->filename,
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
        return 'UserPictureCropPictureForm';
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
                'Default'
            ),
            'filename' => null
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