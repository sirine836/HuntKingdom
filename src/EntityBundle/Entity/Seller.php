<?php

namespace EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Seller
 *
 * @ORM\Table(name="seller")
 * @ORM\Entity(repositoryClass="EntityBundle\Repository\SellerRepository")
 */
class Seller
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
     * @ORM\OneToOne(targetEntity="\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="idUser", referencedColumnName="id")
     */
    private $user;

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
     * @var string
     *
     * @ORM\Column(name="sellername", type="string", length=50)
     */
    private $sellername;

    /**
     * @var string
     *
     * @ORM\Column(name="rcs", type="string", length=100)
     */
    private $rcs;

    /**
     * @var string
     *
     * @ORM\Column(name="taxnumber", type="string", length=100)
     */
    private $taxnumber;

    /**
     * @var int
     *
     * @ORM\Column(name="tva", type="integer")
     */
    private $tva;

    /**
     * @var int
     *
     * @ORM\Column(name="siren", type="integer")
     */
    private $siren;

    /**
     * @var int
     *
     * @ORM\Column(name="fax", type="integer")
     */
    private $fax;

    /**
     * @var int
     *
     * @ORM\Column(name="phonenumber", type="integer")
     */
    private $phonenumber;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;
    /**
     * @ORM\Column(type="string",length=255,nullable=true)
     */
    public $image;
    /**
     * @Assert\File(maxSize="500k")
     */
    public $file;

    /**
     * Set sellername
     *
     * @param string $sellername
     *
     * @return Seller
     */
    public function setSellername($sellername)
    {
        $this->sellername = $sellername;

        return $this;
    }

    /**
     * Get sellername
     *
     * @return string
     */
    public function getSellername()
    {
        return $this->sellername;
    }

    /**
     * Set rcs
     *
     * @param string $rcs
     *
     * @return Seller
     */
    public function setRcs($rcs)
    {
        $this->rcs = $rcs;

        return $this;
    }

    /**
     * Get rcs
     *
     * @return string
     */
    public function getRcs()
    {
        return $this->rcs;
    }

    /**
     * Set taxnumber
     *
     * @param string $taxnumber
     *
     * @return Seller
     */
    public function setTaxnumber($taxnumber)
    {
        $this->taxnumber = $taxnumber;

        return $this;
    }

    /**
     * Get taxnumber
     *
     * @return string
     */
    public function getTaxnumber()
    {
        return $this->taxnumber;
    }

    /**
     * Set tva
     *
     * @param integer $tva
     *
     * @return Seller
     */
    public function setTva($tva)
    {
        $this->tva = $tva;

        return $this;
    }

    /**
     * Get tva
     *
     * @return int
     */
    public function getTva()
    {
        return $this->tva;
    }

    /**
     * Set siren
     *
     * @param integer $siren
     *
     * @return Seller
     */
    public function setSiren($siren)
    {
        $this->siren = $siren;

        return $this;
    }

    /**
     * Get siren
     *
     * @return int
     */
    public function getSiren()
    {
        return $this->siren;
    }

    /**
     * Set fax
     *
     * @param integer $fax
     *
     * @return Seller
     */
    public function setFax($fax)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax
     *
     * @return int
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set phonenumber
     *
     * @param integer $phonenumber
     *
     * @return Seller
     */
    public function setPhonenumber($phonenumber)
    {
        $this->phonenumber = $phonenumber;

        return $this;
    }

    /**
     * Get phonenumber
     *
     * @return int
     */
    public function getPhonenumber()
    {
        return $this->phonenumber;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Seller
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    public function getWebPath(){
        return null===$this->image ? null : $this->getUploadDir.'/'.$this->image;
    }
    protected function getUploadRootDir(){
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }
    protected function getUploadDir(){
        return 'license';
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
     * @return Seller
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

