<?php

namespace UserBundle\Controller;
use UserBundle\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DefaultController extends Controller
{


    public function indexAction()
    {
            return $this->render('UserBundle:Default:index2.html.twig');
    }
}
