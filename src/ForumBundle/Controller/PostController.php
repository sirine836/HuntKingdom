<?php

namespace ForumBundle\Controller;

use EntityBundle\Entity\Metapost;
use EntityBundle\Entity\Post;
use EntityBundle\Entity\Comment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use UserBundle\UserBundle;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;


/**
 * Post controller.
 *
 */
class PostController extends Controller
{
    /**
     * Lists all post entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $posts = $em->getRepository('EntityBundle:Post')->findAll();

        return $this->render('post/index.html.twig', array(
            'posts' => $posts
        ));
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function newAction(Request $request)
    {
        $post = new \EntityBundle\Entity\Post();
        $form = $this->createForm('EntityBundle\Form\PostType', $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setDateadded(new \DateTime("now"));
            $post->setSolved(false);
            $user = $this->getUser();
            $post->setUser($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post_show', array('id' => $post->getId()));
        }

        return $this->render('post/new.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function showAction(Post $post)
    {
        $deleteForm = $this->createDeleteForm($post);
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository('EntityBundle:Comment')->findDQL($post->getId());
        $metapost = $em->getRepository('EntityBundle:Metapost')->findMetaDQL($post->getId());
        $b = false;
        if ($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            if (!empty($metapost)) {
                foreach($metapost as $meta)
                {
                    if ($meta->getUser()->getId() == $user->getId()) {
                        $b = true;
                    }
                }
            }
        }
        return $this->render('post/show.html.twig', array(
            'post' => $post,
            'delete_form' => $deleteForm->createView(),
            'user' => $user,
            'comments'=>$comments,
            'metapost'=>$metapost,'b'=>$b,
        ));
    }

    /**
     * Displays a form to edit an existing post entity.
     *
     */
    public function editAction(Request $request, Post $post)
    {
        $deleteForm = $this->createDeleteForm($post);
        $editForm = $this->createForm('EntityBundle\Form\PostType', $post);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('post_edit', array('id' => $post->getId()));
        }

        return $this->render('post/edit.html.twig', array(
            'post' => $post,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
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

    public function commentAction(Request $request, Post $postid)
    {
        $comment = new Comment();
        $form = $this->createForm('EntityBundle\Form\CommentType', $comment);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('EntityBundle:Post')->find($postid);
        // dump($post);
        // exit();

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $comment->setUser($user);
            $comment->setPost($post);
            $comment->setDateadded(new \DateTime("now"));
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('comment_show', array('id' => $comment->getId()));
        }

        return $this->render('comment/new.html.twig', array(
            'comment' => $comment,
            'form' => $form->createView(),
        ));
    }
    public function upvoteAction(Request $request, Post $postid)
    {
        $metapost = new Metapost();

        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('EntityBundle:Post')->find($postid);
        $deleteForm = $this->createDeleteForm($post);
        $editForm = $this->createForm('EntityBundle\Form\PostType', $post);
        $editForm->handleRequest($request);

        $user = $this->getUser();
        $postid->setVotecount($postid->getVotecount()+1);

        $metapost->setUser($user);
        $metapost->setPost($post);
        $metapost->setVotetype(false);
        $em->persist($metapost);
        $em->flush();
        return $this->redirectToRoute('post_show', array('id' => $post->getId()));
    }




    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('p');
        $post =  $em->getRepository('EntityBundle:Post')->findEntitiesByString($requestString);
        if(!$post) {
            $result['post']['error'] = "Post Not found.";
        } else {
            $result['post'] = $this->getRealEntities($post);
        }
        return new Response(json_encode($result));
    }
    public function getRealEntities($post){
        foreach ($post as $post){
            $realEntities[$post->getId()] = [$post->getTitle()];

        }
        return $realEntities;
    }
    public function solvedAction(Request $request, Post $post)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('EntityBundle:Post')->find($post);
        $post->setSolved(true);

        $editForm = $this->createForm('EntityBundle\Form\PostType', $post);
        $editForm->handleRequest($request);

        $em->persist($post);
        $em->flush();
        return $this->redirectToRoute('post_show', array('id' => $post->getId()));

    }
}
