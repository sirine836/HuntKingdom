<?php

namespace PanierBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EntityBundle\Entity\Panier;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('@Panier\Default\index.html.twig');
    }


    public function displayAction()
    {
        $em = $this->getDoctrine()->getManager();
        $panier = $em->getRepository('EntityBundle:Panier')->findPanierDispoDQL($this->getUser()->getId());
        $prixTotal=$panier[0]->getPrixTotal();

        return($this->render('@Panier\Default\index.html.twig',array(
            'prixTotal'=> $prixTotal,
        )));
    }


    public function displayNBRAction()
    {
        $em = $this->getDoctrine()->getManager();
        $lignespanier = $em->getRepository('EntityBundle:Panier')->findDQL($this->getUser()->getId());
        $nbrproduits=count($lignespanier);

        return($this->render('@Panier\Default\nbrproduits.html.twig',array(
            'nbrproduits'=> $nbrproduits,
        )));
    }

}
