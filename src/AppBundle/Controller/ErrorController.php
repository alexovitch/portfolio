<?php
/**
 * Created by PhpStorm.
 * User: alexis
 * Date: 14/06/2016
 * Time: 18:29
 */

namespace AppBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * User controller.
 *
 * @Route("/error")
 */
class ErrorController extends Controller

{
    /**
     * Lists all User entities.
     *
     * @Route("/", name="error_index")
     * @Method("GET")
     */
    public function indexAction()
    {
//        throw new AccessDeniedException("You don't have access to this page!");

//        $response = new Response();
//        $response->setStatusCode(500);
//        return $response;

        return $this->render('error/index.html.twig', array(

        ));
    }
}