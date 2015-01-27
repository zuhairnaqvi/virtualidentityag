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

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class RequestEntityType extends AbstractType
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
        $orderFieldOps = array();

        $builder
            ->add('id')
            ->add('url', 'text', array(
                'label' => 'Request'
            ))
            ->add('mappedEntity', 'choice', array(
                'choices' => $this->getEntities(),
                'preferred_choices' => array('VirtualIdentity\TwitterBundle\Entity\TwitterEntity')
            ))
            ->add('refreshLifeTime', 'integer')
            ->add('orderField', 'choice', $orderFieldOps)
            ->add('useSinceId', 'checkbox', array(
                'label' => 'Use since_id',
                'required' => false
            ))
        ;

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function(FormEvent $event) use($orderFieldOps) {
                $form = $event->getForm();

                // entity
                $data = $event->getData();
                // we cant get the data from the event, because it hasnt been binded yet
                // we have to get it from the request

                $entityClass = $data['mappedEntity'];

                $methods = get_class_methods($entityClass);
                $getters = array_filter($methods, function($m) { return substr($m, 0,3) == 'get' ; });
                $lcFields = array_map(function($g){ return lcfirst(substr($g, 3)); }, $getters);
                $fields = array();
                foreach ($lcFields as $k => $v) $fields[$v] = $v;

                $ops = array_merge($orderFieldOps, array(
                    'choices' => $fields
                ));
                $form->add('orderField', 'choice', $ops);
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function(FormEvent $event) use ($orderFieldOps) {
                $form = $event->getForm();

                // entity
                $data = $event->getData();
                if ($data === null) {
                    return;
                }

                $entityClass = $data->getMappedEntity();

                $methods = get_class_methods($entityClass);
                $getters = array_filter($methods, function($m) { return substr($m, 0,3) == 'get' ; });
                $lcFields = array_map(function($g){ return lcfirst(substr($g, 3)); }, $getters);
                $fields = array();
                foreach ($lcFields as $k => $v) $fields[$v] = $v;

                $ops = array_merge($orderFieldOps, array(
                    'choices' => $fields
                ));
                $form->add('orderField', 'choice', $ops);
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VirtualIdentity\TwitterBundle\Entity\TwitterRequestEntity',
            'refreshLifeTime' => 54000
        ));
    }

    protected function getEntities()
    {
        return $this->entities;
    }

    public function getName()
    {
        return 'request';
    }
}