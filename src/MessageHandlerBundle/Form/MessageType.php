<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 24/8/2019
 * Time: 05:43
 */

namespace MessageHandlerBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * Class MessageType
 * @package MessageHandlerBundle\Form
 */
class MessageType extends  AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('xml', null, array('description' => 'Xml Body'))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MessageHandlerBundle\Entity\Message',
            'csrf_protection' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return '';
    }
}