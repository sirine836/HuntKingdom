<?php

namespace EntityBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Question
 *
 * @ORM\Table(name="question")
 * @ORM\Entity(repositoryClass="EntityBundle\Repository\QuestionRepository")
 */
class Question
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
     * @var string
     *
     * @ORM\Column(name="wording", type="string", length=255)
     */
    private $wording;

    /**
    * @ORM\OneToMany(targetEntity="Proposition", mappedBy="question", orphanRemoval=true)
    */
    private $propositions;

    /**
     * @var ArrayCollection
    * @ORM\OneToMany(targetEntity="EntityBundle\Entity\Answer", mappedBy="question", orphanRemoval=true)
    */
    private $answers;

    /**
     * @return mixed
     */
    public function getPropositions()
    {
        return $this->propositions;
    }

    /**
     * @param mixed $propositions
     */
    public function setPropositions($propositions)
    {
        $this->propositions = $propositions;
    }

    /**
     * @return Collection
     */
    public function getAnswers(){
        return $this->answers;
    }


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
     * Set wording
     *
     * @param string $wording
     *
     * @return Question
     */
    public function setWording($wording)
    {
        $this->wording = $wording;

        return $this;
    }

    /**
     * Get wording
     *
     * @return string
     */
    public function getWording()
    {
        return $this->wording;
    }
}

