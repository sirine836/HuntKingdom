<?php

namespace RecruitmentBundle\Controller;

use EntityBundle\Entity\Mail;
use EntityBundle\Entity\Professional;
use EntityBundle\Form\MailType;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MailController extends Controller
{

    public function sendMailAction(Request $request, $id)
    {
        $mail = new Mail();
        $em = $this->getDoctrine()->getManager();
        $form= $this->createForm(MailType::class,$mail);
        $form->handleRequest($request);
        $pro = $em->getRepository('EntityBundle:Professional')->find($id);


        if ($form->isSubmitted() && $form->isValid()) {
            $subject = $mail->getSubject();
            $email = $pro->getEmail();
            //$object = $request->get('form')['object'];
            $object = $mail->getObject();
            $username = 'thunteresprit@gmail.com';
          $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($username)
                ->setTo($email)
                ->setBody($object);
            $this->get('mailer')->send($message);
            $this->addFlash('info','Mail sent successfully');
        }

        return $this->render('@Recruitment/Mail/sendMail.html.twig', array ('form'=>$form->createView()));

        }

    public function sendMailSellerAction(Request $request, $id)
    {
        $mail = new Mail();
        $em = $this->getDoctrine()->getManager();
        $form= $this->createForm(MailType::class,$mail);
        $form->handleRequest($request);
        $seller = $em->getRepository('EntityBundle:Seller')->find($id);


        if ($form->isSubmitted() && $form->isValid()) {
            $subject = $mail->getSubject();
            $email = $seller->getEmail();
            //$object = $request->get('form')['object'];
            $object = $mail->getObject();
            $username = 'thunteresprit@gmail.com';
            $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($username)
                ->setTo($email)
                ->setBody($object);
            $this->get('mailer')->send($message);
            $this->addFlash('info','Mail sent successfully');
        }

        return $this->render('@Recruitment/Mail/sendMail.html.twig', array ('form'=>$form->createView()));

    }


}


