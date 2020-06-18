<?php

namespace PanierBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use EntityBundle\Entity\Facture;
use EntityBundle\Entity\Panier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use EntityBundle\Form;

/**
 * Facture controller.
 *
 */
class FactureController extends Controller
{
    /**
     * Lists all facture entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $factures = $em->getRepository('EntityBundle:Facture')->findAll();

        return $this->render('@Panier/Facture/index.html.twig', array(
            'factures' => $factures,
        ));
    }



    public function historiqueIndexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $factures = $em->getRepository('EntityBundle:Facture')->findByUSer($this->getUser()->getId());

        return $this->render('@Panier/Facture/indexUser.html.twig', array(
            'factures' => $factures,
        ));
    }



    /**
     * Creates a new facture entity.
     *
     */
    public function newAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
           $facture = new Facture();
            $form = $this->createForm('EntityBundle\Form\FactureType', $facture);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $panier = $em->getRepository('EntityBundle:Panier')->findPanierDispoDQL($this->getUser()->getId());
                $panier = $panier[0];
                $panier->setEtat(true);
                $panier->setArchive(false);
                $facture->setEtat(false);
                $facture->setPanier($panier);
                $em->persist($facture);
                $em->persist($panier);
                $em->flush();

                return $this->redirectToRoute('facture_show', array('id' => $facture->getId()));


        }
        return $this->render('@Panier/Facture/new.html.twig', array(
            'facture' => $facture,
            'form' => $form->createView(),
        ));
       }



    /**
     * Finds and displays a facture entity.
     *
     */
    public function showAction(Facture $facture)
    {
        $deleteForm = $this->createDeleteForm($facture);
        return $this->render('@Panier\Facture\show.html.twig', array(
            'facture' => $facture,
            'delete_form' => $deleteForm->createView(),
        ));
    }



    /**
     * Displays a form to edit an existing facture entity.
     *
     */
    public function editAction(Request $request, Facture $facture)
    {
        $deleteForm = $this->createDeleteForm($facture);
        $editForm = $this->createForm('EntityBundle\Form\FactureType', $facture);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('facture_edit', array('id' => $facture->getId()));
        }

        return $this->render('@Panier/Facture/edit.html.twig', array(
            'facture' => $facture,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }



    /**
     * Deletes a facture entity.
     *
     */
    public function deleteAction(Request $request, Facture $facture)
    {
        $form = $this->createDeleteForm($facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($facture);
            $em->flush();
        }

        return $this->redirectToRoute('facture_index');
    }



    /**
     * Creates a form to delete a facture entity.
     *
     * @param Facture $facture The facture entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Facture $facture)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('facture_delete', array('id' => $facture->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
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


}
