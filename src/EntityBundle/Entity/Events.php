<?php

namespace EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Events
 *
 * @ORM\Table(name="events")
 * @ORM\Entity(repositoryClass="EntityBundle\Repository\EventsRepository")
 */
class Events
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
     * @ORM\Column(name="titre", type="string", length=255)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="nbrPlaces", type="integer")
     */
    private $nbrPlaces;

    /**
     * @var bool
     *
     * @ORM\Column(name="accesGratuit", type="boolean", nullable=true)
     */
    private $accesGratuit;

    /**
     * @var float
     *
     * @ORM\Column(name="prix", type="float", nullable=true)
     */
    private $prix;

    /**
     * @var string
     *
     * @ORM\Column(name="localisation", type="string", length=255)
     */
    private $localisation;

    /**
     * @ORM\ManyToOne(targetEntity="\EntityBundle\Entity\Professional")
     * @ORM\JoinColumn(name="idPro", referencedColumnName="id")
     */
    private $professional;

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
     * Set titre
     *
     * @param string $titre
     *
     * @return Events
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Events
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Events
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set nbrPlaces
     *
     * @param integer $nbrPlaces
     *
     * @return Events
     */
    public function setNbrPlaces($nbrPlaces)
    {
        $this->nbrPlaces = $nbrPlaces;

        return $this;
    }

    /**
     * Get nbrPlaces
     *
     * @return int
     */
    public function getNbrPlaces()
    {
        return $this->nbrPlaces;
    }

    /**
     * Set accesGratuit
     *
     * @param boolean $accesGratuit
     *
     * @return Events
     */
    public function setAccesGratuit($accesGratuit)
    {
        $this->accesGratuit = $accesGratuit;

        return $this;
    }

    /**
     * Get accesGratuit
     *
     * @return bool
     */
    public function getAccesGratuit()
    {
        return $this->accesGratuit;
    }

    /**
     * Set prix
     *
     * @param float $prix
     *
     * @return Events
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return float
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set localisation
     *
     * @param string $localisation
     *
     * @return Events
     */
    public function setLocalisation($localisation)
    {
        $this->localisation = $localisation;

        return $this;
    }

    /**
     * Get localisation
     *
     * @return string
     */
    public function getLocalisation()
    {
        return $this->localisation;
    }


    /**
     * @return mixed
     */
    public function getProfessional()
    {
        return $this->professional;
    }

    /**
     * @param mixed $professional
     */
    public function setProfessional($professional)
    {
        $this->professional = $professional;
    }

}

