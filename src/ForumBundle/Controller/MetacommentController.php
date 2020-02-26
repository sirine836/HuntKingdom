<?php

namespace ForumBundle\Controller;

use EntityBundle\Entity\Metacomment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Metacomment controller.
 *
 * @Route("metacomment")
 */
class MetacommentController extends Controller
{
    /**
     * Lists all metacomment entities.
     *
     * @Route("/", name="metacomment_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $metacomments = $em->getRepository('EntityBundle:Metacomment')->findAll();

        return $this->render('metacomment/index.html.twig', array(
            'metacomments' => $metacomments,
        ));
    }

    /**
     * Creates a new metacomment entity.
     *
     * @Route("/new", name="metacomment_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $metacomment = new Metacomment();
        $form = $this->createForm('EntityBundle\Form\MetacommentType', $metacomment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($metacomment);
            $em->flush();

            return $this->redirectToRoute('metacomment_show', array('id' => $metacomment->getId()));
        }

        return $this->render('metacomment/new.html.twig', array(
            'metacomment' => $metacomment,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a metacomment entity.
     *
     * @Route("/{id}", name="metacomment_show")
     * @Method("GET")
     */
    public function showAction(Metacomment $metacomment)
    {
        $deleteForm = $this->createDeleteForm($metacomment);

        return $this->render('metacomment/show.html.twig', array(
            'metacomment' => $metacomment,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing metacomment entity.
     *
     * @Route("/{id}/edit", name="metacomment_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Metacomment $metacomment)
    {
        $deleteForm = $this->createDeleteForm($metacomment);
        $editForm = $this->createForm('EntityBundle\Form\MetacommentType', $metacomment);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('metacomment_edit', array('id' => $metacomment->getId()));
        }

        return $this->render('metacomment/edit.html.twig', array(
            'metacomment' => $metacomment,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a metacomment entity.
     *
     * @Route("/{id}", name="metacomment_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Metacomment $metacomment)
    {
        $form = $this->createDeleteForm($metacomment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($metacomment);
            $em->flush();
        }

        return $this->redirectToRoute('metacomment_index');
    }

    /**
     * Creates a form to delete a metacomment entity.
     *
     * @param Metacomment $metacomment The metacomment entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Metacomment $metacomment)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('metacomment_delete', array('id' => $metacomment->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
