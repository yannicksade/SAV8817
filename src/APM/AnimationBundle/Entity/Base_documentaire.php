<?php

namespace APM\AnimationBundle\Entity;

use APM\AnimationBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Offre;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Exclude;
/**
 * Base_documentaire
 *
 * @ORM\Table(name="base_documentaire")
 * @ORM\Entity(repositoryClass="APM\AnimationBundle\Repository\Base_documentaireRepository")
 * @Vich\Uploadable
 * @UniqueEntity("code")
 * @ExclusionPolicy("all")
 */
class Base_documentaire extends TradeFactory
{

    /**
     * @var string
     * @Expose
     * @Groups({"owner_list", "others_document_details", "owner_document_details"})
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @var string
     * @Expose
     * @Groups({"others_list", "owner_list", "others_document_details", "owner_document_details"})
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     * @Expose
     * @Groups({"others_list", "owner_list", "others_document_details", "owner_document_details"})
     * @Assert\NotNull
     * @Assert\Length(min=2, max=55)
     * @ORM\Column(name="objet", type="string", length=255, nullable=true)
     */
    private $objet;

    /**
     * @Exclude
     * @Assert\File( maxSize = "1660932k"
     * )
     * @Vich\UploadableField(mapping="animation_files", fileNameProperty="animation")
     * @var File
     */
    private $animationfile;

    /**
     * @var string
     * @Expose
     * @Groups({"others_advert_details", "owner_advert_details"})
     * @ORM\Column(name="animation", type="string")
     */
    private $animation;


    /**
     * @Exclude
     * @Assert\File(
     *     maxSize = "5024k",
     *     mimeTypes = {"image/jpeg", "image/png", "image/gif"},
     *     mimeTypesMessage = "Please upload a valid PDF"
     * )
     * @Vich\UploadableField(mapping="animation_images", fileNameProperty="image")
     * @var File
     */
    private $imagefile;

    /**
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_animation_details", "others_animation_details"})
     * @var string
     * @Type("string")
     * @ORM\Column(name="image", type="string", nullable=true)
     */
    private $image;


    /**
     * @var \DateTime
     * @Expose
     * @Groups({"others_document_details", "owner_document_details"})
     * @ORM\Column(name="updatedAt", type="datetime")
     * 
     */
    private $updatedAt;

    /**
     * @var integer
     * @Expose
     * @Groups({"others_list", "owner_list", "others_document_details", "owner_document_details"})
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     * @Expose
     * @Groups({"others_document_details", "owner_document_details"})
     * @Assert\DateTime
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var Utilisateur_avm
     * 
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="documents")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="proprietaire_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $proprietaire;
    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\VenteBundle\Entity\Offre", mappedBy="document")
     */
    private $produits;

    /**
     * Base_documentaire constructor.
     * @param string $var
     */
    public function __construct($var)
    {
        $this->code = "BD" . $var;
        $this->date = $this->updatedAt =  new \DateTime();
    }

    public function getAnimationfile()
    {
        return $this->animationfile;
    }

    public function setAnimationfile(File $brochure = null)
    {
        $this->animationfile = $brochure;
        
        if ($brochure) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getImagefile()
    {
        return $this->imagefile;
    }

    public function setImagefile(File $brochure = null)
    {
        $this->imagefile = $brochure;

        if ($brochure) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
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
     * Set description
     *
     * @param string $description
     *
     * @return Base_documentaire
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get objet
     *
     * @return string
     */
    public function getObjet()
    {
        return $this->objet;
    }

    /**
     * Set objet
     *
     * @param string $objet
     *
     * @return Base_documentaire
     */
    public function setObjet($objet)
    {
        $this->objet = $objet;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get proprietaire
     *
     * @return Utilisateur_avm
     */
    public function getProprietaire()
    {
        return $this->proprietaire;
    }

    /**
     * Set proprietaire
     *
     * @param Utilisateur_avm $proprietaire
     *
     * @return Base_documentaire
     */
    public function setProprietaire(Utilisateur_avm $proprietaire)
    {
        $this->proprietaire = $proprietaire;

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
     * @return Base_documentaire
     */
    public function setCode($code)
    {
        $this->code = $code;

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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Base_documentaire
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    public function __toString()
    {
        return $this->objet;
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
     * @return Base_documentaire
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Add produit
     *
     * @param \APM\VenteBundle\Entity\Offre $produit
     *
     * @return Base_documentaire
     */
    public function addProduit(\APM\VenteBundle\Entity\Offre $produit)
    {
        $this->produits[] = $produit;

        return $this;
    }

    /**
     * Remove produit
     *
     * @param \APM\VenteBundle\Entity\Offre $produit
     */
    public function removeProduit(\APM\VenteBundle\Entity\Offre $produit)
    {
        $this->produits->removeElement($produit);
    }

    /**
     * Get produits
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProduits()
    {
        return $this->produits;
    }

    /**
     * Get animation
     *
     * @return string
     */
    public function getAnimation()
    {
        return $this->animation;
    }

    /**
     * Set animation
     *
     * @param string $animation
     *
     * @return Base_documentaire
     */
    public function setAnimation($animation)
    {
        $this->animation = $animation;

        return $this;
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
     * @return Base_documentaire
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }
}
