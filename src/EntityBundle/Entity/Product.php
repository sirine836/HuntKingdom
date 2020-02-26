<?php

namespace EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="EntityBundle\Repository\ProductRepository")
 */
class Product
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
     * @ORM\ManyToOne(targetEntity="EntityBundle\Entity\Category")
     * @ORM\JoinColumn(name="idCategory", referencedColumnName="id")"
     */
    private $category;

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="nompr", type="string", length=50)
     */
    private $nompr;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     * @Assert\NotBlank
     * @Assert\GreaterThan (0)

     */
    private $quantity;

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="descrip", type="string", length=255)
     */
    private $descrip;

    /**
     * @var float
     *
     *
     * @ORM\Column(name="prix", type="float")
     */
    private $prix;

    /**
     * @ORM\Column(type="string",length=255,nullable=true)
     */
    public $image;
    /**
     * @Assert\File(maxSize="1000k")
     */
    public $file;


    /**
     * Set nompr
     *
     * @param string $nompr
     *
     * @return Product
     */
    public function setNompr($nompr)
    {
        $this->nompr = $nompr;

        return $this;
    }

    /**
     * Get nompr
     *
     * @return string
     */
    public function getNompr()
    {
        return $this->nompr;
    }

    /**
     * Set descip
     *
     * @param string $descrip
     *
     * @return Product
     */
    public function setDescrip($descrip)
    {
        $this->descrip = $descrip;

        return $this;
    }

    /**
     * Get descrip
     *
     * @return string
     */
    public function getDescrip()
    {
        return $this->descrip;
    }

    /**
     * Set prix
     *
     * @param float $prix
     *
     * @return Product
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

    public function getWebPath(){
        return null===$this->image ? null : $this->getUploadDir.'/'.$this->image;
    }
    protected function getUploadRootDir(){
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }
    protected function getUploadDir(){
        return 'images';
    }
    public function uploadProfilePicture(){
        $this->file->move($this->getUploadRootDir(), $this->file->getClientOriginalName());
        $this->image=$this->file->getClientOriginalName();
        $this->file=null;
    }
    /**
     * Set image
     *
     * @param string $image
     *
     * @return Product
     */
    public function setImage($image){
        $this->image==$image;
        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage(){
        return $this->image;
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

