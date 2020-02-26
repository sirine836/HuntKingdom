<?php

namespace RecruitmentBundle\Controller;

use EntityBundle\Entity\Answer;
use EntityBundle\Entity\Question;
use http\Env\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Question controller.
 *
 */
class QuestionController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $questions = $em->getRepository('EntityBundle:Question')->findAll();

        return $this->render('question/index.html.twig', array(
            'questions' => $questions,
        ));
    }
    public function ContinueAction()
    {
        $em = $this->getDoctrine()->getManager();

        $questions = $em->getRepository('EntityBundle:Question')->findAll();

        return $this->render('question/continue.html.twig', array(
            'questions' => $questions,
        ));
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function answerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $questions = $em->getRepository('EntityBundle:Question')->findAll();
        $ans = $em->getRepository('EntityBundle:Answer')->findBy(array("user"=>$user->getId()));
        if( empty($ans)){
            if($request->isMethod("POST")){
                foreach ($questions as $q){
                    $score = 0 ;
                    if($request->get($q->getId() .'')){
                        $prop   = $em->getRepository('EntityBundle:Proposition')->find($request->get($q->getId() .''));
                        if($prop->getState()){
                            $score ++;
                        }
                    }
                    $answer = new Answer();
                    $answer->setQuestion($q);
                    $answer->setScore($score);
                    $answer->setUser($user);
                    $em->persist($answer);
                    $em->flush();
                }
                return $this->redirectToRoute('question_continue');
               // return $this->render('question/continue.html.twig');
            }
            else {
                return $this->render('question/answer.html.twig', array( //answers registred
                    'questions' => $questions,
                ));
            }
        }else {
            return $this->render('question/oups.html.twig'); //ma3andksh l7a9 t3awed
        }
    }

    /**
     * Creates a new question entity.
     *
     */
    public function newAction(Request $request)
    {
        $question = new Question();
        $form = $this->createForm('EntityBundle\Form\QuestionType', $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($question);
            $em->flush();
            // redirect To route proposition
            return $this->redirectToRoute('proposition_new', array('id' => $question->getId()));
        }

        return $this->render('question/new.html.twig', array(
            'question' => $question,
            'form' => $form->createView(),
        ));
    }

    public function showAction(Question $question)
    {
        $deleteForm = $this->createDeleteForm($question);

        return $this->render('question/show.html.twig', array(
            'question' => $question,
            'delete_form' => $deleteForm->createView(),
        ));
    }
/*
    public function editAction(Request $request, Question $question)
    {
        $deleteForm = $this->createDeleteForm($question);
        $editForm = $this->createForm('EntityBundle\Form\QuestionType', $question);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('question_edit', array('id' => $question->getId()));
        }

        return $this->render('question/edit.html.twig', array(
            'question' => $question,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    public function deleteAction(Request $request, Question $question)
    {
        $form = $this->createDeleteForm($question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($question);
            $em->flush();
        }

        return $this->redirectToRoute('question_index');
    }

    private function createDeleteForm(Question $question)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('question_delete', array('id' => $question->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }*/
}
