<?php

namespace PanierBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use EntityBundle\Entity\Facture;
use EntityBundle\Entity\Panier;
use EntityBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class FactureApiController extends Controller
{
    /**
     * Lists all facture entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $factures = $em->getRepository('EntityBundle:Facture')->findAll();

        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(2);
        $encoder = new JsonEncoder();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $serializer = new Serializer(array($normalizer), array($encoder));
        $formatted = $serializer->normalize($factures);
        return new JsonResponse($formatted);
    }



    /**
     * Creates a new facture entity.
     *
     */
    public function newAction(Request $request)
    {
        $facture = new Facture();
        $em = $this->getDoctrine()->getManager();
        $panier = $em->getRepository('EntityBundle:Panier')->findPanierDispoDQL(2);
        $panier = $panier[0];
        $panier->setEtat(false);
        $panier->setArchive(false);
        $facture->setEtat(false);
        $facture->setPanier($panier);
        $facture->setNumtel($request->get('numtel'));
        $date = new \DateTime($request->get('dateDeLivraison'));
        $facture->setDateDeLivraison($date);
        $facture->setAdresse($request->get('adresse'));
        $em->persist($facture);
        $em->flush();

        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(3);
        $encoder = new JsonEncoder();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $serializer = new Serializer(array($normalizer), array($encoder));
        $formatted = $serializer->normalize($facture);
        return new JsonResponse($formatted);
    }



    public function PaiementAction(Request $request)
    {
        $factureID=$request->get('id');
        $em = $this->getDoctrine()->getManager();
        $facture = $em->getRepository('EntityBundle:Facture')->find($factureID);
        $panier = $facture->getPanier();
        $facture->setEtat(true);
        $panier->setEtat(true);
        $panier->setArchive(true);
        $panier = $this->initialiserPanier();
        $em->persist($facture);
        $em->flush();

        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(3);
        $encoder = new JsonEncoder();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $serializer = new Serializer(array($normalizer), array($encoder));
        $formatted = $serializer->normalize($facture);
        return new JsonResponse($formatted);
    }


    public function allfactureAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $factures = $em->getRepository('EntityBundle:Panier')->findPanierfactDQL(2);


        $normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor());
        $serializer = new Serializer([new DateTimeNormalizer(), $normalizer]);
        $formatted = $serializer->normalize($factures,'json', [AbstractNormalizer::ATTRIBUTES => ['id','numtel','adresse','etat','dateDeLivraison']]);

        return new JsonResponse($formatted);
    }



    /**
     * Finds and displays a facture entity.
     *
     */
    public function show2Action(Request $request)
    {
        $factureID=$request->get('id');
        $em = $this->getDoctrine()->getManager();
        $facture = $em->getRepository('EntityBundle:Facture')->find($factureID);

        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(3);
        $encoder = new JsonEncoder();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $serializer = new Serializer(array($normalizer), array($encoder));
        $formatted = $serializer->normalize($facture);
        return new JsonResponse($formatted);
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


    public  function initialiserPanier()
    {
        $panier = new Panier();
        $panier->setEtat(false);
        $panier->setArchive(false);

        $panier->setDatePanier(new \DateTime);
        $panier->setProducts(new ArrayCollection());
        $panier->setPrixTotal(0);
        $em = $this->getDoctrine()->getManager();
        $user = $em -> getRepository(User::class) -> find(2);
        $panier->setUser($user);
        $em->persist($panier);
        $em->flush();

        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(3);
        $encoder = new JsonEncoder();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $serializer = new Serializer(array($normalizer), array($encoder));
        $formatted = $serializer->normalize($panier);
        return new JsonResponse($formatted);
    }
}
