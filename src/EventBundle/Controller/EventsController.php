<?php

namespace EventBundle\Controller;

use EntityBundle\Entity\Events;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;


/**
 * Event controller.
 *
 */
class EventsController extends Controller
{
    /**
     * Lists all event entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $events = $em->getRepository('EntityBundle:Events')->findAll();
        return $this->render('events/index.html.twig', array(
            'events' => $events,
        ));
    }

    public function listAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $eventlist = $em->getRepository('EntityBundle:Events')->findAll();
        //$event = $em->getRepository("EntityBundle:Events")
        // ->ORDERBYEvent();
        $event  = $this->get('knp_paginator')->paginate($eventlist, $request->query->get('page', 1), 6);

        return $this->render('@Event/EventsAdmin/listEvents.html.twig', array(
            'events' => $event,
        ));

    }

    /**
     * Finds and displays a event entity.
     *
     */
    public function showEventAction(Events $event)
    {

        return $this->render('@Event/EventsAdmin/ShowEvent.html.twig', array(
            'event' => $event,
        ));
    }

    /**
     * Creates a new event entity.
     *
     */
    public function newAction(Request $request)
    {
        $event = new \EntityBundle\Entity\Events();
        $form = $this->createForm('EntityBundle\Form\EventsType', $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $event->uploadProfilePicture();
            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute('events_showevent', array('id' => $event->getId()));
        }

        return $this->render('events/new.html.twig', array(
            'event' => $event,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a event entity.
     *
     */
    public function showAction(Events $event)
    {

        return $this->render('events/show.html.twig', array(
            'event' => $event,
        ));
    }

    /**
     * Displays a form to edit an existing event entity.
     *
     */
    public function editAction(Request $request, Events $event)
    {
        $deleteForm = $this->createDeleteForm($event);
        $editForm = $this->createForm('EntityBundle\Form\EventsType', $event);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();



            return $this->redirectToRoute('events_edit', array('id' => $event->getId()));
        }

        return $this->render('events/edit.html.twig', array(
            'event' => $event,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a event entity.
     *
     */
    public function deleteAction(Request $request, Events $event)
    {

        if ($event) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($event);
            $em->flush();
        }

        return $this->redirectToRoute('events_list');
    }



    /**
     * Creates a form to delete a event entity.
     *
     * @param Events $event The event entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Events $event)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('events_delete', array('id' => $event->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('p');
        $events =  $em->getRepository('EntityBundle:Events')->findEntitiesByString($requestString);
        if(!$events) {
            $result['events']['error'] = "Event Not found :( ";
        } else {
            $result['events'] = $this->getRealEntities($events);
        }
        return new Response(json_encode($result));
    }

    public function getRealEntities($events){
        foreach ($events as $events){
            $realEntities[$events->getId()] = [$events->getTitre()];

        }
        return $realEntities;
    }

    public function sortEventAction(){
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository("EntityBundle:Events")
            ->ORDERBYEvent();
        return $this->render("@Event/EventsAdmin/listEvents.html.twig",array(
            "events"=>$event,

        ));
    }


    public function sortAction()
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository("EntityBundle:Events")
            ->ORDERBYEventPrix();
        return $this->render("events/index.html.twig", array(
            "events" => $event,

        ));
    }


    /*public function allAction()
    {
        $event = $this->getDoctrine()->getManager()->getRepository('EntityBundle:Events')->findAll();
    $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(2);

        $encoder = new JsonEncoder();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $serializer = new Serializer(array($normalizer), array($encoder));
        $formatted = $serializer->normalize($event);
        return new JsonResponse($formatted);
    }*/


    public function eventscomeAction()
    {
        $em = $this->getDoctrine()->getManager();
        //$User = $this->container->get('security.token_storage')->getToken()->getUser();
        // $User->getRoles();
        $events = $em->getRepository('EntityBundle:Events')->datasuppDQL();

        $normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor());
        $serializer = new Serializer([new DateTimeNormalizer(), $normalizer]);
        $formatted = $serializer->normalize($events,'json', [AbstractNormalizer::ATTRIBUTES => ['id','titre','description','prix','nbrPlaces','localisation','professional','nomImage','date'=>['id']]]);

        return new JsonResponse($formatted);
    }

    public function eventscomecurrAction()
    {
        $em = $this->getDoctrine()->getManager();
        //$User = $this->container->get('security.token_storage')->getToken()->getUser();
        // $User->getRoles();
        $events = $em->getRepository('EntityBundle:Events')->datasuppegDQL();

        $normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor());
        $serializer = new Serializer([new DateTimeNormalizer(), $normalizer]);
        $formatted = $serializer->normalize($events,'json', [AbstractNormalizer::ATTRIBUTES => ['id','titre','description','prix','nbrPlaces','localisation','professional','nomImage','date'=>['id']]]);

        return new JsonResponse($formatted);
    }


    public function eventsgoneAction()
    {
        $em = $this->getDoctrine()->getManager();
        //$User = $this->container->get('security.token_storage')->getToken()->getUser();
        // $User->getRoles();
        $events = $em->getRepository('EntityBundle:Events')->datainfDQL();

        $normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor());
        $serializer = new Serializer([new DateTimeNormalizer(), $normalizer]);
        $formatted = $serializer->normalize($events,'json', [AbstractNormalizer::ATTRIBUTES => ['id','titre','description','prix','nbrPlaces','localisation','professional','nomImage','date'=>['id']]]);

        return new JsonResponse($formatted);
    }

    public function findAction($id)
    {
        $event = $this->getDoctrine()->getManager()->getRepository('EntityBundle:Events')->find($id);
        $normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor());
        $serializer = new Serializer([new DateTimeNormalizer(), $normalizer]);
        $formatted = $serializer->normalize($event,'json', [AbstractNormalizer::ATTRIBUTES => ['id','titre','description','prix','nbrPlaces','localisation','professional','nomImage','date'=>['id']]]);

        return new JsonResponse($formatted);

    }

    public function ajouterAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $event = new Events();
        $event->setTitre($request->get('titre'));
        $event->setDescription($request->get('description'));
        $event->setDate($request->get('date'));
        $event->setNbrPlaces($request->get('nbrPlaces'));
        $event->setPrix($request->get('prix'));
        $event->setLocalisation($request->get('localisation'));
        $event->setNomImage($request->get('nom_image'));
        $event->getProfessional($request->get('idPro'));


        $em ->persist($event);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($event);
        return new JsonResponse($formatted);

    }


    public function searchapiAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('e');
        $events =  $em->getRepository('EntityBundle:Events')->findEntitiesByString($requestString);
        $normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor());
        $serializer = new Serializer([new DateTimeNormalizer(), $normalizer]);
        $formatted = $serializer->normalize($events,'json', [AbstractNormalizer::ATTRIBUTES =>  ['id','titre','description','prix','nbrPlaces','localisation','professional','nomImage','date'=>['id']]]);

        return new JsonResponse($formatted);
    }


    public function findBYdateAction($date)
    {
        $event = $this->getDoctrine()->getManager()->getRepository('EntityBundle:Events')->findDateDQL($date);
        $normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor());
        $serializer = new Serializer([new DateTimeNormalizer(), $normalizer]);
        $formatted = $serializer->normalize($event,'json', [AbstractNormalizer::ATTRIBUTES => ['id','titre','description','prix','nbrPlaces','localisation','professional','nomImage','date'=>['date']]]);

        return new JsonResponse($formatted);

    }

}
