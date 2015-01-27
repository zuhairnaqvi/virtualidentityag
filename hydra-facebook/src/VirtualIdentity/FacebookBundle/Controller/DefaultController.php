<?php
/*
 * This file is part of the Virtual-Identity Facebook package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\FacebookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use VirtualIdentity\FacebookBundle\Form\ConfigurationEntity;
use VirtualIdentity\FacebookBundle\Form\Type\ConfigurationEntityType;

class DefaultController extends Controller
{

    /**
     * @Route("/hydra/facebook/authorize", name="virtual_identity_facebook_authorize")
     */
    public function authorizeAction()
    {
        $service = $this->get('virtual_identity_facebook');

        $parameters = $service->getAuthorizationParameters(
            $this->generateUrl('virtual_identity_facebook_callbackurl', array(), true)
        );

        return new RedirectResponse($parameters['url']);
    }

    /**
     * @Route("/hydra/facebook/callbackurl", name="virtual_identity_facebook_callbackurl")
     * @Template()
     */
    public function callbackAction()
    {
        $service = $this->get('virtual_identity_facebook');

        $shortTermAccessToken = $this->getRequest()->query->get('shortTermAccessToken');

        $tokenInfo = $service->getAccessToken($shortTermAccessToken);

        $response = new Response();
        $response->setContent(json_encode(array(
            'longTermAccessToken' => $tokenInfo['accessToken']
        )));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/hydra/facebook/moderate/{facebookId}/{approved}", name="virtual_identity_facebook_moderate")
     * @Template()
     */
    public function moderateAction($facebookId = null, $approved = null)
    {
        $service = $this->get('virtual_identity_facebook');

        if ($facebookId !== null && $approved !== null && is_numeric($facebookId) && ($approved == '1' || $approved == '0')) {
            $service->setApproved($facebookId, (bool)$approved);
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
     * @Route("/hydra/facebook/sync", name="virtual_identity_facebook_sync")
     * @Template()
     */
    public function syncAction()
    {
        $service = $this->get('virtual_identity_facebook');

        $service->syncDatabase();

        return array(
            'credentialsValid' => $service->isAccessTokenValid()
        );
    }

    /**
     * @Route("/hydra/facebook", name="virtual_identity_facebook_configure")
     * @Template()
     */
    public function configureAction()
    {
        $service = $this->get('virtual_identity_facebook');

        $configurationEntity = new ConfigurationEntity();
        $configurationEntity->setApiRequests($this->container->getParameter('virtual_identity_facebook.api_requests'));
        $configurationEntity->setAppId($this->container->getParameter('virtual_identity_facebook.app_id'));
        $configurationEntity->setAppSecret($this->container->getParameter('virtual_identity_facebook.app_secret'));
        $configurationEntity->setToken($this->container->getParameter('virtual_identity_facebook.token'));
        $configurationEntity->setPermissions($this->container->getParameter('virtual_identity_facebook.permissions'));

        $form = $this->createForm(new ConfigurationEntityType(), $configurationEntity);

        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $hydraConfigFile = $this->get('kernel')->getRootDir().'/config/hydra.yml';

            if (file_exists($hydraConfigFile)) {
                $hydraConfig = Yaml::parse(file_get_contents($hydraConfigFile));
            } else {
                $hydraConfig = array();
            }

            $hydraConfig['virtual_identity_facebook']['api_requests']    = $configurationEntity->getApiRequests();
            $hydraConfig['virtual_identity_facebook']['app_id']          = $configurationEntity->getAppId();
            $hydraConfig['virtual_identity_facebook']['app_secret']      = $configurationEntity->getAppSecret();
            $hydraConfig['virtual_identity_facebook']['token']           = $configurationEntity->getToken();
            $hydraConfig['virtual_identity_facebook']['permissions']     = $configurationEntity->getPermissions();

            // update runtime configuration
            $service->setAuthentication(
                $configurationEntity->getAppId(),
                $configurationEntity->getAppSecret(),
                $configurationEntity->getToken()
            );
            $service->setApiRequests($configurationEntity->getApiRequests());
            
            array_walk($hydraConfig['virtual_identity_facebook']['api_requests'], function(&$r) { 
                $r = str_replace ("%", "%%", $r);
            });            

            // save changes
            file_put_contents($hydraConfigFile, Yaml::dump($hydraConfig, 3));

            // clear cache
            $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($this->get('kernel'));
            $application->setAutoExit(false);
            $options = array('command' => 'cache:clear');
            $application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
        }

        return array(
            'credentialsValid' => $service->isAccessTokenValid(),
            'form' => $form->createView()
        );
    }
}
