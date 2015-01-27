<?php
/*
 * This file is part of the Virtual-Identity Youtube package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\YoutubeBundle\Form\Type;

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
            ->add('consumerKey', 'text', array('label' => 'Client Id'))
            ->add('consumerSecret', 'text', array('required' => false, 'label' => 'Client Secret'))
            ->add('token', 'text', array('required' => false))
            ->add('save', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VirtualIdentity\YoutubeBundle\Form\ConfigurationEntity',
        ));
    }

    public function getName()
    {
        return 'configurationEntity';
    }
}