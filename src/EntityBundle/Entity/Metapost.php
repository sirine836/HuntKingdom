<?php

namespace EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Metapost
 *
 * @ORM\Table(name="metapost")
 * @ORM\Entity(repositoryClass="EntityBundle\Repository\MetapostRepository")
 */
class Metapost
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="votetype", type="boolean")
     */
    private $votetype;

    /**
     * @ORM\ManyToOne(targetEntity="\UserBundle\Entity\User", inversedBy="metapost")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="\EntityBundle\Entity\Post", inversedBy="metapost")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    private $post;



    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set votetype
     *
     * @param boolean $votetype
     *
     * @return Metapost
     */
    public function setVotetype($votetype)
    {
        $this->votetype = $votetype;

        return $this;
    }

    /**
     * Get votetype
     *
     * @return bool
     */
    public function getVotetype()
    {
        return $this->votetype;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param mixed $post
     */
    public function setPost($post)
    {
        $this->post = $post;
    }

}

