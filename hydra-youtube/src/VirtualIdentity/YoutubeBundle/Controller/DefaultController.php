<?php
/*
 * This file is part of the Virtual-Identity Youtube package.
 *
 * (c) Virtual-Identity <dev.saga@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\YoutubeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use VirtualIdentity\YoutubeBundle\Form\ConfigurationEntity;
use VirtualIdentity\YoutubeBundle\Form\Type\ConfigurationEntityType;

class DefaultController extends Controller
{

    /**
     * @Route("/hydra/youtube/authorize", name="virtual_identity_youtube_authorize")
     */
    public function authorizeAction()
    {
        $service = $this->get('virtual_identity_youtube');

        $parameters = $service->getAuthorizationParameters(
            $this->generateUrl('virtual_identity_youtube_callbackurl', array(), true)
        );

        return new RedirectResponse($parameters['url']);
    }

    /**
     * @Route("/hydra/youtube/saveTokenInfo", name="virtual_identity_youtube_save_token_info")
     * @Template()
     */
    public function saveTokenInfoAction()
    {
        $service = $this->get('virtual_identity_youtube');

        $accessToken = $this->getRequest()->request->get('accessToken');
        $refreshToken = $this->getRequest()->request->get('refreshToken');
        $expiresIn = $this->getRequest()->request->get('expiresIn');
        $expireDate = time() + $expiresIn - 10;
        $this->get('logger')->debug(print_r($this->getRequest()->request,1));

        $hydraConfigFile = $this->get('kernel')->getRootDir().'/config/hydra.yml';

        if (file_exists($hydraConfigFile)) {
            $hydraConfig = Yaml::parse(file_get_contents($hydraConfigFile));
        } else {
            $hydraConfig = array();
        }

        $hydraConfig['virtual_identity_youtube']['token']         = $accessToken;
        $hydraConfig['virtual_identity_youtube']['refresh_token'] = $refreshToken;
        $hydraConfig['virtual_identity_youtube']['expire_date']   = $expireDate;

        // update runtime configuration
        $service->setAuthentication(
            isset($hydraConfig['virtual_identity_youtube']['consumer_key']) ? $hydraConfig['virtual_identity_youtube']['consumer_key'] : '',
            isset($hydraConfig['virtual_identity_youtube']['consumer_secret']) ? $hydraConfig['virtual_identity_youtube']['consumer_secret']: '',
            $accessToken
        );

        // save changes
        file_put_contents($hydraConfigFile, Yaml::dump($hydraConfig, 3));

        // clear cache
        $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($this->get('kernel'));
        $application->setAutoExit(false);
        $options = array('command' => 'cache:clear');
        $application->run(new \Symfony\Component\Console\Input\ArrayInput($options));

        return new Response('{"status":"ok"}');
    }

    /**
     * @Route("/hydra/youtube/callbackurl", name="virtual_identity_youtube_callbackurl")
     * @Template()
     */
    public function callbackAction()
    {
        $service = $this->get('virtual_identity_youtube');

        $code = $this->getRequest()->query->get('code');
        $callBackUrl = $this->generateUrl('virtual_identity_youtube_callbackurl', array(), true);

        return $service->getAccessToken($code, $callBackUrl);
    }

    /**
     * @Route("/hydra/youtube/moderate/{youtubeId}/{approved}", name="virtual_identity_youtube_moderate")
     * @Template()
     */
    public function moderateAction($youtubeId = null, $approved = null)
    {
        $service = $this->get('virtual_identity_youtube');

        if ($youtubeId !== null && $approved !== null && is_numeric($youtubeId) && ($approved == '1' || $approved == '0')) {
            $service->setApproved($youtubeId, (bool)$approved);
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
     * @Route("/hydra/youtube/sync", name="virtual_identity_youtube_sync")
     * @Template()
     */
    public function syncAction()
    {
        $service = $this->get('virtual_identity_youtube');

        $service->syncDatabase();

        return array(
            'credentialsValid' => $service->isAccessTokenValid()
        );
    }

    /**
     * @Route("/hydra/youtube", name="virtual_identity_youtube_configure")
     * @Template()
     */
    public function configureAction()
    {
        $service = $this->get('virtual_identity_youtube');

        $configurationEntity = new ConfigurationEntity();
        $configurationEntity->setApiRequests($this->container->getParameter('virtual_identity_youtube.api_requests'));
        $configurationEntity->setConsumerKey($this->container->getParameter('virtual_identity_youtube.consumer_key'));
        $configurationEntity->setConsumerSecret($this->container->getParameter('virtual_identity_youtube.consumer_secret'));
        $configurationEntity->setToken($this->container->getParameter('virtual_identity_youtube.token'));

        $form = $this->createForm(new ConfigurationEntityType(), $configurationEntity);

        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $hydraConfigFile = $this->get('kernel')->getRootDir().'/config/hydra.yml';

            if (file_exists($hydraConfigFile)) {
                $hydraConfig = Yaml::parse(file_get_contents($hydraConfigFile));
            } else {
                $hydraConfig = array();
            }

            $hydraConfig['virtual_identity_youtube']['api_requests']    = $configurationEntity->getApiRequests();
            $hydraConfig['virtual_identity_youtube']['consumer_key']    = $configurationEntity->getConsumerKey();
            $hydraConfig['virtual_identity_youtube']['consumer_secret'] = $configurationEntity->getConsumerSecret();
            $hydraConfig['virtual_identity_youtube']['token']           = $configurationEntity->getToken();

            // update runtime configuration
            $service->setAuthentication(
                $configurationEntity->getConsumerKey(),
                $configurationEntity->getConsumerSecret(),
                $configurationEntity->getToken()
            );
            $service->setApiRequests($configurationEntity->getApiRequests());

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
