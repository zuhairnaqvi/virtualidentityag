<?php
/*
 * This file is part of the Virtual-Identity Facebook package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\FacebookBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConfigurationEntityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'apiRequests',
                'collection',
                array(
                    'type' => 'text',
                    'allow_add' => true,
                    'allow_delete' => true
                )
            )
            ->add('appId')
            ->add('appSecret')
            ->add('permissions')
            ->add('token', 'text', array('required' => false))
            ->add('save', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VirtualIdentity\FacebookBundle\Form\ConfigurationEntity',
        ));
    }

    public function getName()
    {
        return 'configurationEntity';
    }
}