<?php

namespace EntityBundle\Form;

use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SellerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sellername')
            ->add('rcs')
            ->add('taxnumber')
            ->add('tva')
            ->add('siren')
            ->add('fax')
            ->add('phonenumber')
            ->add('email')
           // ->add('image')
            ->add('file')
            ->add('captcha', CaptchaType::class)
            //   ->add('Product', EntityType::class,
            //       array(
            //         'class'=>'EntityBundle:Product',
            //       'choice_label'=>'id',
            //     'multiple'=>false,))
            ->add('Take me to the quiz!', SubmitType::class);
    }
     /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EntityBundle\Entity\Seller'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'entitybundle_seller';
    }


}
