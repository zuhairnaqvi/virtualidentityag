<?php
/*
 * This file is part of the Virtual-Identity Social Media Aggregator package.
 *
 * (c) Virtual-Identity <development@virtual-identity.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VirtualIdentity\AggregatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use VirtualIdentity\TwitterBundle\DefaultController as TwitterController;

/**
 * This controller holds all actions required for easy administration
 * and moderation of the social media aggregator.
 *
 * Furthermore it holds an action that lets you validate your configuration.
 *
 * @author Matthias Steinboeck <matthias.steinboeck@gmail.com>
 */
class DefaultController extends Controller
{
    /**
     * @Route("/hydra")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/hydra/moderate/{unifiedId}/{approved}", name="virtual_identity_aggregator_moderate")
     * @Template()
     */
    public function moderateAction($unifiedId = null, $approved = null)
    {
        $service = $this->get('virtual_identity_aggregator');

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $service->getQueryBuilder(),
            $this->get('request')->query->get('page', 1), /*page number*/
            20 /*limit per page*/
        );

        return array(
            'feed' => $pagination
        );
    }

    /**
     * @Route("/hydra/update/approved/{unifiedId}/{approved}", name="virtual_identity_aggregator_update_approved", requirements={"unifiedId" = "\d+", "approved" = "[-]?\d"})
     * @Method({"POST"})
     */
    public function updateApprovedAction(Request $request, $unifiedId = null, $approved = null)
    {
        $service = $this->get('virtual_identity_aggregator');
        
        $approved = ($approved >= 0) ? (bool)$approved : null;
        $service->setApproved($unifiedId, $approved);
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array('status' => 'ok'));
        }
        return $this->forward('VirtualIdentityAggregatorBundle:Default:moderate');
    }

    /**
     * @Route("/hydra/sync", name="virtual_identity_aggregator_sync")
     * @Template()
     */
    public function syncAction()
    {
        $service = $this->get('virtual_identity_aggregator');

        $service->syncDatabase();

        return array();
    }
}
