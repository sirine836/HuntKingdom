<?php

namespace EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="EntityBundle\Repository\CategoryRepository")
 */
class Category
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
     * @ORM\ManyToOne(targetEntity="EntityBundle\Entity\Product")
     * @ORM\JoinColumn(name="idProduct", referencedColumnName="id")"
     */
    private $Product;

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->Product;
    }

    /**
     * @param mixed $Product
     */
    public function setProduct($Product)
    {
        $this->Product = $Product;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="nomcat", type="string", length=50)
     */
    private $nomcat;
    /**
     * @var string
     *
     * @ORM\Column(name="souscat", type="string", length=50)
     */
    private $souscat;

    /**
     * @return string
     */
    public function getSouscat()
    {
        return $this->souscat;
    }

    /**
     * @param string $souscat
     */
    public function setSouscat($souscat)
    {
        $this->souscat = $souscat;
    }


    /**
     * Set nomcat
     *
     * @param string $nomcat
     *
     * @return Category
     */
    public function setNomcat($nomcat)
    {
        $this->nomcat = $nomcat;

        return $this;
    }

    /**
     * Get nomcat
     *
     * @return string
     */
    public function getNomcat()
    {
        return $this->nomcat;
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
}

