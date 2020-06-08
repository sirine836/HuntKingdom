<?php

namespace PanierBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use EntityBundle\Entity\lignePanier;
use EntityBundle\Entity\Panier;
use EntityBundle\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Panier controller.
 *
 */
class PanierController extends Controller
{
    /**
     * Lists all panier entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $paniers = $em->getRepository('EntityBundle:Panier')->findAll();

        return $this->render('@Panier/panier/index.html.twig', array(
            'paniers' => $paniers,
        ));
    }



    /**
     * Creates a new panier entity.
     *
     */
    public function newAction(Request $request)
    {
        $panier = new Panier();
        $form = $this->createForm('EntityBundle\Form\PanierType', $panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($panier);
            $em->flush();

            return $this->redirectToRoute('panier_show', array('id' => $panier->getId()));
        }
        return $this->render('@Panier/Panier/new.html.twig', array(
            'panier' => $panier,
            'form' => $form->createView(),
        ));
    }



    /**
     * Finds and displays a panier entity.
     *
     */
    public function showAction(Panier $panier)
    {
        $deleteForm = $this->createDeleteForm($panier);

        return $this->render('@Panier/Panier/show.html.twig', array(
            'panier' => $panier,
            'delete_form' => $deleteForm->createView(),
        ));
    }



