<?php

namespace EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Facture
 *
 * @ORM\Table(name="facture")
 * @ORM\Entity(repositoryClass="EntityBundle\Repository\FactureRepository")
 */
class Facture
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
     * @ORM\Column(name="numtel", type="string", length=255)
     */
    private $numtel;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=255)
     */
    private $adresse;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDeLivraison", type="datetime")
     */
    private $dateDeLivraison;
    /**
     * @ORM\OneToOne(targetEntity="\EntityBundle\Entity\Panier")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $panier;


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
     * Set numtel
     *
     * @param string $numtel
     *
     * @return Facture
     */
    public function setNumtel($numtel)
    {
        $this->numtel = $numtel;

        return $this;
    }

    /**
     * Get numtel
     *
     * @return string
     */
    public function getNumtel()
    {
        return $this->numtel;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Facture
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set dateDeLivraison
     *
     * @param \DateTime $dateDeLivraison
     *
     * @return Facture
     */
    public function setDateDeLivraison($dateDeLivraison)
    {
        $this->dateDeLivraison = $dateDeLivraison;

        return $this;
    }

    /**
     * Get dateDeLivraison
     *
     * @return \DateTime
     */
    public function getDateDeLivraison()
    {
        return $this->dateDeLivraison;
    }

    /**
     * @return mixed
     */
    public function getPanier()
    {
        return $this->panier;
    }

    /**
     * @param mixed $panier
     */
    public function setPanier($panier)
    {
        $this->panier = $panier;
    }

}

