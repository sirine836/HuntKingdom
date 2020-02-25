<?php

namespace ProductBundle\Controller;
use EntityBundle\Entity\Product;
use EntityBundle\Form\ProductType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller
{
    public function ListProductAction(){
        $em = $this->getDoctrine()->getManager();

        $productlist = $em->getRepository('EntityBundle:Product')->findAll();
        return $this->render('@Product/Product/listProduct.html.twig', array(
            "product" => $productlist,
        ));
    }
    public function AddProductAction(Request $request)
    {
        $product = new Product();
        $form = $this -> createForm(ProductType::class,$product);
        $form -> handleRequest($request);
        if ( $form -> isSubmitted() ) {
            $em = $this->getDoctrine()->getManager();
            $product->uploadProfilePicture();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute("list_product");
        }
        return $this->render('@Product/Product/add_product.html.twig', array("form"=>$form->createView()));

    }
    public function UpdateProductAction(Request $request,$id)
    {

        $em=$this->getDoctrine()->getManager();
        $product= $em->getRepository('EntityBundle:Product')->find($id);
        $form=$this->createForm(ProductType::class,$product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            $this->addFlash('info', 'Created Successfully !');
            return $this->redirectToRoute('list_product');
        }


        return $this->render('@Product/Product/update_product.html.twig', array("form"=>$form->createView()));
    }
    public function DeleteProductAction($id)
    {
        $product = $this -> getDoctrine() -> getRepository(Product::class) -> find($id);
        $em = $this -> getDoctrine() -> getManager();
        $em -> remove($product);
        $em -> flush();
        return $this -> redirectToRoute("list_product");
    }
}
