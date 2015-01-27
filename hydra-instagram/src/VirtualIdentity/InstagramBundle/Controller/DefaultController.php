<?php
/*
 * This file is part of the Virtual-Identity Instagram package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\InstagramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use VirtualIdentity\InstagramBundle\Form\ConfigurationEntity;
use VirtualIdentity\InstagramBundle\Form\Type\ConfigurationEntityType;

class DefaultController extends Controller
{

    /**
     * @Route("/hydra/instagram/authorize", name="virtual_identity_instagram_authorize")
     */
    public function authorizeAction()
    {
        $service = $this->get('virtual_identity_instagram');

        $parameters = $service->getAuthorizationParameters(
            $this->generateUrl('virtual_identity_instagram_callbackurl', array(), true)
        );

        return new RedirectResponse($parameters['url']);
    }

    /**
     * @Route("/hydra/instagram/callbackurl", name="virtual_identity_instagram_callbackurl")
     * @Template()
     */
    public function callbackAction()
    {
        $service = $this->get('virtual_identity_instagram');

        $code = $this->getRequest()->query->get('code');
        $callBackUrl = $this->generateUrl('virtual_identity_instagram_callbackurl', array(), true);

        return $service->getAccessToken($code, $callBackUrl);
    }

    /**
     * @Route("/hydra/instagram/moderate/{instagramId}/{approved}", name="virtual_identity_instagram_moderate")
     * @Template()
     */
    public function moderateAction($instagramId = null, $approved = null)
    {
        $service = $this->get('virtual_identity_instagram');

        if ($instagramId !== null && $approved !== null && is_numeric($instagramId) && ($approved == '1' || $approved == '0')) {
            $service->setApproved($instagramId, (bool)$approved);
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $service->getQueryBuilder(),
            $this->get('request')->query->get('page', 1), /*page number*/
            20 /*limit per page*/
        );

        return array(
            'credentialsValid' => $service->isAccessTokenValid(),
            'feed' => $pagination
        );
    }

    /**
     * @Route("/hydra/instagram/sync", name="virtual_identity_instagram_sync")
     * @Template()
     */
    public function syncAction()
    {
        $service = $this->get('virtual_identity_instagram');

        $service->syncDatabase();

        return array(
            'credentialsValid' => $service->isAccessTokenValid()
        );
    }

    /**
     * @Route("/hydra/instagram", name="virtual_identity_instagram_configure")
     * @Template()
     */
    public function configureAction()
    {
        $service = $this->get('virtual_identity_instagram');

        $configurationEntity = new ConfigurationEntity();
        $configurationEntity->setApiRequests($this->container->getParameter('virtual_identity_instagram.api_requests'));
        $configurationEntity->setConsumerKey($this->container->getParameter('virtual_identity_instagram.consumer_key'));
        $configurationEntity->setConsumerSecret($this->container->getParameter('virtual_identity_instagram.consumer_secret'));
        $configurationEntity->setToken($this->container->getParameter('virtual_identity_instagram.token'));

        $form = $this->createForm(new ConfigurationEntityType(), $configurationEntity);

        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $hydraConfigFile = $this->get('kernel')->getRootDir().'/config/hydra.yml';

            if (file_exists($hydraConfigFile)) {
                $hydraConfig = Yaml::parse(file_get_contents($hydraConfigFile));
            } else {
                $hydraConfig = array();
            }

            $hydraConfig['virtual_identity_instagram']['api_requests']    = $configurationEntity->getApiRequests();
            $hydraConfig['virtual_identity_instagram']['consumer_key']    = $configurationEntity->getConsumerKey();
            $hydraConfig['virtual_identity_instagram']['consumer_secret'] = $configurationEntity->getConsumerSecret();
            $hydraConfig['virtual_identity_instagram']['token']           = $configurationEntity->getToken();

            // update runtime configuration
            $service->setAuthentication(
                $configurationEntity->getConsumerKey(),
                $configurationEntity->getConsumerSecret(),
                $configurationEntity->getToken()
            );
            $service->setApiRequests($configurationEntity->getApiRequests());
            
            // Single % in YML not allowed => Replace with %%
            array_walk($hydraConfig['virtual_identity_instagram']['api_requests'], function(&$r) { 
                $r = str_replace ("%", "%%", $r);
            });

            // save changes
            file_put_contents($hydraConfigFile, Yaml::dump($hydraConfig, 3));

            // clear cache
            // $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($this->get('kernel'));
            // $application->setAutoExit(false);
            // $options = array('command' => 'cache:clear');
            // $application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
        }

        return array(
            'credentialsValid' => $service->isAccessTokenValid(),
            'form' => $form->createView()
        );
    }
}
