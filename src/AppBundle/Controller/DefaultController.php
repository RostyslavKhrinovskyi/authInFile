<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\User;



class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('default/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('homepage'));
        }

        $session = $this->get('session');

        $authenticationUtils = $this->get('security.authentication_utils');

        $error = $authenticationUtils->getLastAuthenticationError();

        if ($error) {

            if (date('U') > $session->get('time') + $this->getParameter('limit_seconds_login')) {
                $session->set('limit', $this->getParameter('limit_try_login'));
            }

            $session->set('time', date('U'));

            $limit = $session->get('limit');
            $limit--;

            if ($limit < 0) {
                $countSeconds = $session->get('time') + $this->getParameter('limit_seconds_login') - date('U');
                $errorMessage = 'Try after ' . $countSeconds . ' seconds';
                $error = new CustomUserMessageAuthenticationException($errorMessage);
            }

            $session->set('limit', $limit);
        }


        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('default/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

}
