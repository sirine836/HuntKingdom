<?php

namespace EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Evaluations
 *
 * @ORM\Table(name="evaluations")
 * @ORM\Entity(repositoryClass="EntityBundle\Repository\EvaluationsRepository")
 */
class Evaluations
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
     * @var int
     *
     * @ORM\Column(name="note", type="integer")
     */
    private $note;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="string", length=255)
     */
    private $commentaire;


    /**
     * @ORM\ManyToOne(targetEntity="\EntityBundle\Entity\Events")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     */
    private $Events;

    /**
     * @ORM\ManyToOne(targetEntity="\UserBundle\Entity\User", inversedBy="metacomment")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

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
     * Set note
     *
     * @param integer $note
     *
     * @return Evaluations
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return int
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return Evaluations
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * @return mixed
     */
    public function getEvents()
    {
        return $this->Events;
    }

    /**
     * @param mixed $Events
     */
    public function setEvents($Events)
    {
        $this->Events = $Events;
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


}

