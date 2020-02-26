<?php

namespace ForumBundle\Controller;

use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use EntityBundle\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use EntityBundle\Entity\Metapost;
use EntityBundle\Entity\Comment;
use EntityBundle\Entity\Seller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\UserBundle;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;

class AdminController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $posts = $em->getRepository('EntityBundle:Post')->findAll();

        return $this->render('post/adminindex.html.twig', array(
            'posts' => $posts
        ));
    }

    public  function showAction()
    {
        $em = $this->getDoctrine()->getManager();

        $posts = $em->getRepository('EntityBundle:Post')->findAll();

        return $this->render('post/adminindex.html.twig', array(
            'posts' => $posts
        ));
    }

    public function admindeleteAction(Request $request, Post $post, $id)
    {
        $post = $this -> getDoctrine() -> getRepository(post::class) -> find($id);
        $em = $this -> getDoctrine() -> getManager();
        $em -> remove($post);
        $em -> flush();
        return $this -> redirectToRoute("dash");
    }
    /**
     * Deletes a post entity.
     *
     */
    public function deleteAction(Request $request, Post $post)
    {
        $form = $this->createDeleteForm($post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
        }

        return $this->redirectToRoute('post_index');
    }

    /**
     * Creates a form to delete a post entity.
     *
     * @param Post $post The post entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Post $post)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('post_delete', array('id' => $post->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Lists all reparation entities.
     *
     * @Route("/stat", name="reparation_stat")
     * @Method("GET")
     */
    public function statAction()
    {
        $pieChart = new PieChart();
        $em= $this->getDoctrine();
        $classes = $em->getRepository('EntityBundle:Post')->findAll();
        $totalEtudiant=0;
        foreach($classes as $post) {
            $totalEtudiant=$totalEtudiant+$post->getVotecount();
        }

        $data= array();
        $stat=['classe', 'nbEtudiant'];
        $nb=0;
        array_push($data,$stat);
        foreach($classes as $post) {
            $stat=array();
            array_push($stat,$post->getTitle(),(($post->getVotecount()) ));
            $nb=($post->getVotecount());
            $stat=[$post->getTitle(),$nb];
            array_push($data,$stat);

        }

        $pieChart->getData()->setArrayToDataTable(
            $data
        );
        $pieChart->getOptions()->setTitle('Likes per post');
        $pieChart->getOptions()->setHeight(500);
        $pieChart->getOptions()->setWidth(900);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);


        return $this->render('post/stat.html.twig', array('pieChart' => $pieChart));
    }
}

