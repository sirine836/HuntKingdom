<?php

namespace EventBundle\Controller;

use EntityBundle\Entity\Events;
use EntityBundle\Entity\Reservation;
use EntityBundle\Form\ReservationsType;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * Reservation controller.
 *
 */
class ReservationController extends Controller
{
    private $user;

    /**
     * Lists all reservation entities.
     *
     */
    public function indexAction()
    {
        $ob = new Highchart();
        $ob->chart->renderTo('pichart');
        $ob->title->text('Evaluations des Reservations');
        $ob->plotOptions->pie(array(
            'allowPointSelect'  => true,
            'cursor'    => 'pointer',
            'dataLabels'    => array('enabled' => false),
            'showInLegend'  => true
        ));
        $em = $this->getDoctrine()->getManager();
        $query = $em-> createQuery('SELECT (p.user) as User, (p.id) as Reservation FROM  EntityBundle:Reservation p ');
        $resultat = $query->getResult();
        $data = array();
        foreach ($resultat as $values)
        {
            $a = array($values['User'], intval($values['Reservation']));
            array_push($data,$a);
        }
        $ob->series(array(array('type' => 'pie','name' => 'Reservation id', 'data' => $data)));
        $em = $this->getDoctrine()->getManager();
        $reservations = $em->getRepository('EntityBundle:Reservation')->findAll();

        return $this->render('reservation/index.html.twig', array(
            'reservations' => $reservations, 'chart' => $ob
        ));
    }

    /**
     * Finds and displays a reservation entity.
     *
     */
    public function showAction(Reservation $reservation)
    {

        return $this->render('reservation/show.html.twig', array(
            'reservation' => $reservation,
        ));
    }
    public function showResAction(Reservation $reservation)
    {

        return $this->render('@Event/ReservationsAdmin/showRes.html.twig', array(
            'reservation' => $reservation,
        ));
    }
    public function newAction(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $reservation = new Reservation();
        $reservation->getEvents();
        $form = $this->createForm(ReservationsType::class, $reservation);
        $form->handleRequest($request);
        $reservation->setUser($user);
        $event=$reservation->getEvents();


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em1 = $this->getDoctrine()->getManager();
            $reservation->setPrixpaye($reservation->getQuantite()*$event->getPrix());
            $event->setNbrPlaces($event->getNbrPlaces()-$reservation->getQuantite());
            $em1->persist($event);
            $em->persist($reservation);
            $em->flush();

            return $this->redirectToRoute('reservation_show', array('id' => $reservation->getId()));

        }

        return $this->render('reservation/new.html.twig', array(
            'reservation' => $reservation,
            'form' => $form->createView(),
        ));
    }

    public function getUser()
    {
        return $this->user;
    }


    public function ajouterAction(Request $request ,float $event_id){
        $em = $this->getDoctrine()->getManager();
        $reservation = new Reservation();
        $reservation->setPrixpaye($request->get('prixpaye'));
        $reservation->setQuantite($request->get('quantite'));


        //$reservation->setEvents($request->get('event_id'));
        $event=$this->getDoctrine()->getManager()->getRepository('EntityBundle:Events')->find(intval($event_id));
        $reservation->setEvents($event);

        $event->setNbrPlaces($event->getNbrPlaces()-$reservation->getQuantite());

        //$User = $this->container->get('security.token_storage')->getToken()->getUser();
        //$reservation->setUser($User);

        $em ->persist($reservation);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($reservation);
        return new JsonResponse($formatted);

    }


    public function findAction($id)
    {
        $reservation = $this->getDoctrine()->getManager()->getRepository('EntityBundle:Reservation')->find($id);
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($reservation);
        return new JsonResponse($formatted);

    }


    public function deleteResAction(Request $request,float  $event_id)
    {
        $ReservationID=$request->get('id');

        $em = $this->getDoctrine()->getManager();
        $reservation = $em->getRepository('EntityBundle:Reservation')->find($ReservationID);
        $event=$this->getDoctrine()->getManager()->getRepository('EntityBundle:Events')->find(intval($event_id));
        //$panier = $panier[0];


        //  $Placesàajouter = $panierligne->getProduct()->getPrix() * $panierligne->getQuantite();
        $event->setNbrPlaces($event->getNbrPlaces() + $reservation->getQuantite() );
        // $panierligne->getProduct()->setQuantity($panierligne->getProduct()->getQuantity()+$panierligne->getQuantite());


        $em->remove($reservation);
        $em->flush();

        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(3);
        $encoder = new JsonEncoder();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $serializer = new Serializer(array($normalizer), array($encoder));
        $formatted = $serializer->normalize($reservation);
        return new JsonResponse($formatted);


        /* $serializer = new Serializer([new ObjectNormalizer()]);
         $formatted = $serializer->normalize($tasks);
         return new JsonResponse($formatted);*/
    }

    public function affidAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $reservation = $em->getRepository('EntityBundle:Reservation')->recupIdDispoDQL($id);

        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(3);
        $encoder = new JsonEncoder();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $serializer = new Serializer(array($normalizer), array($encoder));
        $formatted = $serializer->normalize($reservation);
        return new JsonResponse($formatted);
    }
}
