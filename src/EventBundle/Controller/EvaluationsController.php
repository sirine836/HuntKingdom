<?php

namespace EventBundle\Controller;

use EntityBundle\Entity\Evaluations;
use EntityBundle\Entity\Events;
use EntityBundle\Entity\User;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Evaluation controller.
 *
 */
class EvaluationsController extends Controller
{
    /**
     * Lists all evaluation entities.
     *
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $evaluations = $em->getRepository('EntityBundle:Evaluations')->findAll();

        return $this->render('evaluations/index.html.twig', array(
            'evaluations' => $evaluations,
        ));
    }

    public function listAction()
    {
        $ob = new Highchart();
        $ob->chart->renderTo('pichart');
        $ob->title->text('Evaluations des Evenements');
        $ob->plotOptions->pie(array(
            'allowPointSelect'  => true,
            'cursor'    => 'pointer',
            'dataLabels'    => array('enabled' => false),
            'showInLegend'  => true
        ));
        $em = $this->getDoctrine()->getManager();
        $query = $em-> createQuery('SELECT AVG(p.note) as Note, (c.titre) as Titre FROM  EntityBundle:Evaluations p , EntityBundle:Events c where (p.Events = c.id) group by p.Events');
        $resultat = $query->getResult();
        $data = array();
        foreach ($resultat as $values)
        {
            $a = array($values['Titre'], intval($values['Note']));
            array_push($data,$a);
        }
        $ob->series(array(array('type' => 'pie','name' => 'Note', 'data' => $data)));
        $em = $this->getDoctrine()->getManager();
        $evaluations = $em->getRepository('EntityBundle:Evaluations')->findAll();

        return $this->render('@Event/EvaluationsAdmin/listEvaluations.html.twig', array(
            'evaluations' => $evaluations, 'chart' => $ob,
        ));
    }

    /**
     * Creates a new evaluation entity.
     *
     */
    public function newAction(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        $evaluation = new Evaluations();
        $form = $this->createForm('EntityBundle\Form\EvaluationsType', $evaluation);
        $form->handleRequest($request);
        $evaluation->setUser($user);


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($evaluation);
            $em->flush();

            return $this->redirectToRoute('evaluation_show', array('id' => $evaluation->getId()));
        }

