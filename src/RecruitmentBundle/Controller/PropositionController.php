<?php

namespace RecruitmentBundle\Controller;

use EntityBundle\Entity\Proposition;
use EntityBundle\Entity\Question;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Proposition controller.
 *
 */
class PropositionController extends Controller
{
    /**
     * Lists all proposition entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $propositions = $em->getRepository('EntityBundle:Proposition')->findAll();

        return $this->render('proposition/index.html.twig', array(
            'propositions' => $propositions,
        ));
    }



    public function clearCreateAction()
    {
        return $this->redirectToRoute('question_new');
    }

    /**
     * Creates a new proposition entity.
     *
     */
    public function newAction(Request $request,Question $question)
    {
        $proposition = new Proposition();
        $form = $this->createForm('EntityBundle\Form\PropositionType', $proposition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $proposition->setQuestion($question);
            $em->persist($proposition);
            $em->flush();

            return $this->redirectToRoute('proposition_new', array('id' => $question->getId()));
        }

        return $this->render('proposition/new.html.twig', array(
            'proposition' => $proposition,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a proposition entity.
     *
     */
    public function showAction(Proposition $proposition)
    {
        $deleteForm = $this->createDeleteForm($proposition);

        return $this->render('proposition/show.html.twig', array(
            'proposition' => $proposition,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing proposition entity.
     *
     */
    public function editAction(Request $request, Proposition $proposition)
    {
        $deleteForm = $this->createDeleteForm($proposition);
        $editForm = $this->createForm('EntityBundle\Form\PropositionType', $proposition);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('proposition_edit', array('id' => $proposition->getId()));
        }

        return $this->render('proposition/edit.html.twig', array(
            'proposition' => $proposition,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a proposition entity.
     *
     */
    public function deleteAction(Request $request, Proposition $proposition)
    {
        $form = $this->createDeleteForm($proposition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($proposition);
            $em->flush();
        }

        return $this->redirectToRoute('proposition_index');
    }

    /**
     * Creates a form to delete a proposition entity.
     *
     * @param Proposition $proposition The proposition entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Proposition $proposition)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('proposition_delete', array('id' => $proposition->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
