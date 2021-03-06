<?php

namespace APM\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Exclude;

/**
 * @ORM\Entity(repositoryClass="APM\UserBundle\Repository\UtilisateurRepository")
 * @Vich\Uploadable
 * @ORM\Table(name="utilisateur")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"admin" = "Admin", "utilisateur_avm" = "Utilisateur_avm"})
 * @ExclusionPolicy("all")
 */
abstract class Utilisateur extends BaseUser
{
    /**
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_user_details", "others_user_details", "reset"})
     */
    protected $username;

    /**
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_user_details", "others_user_details"})
     */
    protected $email;

    /**
     * @var string
     * @Assert\Length(min=2)
     * @ORM\Column(name="profession", type="string", length=255, nullable=true)
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_user_details", "others_user_details"})
     */
    protected $profession;

    /**
     * @var \DateTime
     * @Assert\DateTime
     * @ORM\Column(name="dateEnregistrement", type="datetime", nullable=true)
     * @Expose
     * @Groups({"owner_user_details"})
     */
    protected $dateEnregistrement;

    /**
     * @var boolean
     * @ORM\Column(name="isLoggedIn", type="boolean", nullable=true)
     * @Expose
     * @Groups({"owner_user_details", "others_user_details"})
     */
    protected $isLoggedIn;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_user_details", "others_user_details"})
     */
    protected $id;

    /**
     * @var string
     * @Expose
     * @Groups({"owner_user_details"})
     * @Assert\NotBlank
     * @Assert\Length(min=2, max=100)
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    protected $nom;
    /**
     * @var string
     * @Assert\Length(min=2, max=100)
     * @ORM\Column(name="prenom", type="string", length=255, nullable=true)
     * @Expose
     * @Groups({"owner_user_details", "others_user_details"})
     */
    protected $prenom;
    /**
     * @var \DateTime
     * @Expose
     * @Groups({"owner_user_details"})
     * @ORM\Column(name="dateNaissance", type="datetime", nullable=true)
     */
    protected $dateNaissance;

    /**
     * @Expose
     * @Groups({"owner_user_details"})
     * @Assert\Country()
     * @ORM\Column(name="pays", type="string", length=255, nullable=true)
     *
     */
    protected $pays;
    /**
     * @var string
     * @Expose
     * @Groups({"owner_user_details", "others_user_details"})
     * @ORM\Column(name="genre", type="string", nullable=true)
     */
    protected $genre;

    /**
     * @var integer
     * @Expose
     * @Groups({"owner_user_details"})
     * @Assert\Range(min=100,)
     * @ORM\Column(name="telephone", type="integer", nullable=true)
     *
     */
    protected $telephone;

    /**
     * @var string
     * @Expose
     * @Groups({"owner_list", "owner_user_details", "others_user_details"})
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    protected $code;

    /**
     * @var integer
     * @Expose
     * @Groups({"owner_user_details"})
     * @Assert\Choice({0,1,2,3})
     * @ORM\Column(name="etatCompte", type="integer", nullable=true)
     */
    protected $etatDuCompte;

    /**
     * @var string
     * @Assert\Length(min=2, max=255)
     * @ORM\Column(name="adresse", type="string", nullable=true)
     * @Expose
     * @Groups({"owner_user_details"})
     */
    protected $adresse;
    /**
     * @Expose
     * @Groups({"owner_user_details", "others_user_details"})
     * @ORM\Column(name="updatedAt", type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $updatedAt;
    /**
     * @Exclude
     * @Assert\File(
     *     maxSize = "5024k",
     *     mimeTypes = {"image/jpeg", "image/png", "image/gif"},
     *     mimeTypesMessage = "Please upload a valid image"
     * )
     * @Vich\UploadableField(mapping="user_images", fileNameProperty="image")
     * @var UploadedFile
     */
    private $imagefile;
    /**
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_user_details", "others_user_details"})
     * @var string
     * @Type("string")
     * @ORM\Column(name="image", type="string", nullable=true)
     */
    private $image;

    public function getImagefile()
    {
        return $this->imagefile;
    }

    public function setImagefile(File $image = null)
    {
        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        // if 'updatedAt' is not defined in your entity, use another property
        $this->imagefile = $image;
        if ($image) {
            $this->updatedAt = new \DateTime('now');
        }
    }


    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Utilisateur
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Utilisateur
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return \DateTime
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     *
     * @return Utilisateur
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * Get pays
     *
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set pays
     *
     * @param string $pays
     *
     * @return Utilisateur
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get genre
     *
     * @return string
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * Set genre
     *
     * @param string $genre
     *
     * @return Utilisateur
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Utilisateur
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Utilisateur
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    public function __toString()
    {
        return $this->username;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Utilisateur
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Utilisateur
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get profession
     *
     * @return string
     */
    public function getProfession()
    {
        return $this->profession;
    }

    /**
     * Set profession
     *
     * @param string $profession
     *
     * @return Utilisateur
     */
    public function setProfession($profession)
    {
        $this->profession = $profession;

        return $this;
    }

    /**
     * Get dateEnregistrement
     *
     * @return \DateTime
     */
    public function getDateEnregistrement()
    {
        return $this->dateEnregistrement;
    }

    /**
     * Set dateEnregistrement
     *
     * @param \DateTime $dateEnregistrement
     *
     * @return Utilisateur
     */
    public function setDateEnregistrement($dateEnregistrement)
    {
        $this->dateEnregistrement = $dateEnregistrement;

        return $this;
    }

    /**
     * Get etatDuCompte
     *
     * @return integer
     */
    public function getEtatDuCompte()
    {
        return $this->etatDuCompte;
    }

    /**
     * Set etatDuCompte
     *
     * @param integer $etatDuCompte
     *
     * @return Utilisateur
     */
    public function setEtatDuCompte($etatDuCompte)
    {
        $this->etatDuCompte = $etatDuCompte;

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
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Utilisateur
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get isLoggedIn
     *
     * @return boolean
     */
    public function getIsLoggedIn()
    {
        return $this->isLoggedIn;
    }

    /**
     * Set isLoggedIn
     *
     * @param boolean $isLoggedIn
     *
     * @return Utilisateur
     */
    public function setIsLoggedIn($isLoggedIn)
    {
        $this->isLoggedIn = $isLoggedIn;

        return $this;
    }
}
