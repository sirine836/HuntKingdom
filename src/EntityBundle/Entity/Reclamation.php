<?php

namespace EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reclamation
 *
 * @ORM\Table(name="reclamation")
 * @ORM\Entity(repositoryClass="EntityBundle\Repository\ReclamationRepository")
 */
class Reclamation
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
     * @ORM\Column(name="Probleme", type="string", length=255)
     */
    private $probleme;

    /**
     * @var string
     *
     * @ORM\Column(name="Type", type="string", length=255)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="EntityBundle\Entity\Product")
     * @ORM\JoinColumn(name="idProduit", referencedColumnName="id")"
     */
    private $idProduit;


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
     * Set probleme
     *
     * @param string $probleme
     *
     * @return Reclamation
     */
    public function setProbleme($probleme)
    {
        $this->probleme = $probleme;

        return $this;
    }

    /**
     * Get probleme
     *
     * @return string
     */
    public function getProbleme()
    {
        return $this->probleme;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Reclamation
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set idProduit
     *
     * @param string $idProduit
     *
     * @return Reclamation
     */
    public function setIdProduit($idProduit)
    {
        $this->idProduit = $idProduit;

        return $this;
    }

    /**
     * Get idProduit
     *
     * @return string
     */
    public function getIdProduit()
    {
        return $this->idProduit;
    }
}

