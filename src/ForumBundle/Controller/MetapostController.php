<?php

namespace ForumBundle\Controller;

use EntityBundle\Entity\Metapost;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Metapost controller.
 *
 * @Route("metapost")
 */
class MetapostController extends Controller
{
    /**
     * Lists all metapost entities.
     *
     * @Route("/", name="metapost_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $metaposts = $em->getRepository('EntityBundle:Metapost')->findAll();

        return $this->render('metapost/index.html.twig', array(
            'metaposts' => $metaposts,
        ));
    }

    /**
     * Creates a new metapost entity.
     *
     * @Route("/new", name="metapost_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $metapost = new Metapost();
        $form = $this->createForm('EntityBundle\Form\MetapostType', $metapost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($metapost);
            $em->flush();

            return $this->redirectToRoute('metapost_show', array('id' => $metapost->getId()));
        }

        return $this->render('metapost/new.html.twig', array(
            'metapost' => $metapost,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a metapost entity.
     *
     * @Route("/{id}", name="metapost_show")
     * @Method("GET")
     */
    public function showAction(Metapost $metapost)
    {
        $deleteForm = $this->createDeleteForm($metapost);

        return $this->render('metapost/show.html.twig', array(
            'metapost' => $metapost,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing metapost entity.
     *
     * @Route("/{id}/edit", name="metapost_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Metapost $metapost)
    {
        $deleteForm = $this->createDeleteForm($metapost);
        $editForm = $this->createForm('EntityBundle\Form\MetapostType', $metapost);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('metapost_edit', array('id' => $metapost->getId()));
        }

        return $this->render('metapost/edit.html.twig', array(
            'metapost' => $metapost,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a metapost entity.
     *
     * @Route("/{id}", name="metapost_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Metapost $metapost)
    {
        $form = $this->createDeleteForm($metapost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($metapost);
            $em->flush();
        }

        return $this->redirectToRoute('metapost_index');
    }

    /**
     * Creates a form to delete a metapost entity.
     *
     * @param Metapost $metapost The metapost entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Metapost $metapost)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('metapost_delete', array('id' => $metapost->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
