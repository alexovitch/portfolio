<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ));
    }

    /**
     * @Route("/secure", name="secure")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function secureAction(Request $request)
    {


        return $this->render('default/secure.html.twig', array(
            'type' =>'blog'));
    }

    /**
     * @Route("/info", name="info")
     */
    public function infoAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/info.html.twig', []);
    }
}
