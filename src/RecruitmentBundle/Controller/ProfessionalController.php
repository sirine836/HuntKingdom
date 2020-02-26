<?php

namespace RecruitmentBundle\Controller;

use EntityBundle\Entity\Professional;
use EntityBundle\Form\ProfessionalType;
use EntityBundle\Form\ValidateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProfessionalController extends Controller
{

    /**
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function ListProAction(){
        $em = $this->getDoctrine()->getManager();

        $prolist = $em->getRepository('EntityBundle:Professional')->findAll();
        return $this->render('@Recruitment/Professional/listProfessional.html.twig', array(
            "professional" => $prolist,
        ));
    }

    /**
     * @IsGranted("ROLE_MEMBER")
     */
    public function AddProAction(Request $request)
    {
        $pro = new Professional();
        $form = $this -> createForm(ProfessionalType::class,$pro);
        $form -> handleRequest($request);
        $user= $this->getUser();
        if ( $form -> isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $pro->uploadProfilePicture();
            $pro->setUser($user);
            $em->persist($pro);
            $em->flush();

         //   return $this->redirectToRoute("list_professional");
            return $this->render('@Recruitment/Professional/continue.html.twig');
        }

        return $this->render('@Recruitment/Professional/add_pro.html.twig', array("form"=>$form->createView()
        ));
    }

    /**
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function UpdateProAction(Request $request,$id)
    {

        $em=$this->getDoctrine()->getManager();
        $pro= $em->getRepository('EntityBundle:Professional')->find($id);
        $form=$this->createForm(ProfessionalType::class,$pro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pro);
            $em->flush();
            $this->addFlash('info', 'Created Successfully !');
           return $this->redirectToRoute('list_professional');
        }


        return $this->render('@Recruitment/Professional/update_pro.html.twig', array("form"=>$form->createView()));
    }

    /**
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function DeleteProAction($id)
    {
        $pro = $this -> getDoctrine() -> getRepository(Professional::class) -> find($id);
        $em = $this -> getDoctrine() -> getManager();
        $em -> remove($pro);
        $em -> flush();
        return $this -> redirectToRoute("list_professional");
    }
   public function SearchAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $professional= $em->getRepository('EntityBundle:Professional')->findAll();
        if($request->isMethod('POST'))
        {
            $proname=$request->get('proname');
            $professional= $em->getRepository('EntityBundle:Professional')->findBy(array("proname"=>$proname));
        }
        return $this->render('@Recruitment/Professional/listProfessional.html.twig', array("professional"=>$professional));

    }

   /* public function ValidateAction(Request $request, $id)
    {
        $em=$this->getDoctrine()->getManager();
        $pro= $em->getRepository('EntityBundle:Professional')->findAll();
        $select=$select->getRepository('EntityBundle:Professional')->find($id);
        $form=$this->createForm(ValidateType::class,$pro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pro);
            $em->flush();
            $this->addFlash('info', 'Created Successfully !');
           // return $this->redirectToRoute('list_professional');
        }

        return $this->render('@Recruitment/Professional/validateProfessional.html.twig', array(
            "professional" => $pro,
        ));
    }*/
}