    /**
     * Displays a form to edit an existing panier entity.
     *
     */
    public function editAction(Request $request, Panier $panier)
    {
        $deleteForm = $this->createDeleteForm($panier);
        $editForm = $this->createForm('EntityBundle\Form\PanierType', $panier);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('panier_edit', array('id' => $panier->getId()));
        }
        return $this->render('@Panier/Panier/edit.html.twig', array(
            'panier' => $panier,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }



    /**
     * Deletes a panier entity.
     *
     */
    public function deleteAction(Request $request, Panier $panier)
    {
        $form = $this->createDeleteForm($panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($panier);
            $em->flush();
        }
        return $this->redirectToRoute('panier_index');
    }



    /**
     * Creates a form to delete a panier entity.
     *
     * @param Panier $panier The panier entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Panier $panier)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('panier_delete', array('id' => $panier->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }



    public function panierAction()
    {
        $em = $this->getDoctrine()->getManager();
        $panier = $em->getRepository('EntityBundle:Panier')->findPanierDispoDQL($this->getUser()->getId());

        if($panier == null){
            $panier = $this->initialiserPanier();
        }else{
            $panier=$panier[0];
        }

        return $this->render('@Panier/panier/panier.html.twig', array(
            'panier' => $panier,
        ));
    }



    public function deleteProductAction(Request $request)
    {
        $lignePanierID=$request->get('id');

        $em = $this->getDoctrine()->getManager();
        $panierligne = $em->getRepository('EntityBundle:lignePanier')->find($lignePanierID);
        $panier = $em->getRepository('EntityBundle:Panier')->findPanierDispoDQL($this->getUser()->getId());
        $panier = $panier[0];
        $prixàSoustraire = $panierligne->getProduct()->getPrix() * $panierligne->getQuantite();
        $panier->setPrixTotal($panier->getPrixTotal() - $prixàSoustraire );
        $panierligne->getProduct()->setQuantity($panierligne->getProduct()->getQuantity()+$panierligne->getQuantite());
            $em->remove($panierligne);
            $em->flush();

        return $this->redirectToRoute('panier_mypanier');
    }



    public function addProductToPanierAction(Request $request)
    {
        $productID=$request->get('id');

        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('EntityBundle:Product')->find($productID);

        if($product->getQuantity() > 0 )
        {
            $lignePanier = $em->getRepository('EntityBundle:Panier')->findByProduct($productID,$this->getUser()->getId());
              if($lignePanier != null)
              {
                $lignePanier = $lignePanier[0];
                $this->incFunction($lignePanier);
              }else{
                $lignePanier = new lignePanier();
                $lignePanier->setProduct($product);
                $lignePanier->setQuantite(1);
                $product->setQuantity($product->getQuantity()-1);
              }
            $em->persist($lignePanier);
            $em->flush();
        }else{
           // finStock
            return $this->render('panier\finStock.html.twig');
        }
        $panier = $em->getRepository('EntityBundle:Panier')->findPanierDispoDQL($this->getUser()->getId());
        if($panier != null){

            $panier= $panier[0];
            $panier->getProducts()->add($lignePanier);
            $lignePanier->setPanier($panier);
            $panier->setPrixTotal($panier->getPrixTotal() + $product->getPrix());
        }else{
            $panier = $this->initialiserPanier();
            $panier->setPrixTotal($panier->getPrixTotal() + $product->getPrix());
            $lignePanier->setPanier($panier);
            $panier->getProducts()->add($lignePanier);
        }
        $em->flush();

        return $this->redirectToRoute('panier_mypanier');
    }



    public  function initialiserPanier()
    {
        $panier = new Panier();
        $panier->setEtat(false);
        $panier->setUser($this->getUser());
        $panier->setArchive(false);
        $panier->setDatePanier(new \DateTime);
        $panier->setProducts(new ArrayCollection());
        $panier->setPrixTotal(0);
        $em = $this->getDoctrine()->getManager();
        $em->persist($panier);
        $em->flush();

        return $panier;
    }



    public function increaseProductQTEAction(Request $request)
    {
        $lignePanierID=$request->get('id');
        $em = $this->getDoctrine()->getManager();
        $panierligne = $em->getRepository('EntityBundle:lignePanier')->find($lignePanierID);

        if($panierligne->getProduct()->getQuantity() > 0 )
        {
            $this->incFunction($panierligne);
        }else{
            // finStock
            return $this->render('@Panier\panier\finStock.html.twig');
        }
        return $this->redirectToRoute('panier_mypanier');
    }



    public function incFunction(lignePanier $panierligne)
    {
            $panierligne->setQuantite($panierligne->getQuantite()+1);
            $em = $this->getDoctrine()->getManager();
            $panier = $em->getRepository('EntityBundle:Panier')->findPanierDispoDQL($this->getUser()->getId());
            $panier = $panier[0];
            $prixàAjoute = $panierligne->getProduct()->getPrix();
            $panier->setPrixTotal($panier->getPrixTotal() + $prixàAjoute);
            $panierligne->getProduct()->setQuantity($panierligne->getProduct()->getQuantity()-1);

            $em->flush();
    }


    public function DecreaseProductQTEAction(Request $request)
    {
        $lignePanierID=$request->get('id');
        $em = $this->getDoctrine()->getManager();
        $panierligne = $em->getRepository('EntityBundle:lignePanier')->find($lignePanierID);
        $panierligne->setQuantite($panierligne->getQuantite()-1);

             while ($panierligne->getQuantite()>=1) {
                 $panier = $em->getRepository('EntityBundle:Panier')->findPanierDispoDQL($this->getUser()->getId());
                 $panier = $panier[0];
                 $prixàSoustraire = $panierligne->getProduct()->getPrix();
                 $panier->setPrixTotal($panier->getPrixTotal() - $prixàSoustraire);
                 $panierligne->getProduct()->setQuantity($panierligne->getProduct()->getQuantity()+1);
                 $em->flush();

                 return $this->redirectToRoute('panier_mypanier');
             }
          $this->addFlash('info', "quantite invalide");

        return $this->redirectToRoute('panier_mypanier');
    }





    public function payerAction(Request $request)
    {
        $factureID=$request->get('id');
        $em = $this->getDoctrine()->getManager();
        $facture = $em->getRepository('EntityBundle:Facture')->find($factureID);
        $facture->setEtat(true);
        $panier = $facture->getPanier();
        $panier->setArchive(true);
        //dump($facture);
        //exit();
        \Stripe\Stripe::setApiKey("sk_test_sM3fCA57AXRf30HBdPYXDmY80083NDeCJu");

        \Stripe\Charge::create(array(
            "amount" => $panier->getPrixTotal(),
            "currency" => "usd",
            "source" => 'tok_mastercard', // obtained with Stripe.js
            "description" => "Payer"
        ));

//        $panier->setPrixtotal(0);
//        $el->flush();
//        $ecc = $this->getDoctrine()->getManager();
//        foreach ($ec as $c) {
//            $ecc->clear($c);
//            $ecc->flush();
//        }
        $em->persist($panier);
        $em->persist($facture);
        $em->flush();

        return $this->render('@Panier/panier/payment.html.twig');
    }





}
