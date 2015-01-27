<?php
/*
 * This file is part of the Virtual-Identity Twitter package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\TwitterBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConfigurationEntityType extends AbstractType
{
    /**
     * folders that are searched for doctrine entities. those entities are then
     * listed as possible mappings for responses from the twitter api
     *
     * @var array
     */
    protected $entities;

    public function __construct(array $entities)
    {
        $this->entities = $entities;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'apiRequests',
                'collection',
                array(
                    'type' => new RequestEntityType($this->entities),
                    'allow_add' => true,
                    'allow_delete' => true
                )
            )
            ->add('consumerKey')
            ->add('consumerSecret')
            ->add('token', 'text', array('required' => false))
            ->add('secret', 'text', array('required' => false))
            ->add('save', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VirtualIdentity\TwitterBundle\Form\ConfigurationEntity',
            'cascade_validation' => true
        ));
    }

    public function getName()
    {
        return 'configurationEntity';
    }
}