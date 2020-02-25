<?php

namespace EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Professional
 *
 * @ORM\Table(name="professional")
 * @ORM\Entity(repositoryClass="EntityBundle\Repository\ProfessionalRepository")
 */
class Professional
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @var string
     *
     * @ORM\Column(name="proname", type="string", length=50)
     */
    private $proname;

    /**
     * @var string
     *
     * @ORM\Column(name="prosurname", type="string", length=50)
     */
    private $prosurname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     *
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="softskills", type="text")
     */
    private $softskills;

    /**
     * @ORM\Column(type="string",length=255,nullable=true)
     */
    public $image;
    /**
     * @Assert\File(maxSize="500k")
     */
    public $file;


    /**
     * @ORM\OneToOne(targetEntity="\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="idUser", referencedColumnName="id")
     */
    private $user;

    /**
     * Set proname
     *
     * @param string $proname
     *
     * @return Professional
     */
    public function setProname($proname)
    {
        $this->proname = $proname;

        return $this;
    }

    /**
     * Get proname
     *
     * @return string
     */
    public function getProname()
    {
        return $this->proname;
    }

    /**
     * Set prosurname
     *
     * @param string $prosurname
     *
     * @return Professional
     */
    public function setProsurname($prosurname)
    {
        $this->prosurname = $prosurname;

        return $this;
    }

    /**
     * Get prosurname
     *
     * @return string
     */
    public function getProsurname()
    {
        return $this->prosurname;
    }
    /**
     * Set email
     *
     * @param string $email
     *
     * @return Professional
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

    /**
     * Set softskills
     *
     * @param string $softskills
     *
     * @return Professional
     */
    public function setSoftskills($softskills)
    {
        $this->softskills = $softskills;

        return $this;
    }

    /**
     * Get softskills
     *
     * @return string
     */
    public function getSoftskills()
    {
        return $this->softskills;
    }
    public function getWebPath(){
        return null===$this->image ? null : $this->getUploadDir.'/'.$this->image;
    }
    protected function getUploadRootDir(){
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }
    protected function getUploadDir(){
        return 'CV';
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
     * @return Professional
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

