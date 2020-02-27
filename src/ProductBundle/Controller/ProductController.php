<?php

namespace ProductBundle\Controller;
use EntityBundle\Entity\Product;
use EntityBundle\Entity\Question;
use EntityBundle\Form\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller
{

    public function ListProductAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $productlist = $em->getRepository('EntityBundle:Product')->findAll();
        $products = $this->get('knp_paginator')->paginate($productlist,$request->query->get('page', 1), 6);
        return $this->render('@Product/Product/listProduct.html.twig', array(
            "products" => $products,
        ));
    }

    public function ListProductIndexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $productlist = $em->getRepository('EntityBundle:Product')->findAll();
        $products = $this->get('knp_paginator')->paginate($productlist,$request->query->get('page', 1), 6);

        return $this->render('product/listProductIndex.html.twig', array(
            "product" => $products,
        ));
    }

    public function AddProductAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $product->uploadProfilePicture();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute("list_product");
        }
        return $this->render('@Product/Product/add_product.html.twig', array("form" => $form->createView()));

    }

    /**
     * @IsGranted("ROLE_SELLER")
     */
    public function AddProdIndexAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $product->uploadProfilePicture();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute("list_product_index");
        }
        return $this->render('product/add_product_index.html.twig', array("form" => $form->createView()));

    }

    /**
     * Finds and displays a Product entity.
     *
     */
    public function showAction(Product $product)
    {
        return $this->render('@Product/Product/show.html.twig', array(
            'Product' => $product,
        ));
    }

    public function showProdAction(Product $product)
    {
        return $this->render('product/showProd.html.twig', array(
            'Product' => $product,
        ));
    }

    public function UpdateProductAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('EntityBundle:Product')->find($id);
        $form = $this->createForm(ProductType::class, $product);
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

    /**
     * @IsGranted("ROLE_SELLER")
     */
    public function UpdateProdIndexAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('EntityBundle:Product')->find($id);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            $this->addFlash('info', 'Created Successfully !');
            return $this->redirectToRoute('list_product_index');
        }
        return $this->render('product/updateprod.html.twig', array("form"=>$form->createView()));

    }
        public function DeleteProductAction($id)
        {
            $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
            return $this->redirectToRoute("list_product");
        }

        public function filterproductAction(Request $request)
        {
            $nompr = $request->get('nompr');
            $em = $this->getDoctrine()->getManager();
            $product = $em->getRepository("EntityBundle:Product")
                ->findByName($nompr);
            return $this->render("@Product/Product/filter_product.html.twig", array(
                "products" => $product,

            ));
        }

        public function sortProductAction()
        {
            $em = $this->getDoctrine()->getManager();
            $prod = $em->getRepository("EntityBundle:Product")
                ->ORDERBYprod();
            return $this->render("@Product/Product/sortProd.html.twig", array(
                "prods" => $prod,

            ));
        }

    public function sortProdIndexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $prod = $em->getRepository("EntityBundle:Product")
            ->ORDERBYprod();
        return $this->render("product/sortprod.html.twig", array(
            "prods" => $prod,
        ));
    }

    public function SearchAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $product= $em->getRepository('EntityBundle:Product')->findAll();
        if($request->isMethod('POST'))
        {
            $category=$request->get('category');
            $product= $em->getRepository('EntityBundle:Product')->findBy(array("category"=>$category));
        }
        return $this->render('@Product/Product/search.html.twig', array("Product"=>$product));
    }
}