<?php


namespace PanierBundle\Controller;


use Doctrine\Common\Collections\ArrayCollection;
use EntityBundle\Entity\LignePanier;
use EntityBundle\Entity\Panier;
use EntityBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class PanierApiController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $paniers = $em->getRepository('EntityBundle:Panier')->findAll();

        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit($this->getUser()->getId());
        $encoder = new JsonEncoder();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $serializer = new Serializer(array($normalizer), array($encoder));
        $formatted = $serializer->normalize($paniers);
        return new JsonResponse($formatted);

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

        $normalizer = new ObjectNormalizer();
        $encoder = new JsonEncoder();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $serializer = new Serializer(array($normalizer), array($encoder));
        $formatted = $serializer->normalize($panier);
        return new JsonResponse($formatted);
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
        //$user = $this->container->get('security.token_storage')->getToken()->getUser();
        $panier = $em->getRepository('EntityBundle:Panier')->findPanierDispoDQL(2);

        if($panier == null)
        {
            $panier = $this->initialiserPanier();
        }else{
            $panier=$panier[0];
        }

        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(2);
        $encoder = new JsonEncoder();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $serializer = new Serializer(array($normalizer), array($encoder));
        $formatted = $serializer->normalize($panier);
        return new JsonResponse($formatted);

    }



    public function deleteProductAction(Request $request)
    {
        $lignePanierID=$request->get('id');

        $em = $this->getDoctrine()->getManager();
        $panierligne = $em->getRepository('EntityBundle:LignePanier')->find($lignePanierID);
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $panier = $em->getRepository('EntityBundle:Panier')->findPanierDispoDQL(2);
        $panier = $panier[0];
        $prixàSoustraire = $panierligne->getProduct()->getPrix() * $panierligne->getQuantite();
        $panier->setPrixTotal($panier->getPrixTotal() - $prixàSoustraire );
        $panierligne->getProduct()->setQuantity($panierligne->getProduct()->getQuantity()+$panierligne->getQuantite());
        $em->remove($panierligne);
        $em->flush();

        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(3);
        $encoder = new JsonEncoder();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $serializer = new Serializer(array($normalizer), array($encoder));
        $formatted = $serializer->normalize($panierligne);
        return new JsonResponse($formatted);
    }



    public function affprixAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $panier = $em->getRepository('EntityBundle:Panier')->ligprixDispoDQL(2);

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


    public function affligneAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $panier = $em->getRepository('EntityBundle:Panier')->ligneDispoDQL(2);

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


    public function addProductToPanierAction(Request $request)
    {
        $productID=$request->get('id');

        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('EntityBundle:Product')->find($productID);
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if($product->getQuantity() > 0 )
        {

            $lignePanier = $em->getRepository('EntityBundle:Panier')->findByProduct($productID,2);
            if($lignePanier != null)
            {
                $lignePanier = $lignePanier[0];
                $lignePanier->setQuantite($lignePanier->getQuantite()+1);
                $em = $this->getDoctrine()->getManager();
                $panier = $em->getRepository('EntityBundle:Panier')->findPanierDispoDQL(2);
                $panier = $panier[0];
                $prixàAjoute = $lignePanier->getProduct()->getPrix();
                $lignePanier->getProduct()->setQuantity($lignePanier->getProduct()->getQuantity()-1);
                $em->flush();
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

        $panier = $em->getRepository('EntityBundle:Panier')->findPanierDispoDQL(2);

        if($panier != null)
        {
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
        $panierligne->setQuantite($panierligne->getQuantite()+1);
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        while ($panierligne->getQuantite()>=1)
        {
            $panier = $em->getRepository('EntityBundle:Panier')->findPanierDispoDQL(2);
            $panier = $panier[0];
            $prixàSoustraire = $panierligne->getProduct()->getPrix();
            $panier->setPrixTotal($panier->getPrixTotal() + $prixàSoustraire);
            if($panierligne->getProduct()->getQuantity()>0){
            $panierligne->getProduct()->setQuantity($panierligne->getProduct()->getQuantity()-1);

            }
            else{
                $normalizer = new ObjectNormalizer();
                $normalizer->setCircularReferenceLimit(3);
                $encoder = new JsonEncoder();
                $normalizer->setCircularReferenceHandler(function ($object) {
                    return $object->getId();
                });
                $serializer = new Serializer(array($normalizer), array($encoder));
                $formatted = $serializer->normalize($panierligne);
                return new JsonResponse($formatted);
            }
            $em->flush();


            $normalizer = new ObjectNormalizer();
            $normalizer->setCircularReferenceLimit(3);
            $encoder = new JsonEncoder();
            $normalizer->setCircularReferenceHandler(function ($object) {
                return $object->getId();
            });
            $serializer = new Serializer(array($normalizer), array($encoder));
            $formatted = $serializer->normalize($panierligne);
            return new JsonResponse($formatted);
        }

        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(3);
        $encoder = new JsonEncoder();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $serializer = new Serializer(array($normalizer), array($encoder));
        $formatted = $serializer->normalize($panierligne);
        return new JsonResponse($formatted);
    }



    public function incFunction(lignePanier $panierligne)
    {
        $panierligne->setQuantite($panierligne->getQuantite()+1);
        $em = $this->getDoctrine()->getManager();
        $panier = $em->getRepository('EntityBundle:Panier')->findPanierDispoDQL(2);
        $panier = $panier[0];
        $prixàAjoute = $panierligne->getProduct()->getPrix();
        $prixàSoustraire = $panierligne->getProduct()->getPrix();
        $panier->setPrixTotal($panier->getPrixTotal() + $prixàAjoute);
        $panierligne->getProduct()->setQuantity($panierligne->getProduct()->getQuantity()-1);

        $em->flush();
    }

    public function ProductQTEAction(lignePanier $lignePanierID)
    {
        $em = $this->getDoctrine()->getManager();
        $lignePanier = $em -> getRepository('EntityBundle:lignePanier')->find($lignePanierID);
        $x=$lignePanier->getProduct()->getQuantity();

        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(3);
        $encoder = new JsonEncoder();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $serializer = new Serializer(array($normalizer), array($encoder));
        $formatted = $serializer->normalize($x);
        return new JsonResponse($formatted);

    }


    public function DecreaseProductQTEAction(Request $request)
    {
        $lignePanierID=$request->get('id');
        $em = $this->getDoctrine()->getManager();
        $panierligne = $em->getRepository('EntityBundle:lignePanier')->find($lignePanierID);
        $panierligne->setQuantite($panierligne->getQuantite()-1);
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        while ($panierligne->getQuantite()>=1)
        {
            $panier = $em->getRepository('EntityBundle:Panier')->findPanierDispoDQL(2);
            $panier = $panier[0];
            $prixàSoustraire = $panierligne->getProduct()->getPrix();
            $panier->setPrixTotal($panier->getPrixTotal() - $prixàSoustraire);
            $panierligne->getProduct()->setQuantity($panierligne->getProduct()->getQuantity()+1);
            $em->flush();

            $normalizer = new ObjectNormalizer();
            $normalizer->setCircularReferenceLimit(3);
            $encoder = new JsonEncoder();
            $normalizer->setCircularReferenceHandler(function ($object) {
                return $object->getId();
            });
            $serializer = new Serializer(array($normalizer), array($encoder));
            $formatted = $serializer->normalize($panierligne);
            return new JsonResponse($formatted);
        }


        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(3);
        $encoder = new JsonEncoder();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $serializer = new Serializer(array($normalizer), array($encoder));
        $formatted = $serializer->normalize($panierligne);
        return new JsonResponse($formatted);
    }


}