        return $this->render('evaluations/new.html.twig', array(
            'evaluation' => $evaluation,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a evaluation entity.
     *
     */
    public function showAction(Evaluations $evaluation)
    {
        $deleteForm = $this->createDeleteForm($evaluation);

        return $this->render('evaluations/show.html.twig', array(
            'evaluation' => $evaluation,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function EvaluationAction(Evaluations $evaluation)
    {
        $deleteForm = $this->createDeleteForm($evaluation);

        return $this->render('@Event/EvaluationsAdmin/showEvaluation.html.twig', array(
            'evaluation' => $evaluation,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to edit an existing evaluation entity.
     *
     */
    public function editAction(Request $request, Evaluations $evaluation)
    {
        $deleteForm = $this->createDeleteForm($evaluation);
        $editForm = $this->createForm('EntityBundle\Form\EvaluationsType', $evaluation);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('evaluation_edit', array('id' => $evaluation->getId()));
        }

        return $this->render('evaluations/edit.html.twig', array(
            'evaluation' => $evaluation,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a evaluation entity.
     *
     */
    public function deleteAction(Request $request, Evaluations $evaluation)
    {
        $form = $this->createDeleteForm($evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($evaluation);
            $em->flush();
        }

        return $this->redirectToRoute('evaluation_index');
    }

    public function deleteEvalAction(Request $request, Evaluations $evaluation)
    {
        $form = $this->createDeleteForm($evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($evaluation);
            $em->flush();
        }

        return $this->redirectToRoute('evaluation_list');
    }

    /**
     * Creates a form to delete a evaluation entity.
     *
     * @param Evaluations $evaluation The evaluation entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Evaluations $evaluation)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('evaluation_delete', array('id' => $evaluation->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

    public function ajouterAction(Request $request ,float $event_id,$comm, $note){
        $em = $this->getDoctrine()->getManager();
        $evaluation = new Evaluations();
        $evaluation->setNote($note);
        $evaluation->setCommentaire($comm);
        $user = $em -> getRepository(User::class) -> find(2);

        $evaluation->setUser($user);


        //$reservation->setEvents($request->get('event_id'));
        $event=$this->getDoctrine()->getManager()->getRepository('EntityBundle:Events')->find(intval($event_id));

        $evaluation->setEvents($event);

        //$User = $this->container->get('security.token_storage')->getToken()->getUser();
        //$reservation->setUser($User);

        $em ->persist($evaluation);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($evaluation);
        return new JsonResponse($formatted);

    }

    public function ajouter2Action(Request $request ,float $event_id){
        $em = $this->getDoctrine()->getManager();
        $evaluation = new Evaluations();
        $evaluation->setNote(2);
        $evaluation->setCommentaire($request->get('commentaire'));


        //$reservation->setEvents($request->get('event_id'));
        $event=$this->getDoctrine()->getManager()->getRepository('EntityBundle:Events')->find(intval($event_id));

        $evaluation->setEvents($event);

        //$User = $this->container->get('security.token_storage')->getToken()->getUser();
        //$reservation->setUser($User);

        $em ->persist($evaluation);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($evaluation);
        return new JsonResponse($formatted);

    }

    public function ajouter3Action(Request $request ,float $event_id){
        $em = $this->getDoctrine()->getManager();
        $evaluation = new Evaluations();
        $evaluation->setNote(3);
        $evaluation->setCommentaire($request->get('commentaire'));


        //$reservation->setEvents($request->get('event_id'));
        $event=$this->getDoctrine()->getManager()->getRepository('EntityBundle:Events')->find(intval($event_id));

        $evaluation->setEvents($event);

        //$User = $this->container->get('security.token_storage')->getToken()->getUser();
        //$reservation->setUser($User);

        $em ->persist($evaluation);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($evaluation);
        return new JsonResponse($formatted);

    }

    public function ajouter4Action(Request $request ,float $event_id){
        $em = $this->getDoctrine()->getManager();
        $evaluation = new Evaluations();
        $evaluation->setNote(4);
        $evaluation->setCommentaire($request->get('commentaire'));


        //$reservation->setEvents($request->get('event_id'));
        $event=$this->getDoctrine()->getManager()->getRepository('EntityBundle:Events')->find(intval($event_id));

        $evaluation->setEvents($event);

        //$User = $this->container->get('security.token_storage')->getToken()->getUser();
        //$reservation->setUser($User);

        $em ->persist($evaluation);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($evaluation);
        return new JsonResponse($formatted);

    }

    public function ajouter5Action(Request $request ,float $event_id){
        $em = $this->getDoctrine()->getManager();
        $evaluation = new Evaluations();
        $evaluation->setNote(5);
        $evaluation->setCommentaire($request->get('commentaire'));


        //$reservation->setEvents($request->get('event_id'));
        $event=$this->getDoctrine()->getManager()->getRepository('EntityBundle:Events')->find(intval($event_id));

        $evaluation->setEvents($event);

        //$User = $this->container->get('security.token_storage')->getToken()->getUser();
        //$reservation->setUser($User);

        $em ->persist($evaluation);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($evaluation);
        return new JsonResponse($formatted);

    }

  /*  public function ajouterAction(Request $request ,float $event_id){
        $em = $this->getDoctrine()->getManager();
        $evaluation = new Evaluations();
        $evaluation->setNote($request->get('note'));
        $evaluation->setCommentaire($request->get('commentaire'));


        //$reservation->setEvents($request->get('event_id'));
        $event=$this->getDoctrine()->getManager()->getRepository('EntityBundle:Events')->find(intval($event_id));

        $evaluation->setEvents($event);

        //$User = $this->container->get('security.token_storage')->getToken()->getUser();
        //$reservation->setUser($User);

        $em ->persist($evaluation);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($evaluation);
        return new JsonResponse($formatted);

    }*/


    public function findAction($id)
    {
        $evaluation = $this->getDoctrine()->getManager()->getRepository('EntityBundle:Evaluations')->find($id);
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($evaluation);
        return new JsonResponse($formatted);

    }

    public function allAction()
    {
        $evaluation = $this->getDoctrine()->getManager()->getRepository('EntityBundle:Evaluations')->findAll();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(2);

        $encoder = new JsonEncoder();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $serializer = new Serializer(array($normalizer), array($encoder));
        $formatted = $serializer->normalize($evaluation);
        return new JsonResponse($formatted);
    }


}
