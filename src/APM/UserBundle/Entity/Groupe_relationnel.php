<?php

namespace APM\UserBundle\Entity;

use APM\UserBundle\Factory\TradeFactory;
use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Rabais_offre;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Groupe_Relationnel
 *
 * @ORM\Table(name="groupe_relationnel")
 * @ORM\Entity(repositoryClass="APM\UserBundle\Repository\Groupe_relationnelRepository")
 * @Vich\Uploadable
 * @UniqueEntity("code")
 */
class Groupe_relationnel extends TradeFactory
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private $code;

    /**
     * @var string
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(min=2, max=155)
     * @ORM\Column(name="designation", type="string", length=255, nullable=false)
     */
    private $designation;

    /**
     * @var boolean
     * @ORM\Column(name="isConversationalGroup", type="boolean", nullable= true)
     */
    private $conversationalGroup;

    /**
     * @var integer
     * @Assert\Choice({0,1,2,3,4,5,6,7,8})
     * @ORM\Column(name="type", type="integer", length=255, nullable=true)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Utilisateur_avm
     *
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="groupesProprietaire")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="proprietaire_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $proprietaire;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="APM\UserBundle\Entity\Individu_to_groupe", mappedBy="groupeRelationnel", cascade={"persist","remove"})
     *
     */
    private $groupeIndividus;

    /**
     * @var Boutique
     *
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Boutique", inversedBy="groupesRelationnels")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="boutique_id", referencedColumnName="id")
     * })
     */
    private $boutique;

    /**
     * @var string
     * @ORM\Column(name="image", type="string", nullable=true)
     */
    private $image;

    /**
     * @Assert\Image()
     * @Vich\UploadableField(mapping="entity_images", fileNameProperty="image")
     * @var File
     *     */
    private $imageFile;

    /**
     * @ORM\Column(name="updatedAt", type="datetime", nullable= true)
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\Column(name="dateCreation", type="datetime", nullable= true)
     * @var \DateTime
     */
    private $dateCreation;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity ="APM\VenteBundle\Entity\Rabais_offre", mappedBy="groupe")
     */
    private $rabais;

    /**
     * Constructor
     * @param string $var
     */
    public function __construct($var)
    {
        $this->groupeIndividus = new ArrayCollection();
        $this->code = "GR" . $var;
        $this->rabais = new ArrayCollection();
        $this->dateCreation = $this->updatedAt = new \DateTime('now');
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
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
     * @return Groupe_relationnel
     */
    public function setDescription($description)
    {
        $this->description = $description;


        return $this;
    }

    /**
     * Get designation
     *
     * @return string
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * Set designation
     *
     * @param string $designation
     *
     * @return Groupe_relationnel
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Groupe_relationnel
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get Id
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
     * @return Groupe_relationnel
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
     * @return Groupe_relationnel
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Add groupeIndividus
     *
     * @param Individu_to_groupe $groupeIndividus
     *
     * @return Groupe_relationnel
     */
    public function addGroupeIndividus(Individu_to_groupe $groupeIndividus)
    {
        $this->groupeIndividus[] = $groupeIndividus;

        return $this;
    }

    /**
     * Remove groupeIndividus
     *
     * @param Individu_to_groupe $groupeIndividus
     */
    public function removeGroupeIndividus(Individu_to_groupe $groupeIndividus)
    {
        $this->groupeIndividus->removeElement($groupeIndividus);
    }

    /**
     * Get groupeIndividus
     *
     * @return Collection
     */
    public function getGroupeIndividus()
    {
        return $this->groupeIndividus;
    }

    public function __toString()
    {
        return $this->designation;
    }

    /**
     * Get boutique
     *
     * @return Boutique
     */
    public function getBoutique()
    {
        return $this->boutique;
    }

    /**
     * Set boutique
     *
     * @param Boutique $boutique
     *
     * @return Groupe_relationnel
     */
    public function setBoutique(Boutique $boutique = null)
    {
        $this->boutique = $boutique;

        return $this;
    }

    /**
     * Add rabai
     *
     * @param Rabais_offre $rabai
     *
     * @return Groupe_relationnel
     */
    public function addRabai(Rabais_offre $rabai)
    {
        $this->rabais[] = $rabai;

        return $this;
    }

    /**
     * Remove rabai
     *
     * @param Rabais_offre $rabai
     */
    public function removeRabai(Rabais_offre $rabai)
    {
        $this->rabais->removeElement($rabai);
    }

    /**
     * Get rabais
     *
     * @return Collection
     */
    public function getRabais()
    {
        return $this->rabais;
    }

    /**
     * @return boolean
     */
    public function isConversationalGroup()
    {
        return $this->conversationalGroup;
    }

    /**
     * Get conversationalGroup
     *
     * @return boolean
     */
    public function getConversationalGroup()
    {
        return $this->conversationalGroup;
    }

    /**
     * @param boolean $conversationalGroup
     */
    public function setConversationalGroup(bool $conversationalGroup)
    {
        $this->conversationalGroup = $conversationalGroup;
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
     * @return Groupe_relationnel
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
     * @return Groupe_relationnel
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Groupe_relationnel
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }
}
