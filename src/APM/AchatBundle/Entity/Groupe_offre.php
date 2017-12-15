<?php

namespace APM\AchatBundle\Entity;

use APM\AchatBundle\Factory\TradeFactory;
use Symfony\Component\Validator\Constraints as Assert;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Offre;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Exclude;
/**
 * Groupe_offre
 *
 * @ORM\Table(name="Groupe_offre")
 * @ORM\Entity(repositoryClass="APM\AchatBundle\Repository\Groupe_offreRepository")
 * @UniqueEntity("code")
 * @ExclusionPolicy("all")
 */
class Groupe_offre extends TradeFactory
{

    /**
     * @var string
     * @Expose
     * @Groups({"owner_list", "others_groupeO_details", "owner_groupeO_details"})
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private $code;

    /**
     * @var \DateTime
     * @Expose
     * @Groups({"owner_groupeO_details"})
     * @Assert\DateTime
     * @ORM\Column(name="dateDeVigueur", type="datetime", nullable=true)
     */
    private $dateDeVigueur;

    /**
     * @var \DateTime
     * @Expose
     * @Groups({"owner_groupeO_details"})
     * @Assert\DateTime
     * @ORM\Column(name="dateCreation", type="datetime", nullable=true)
     */
    private $dateCreation;


    /**
     * @var integer
     * @Expose
     * @Groups({"owner_groupeO_details"})
     * @ORM\Column(name="propriete", type="integer", nullable=true)
     * @Assert\Range(min=0, max=4)
     */
    private $propriete;

    /**
     * @var string
     * @Expose
     * @Groups({"owner_list", "others_list", "others_groupeO_details", "owner_groupeO_details"})
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     * @Assert\Length(min=2, max=254, minMessage="Ce champs doit contenir au moins {{limit}} caracteres.",
     * maxMessage="Ce champs doit contenir au plus {{limit}} caracteres.")
     */
    private $description;

    /**
     * @var boolean
     * @Expose
     * @Groups({"owner_groupeO_details"})
     * @Assert\NotNull
     * @Assert\Choice({0,1})
     * @ORM\Column(name="estRecurrent", type="boolean", nullable=false)
     */
    private $recurrent;

    /**
     * @var string
     * @Expose
     * @Groups({"owner_list", "others_list", "others_groupeO_details", "owner_groupeO_details"})
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=50)
     * @ORM\Column(name="designation", type="string", length=255, nullable=false)
     */
    private $designation;

    /**
     * @var integer
     * @Expose
     * @Groups({"owner_list", "others_list", "others_groupeO_details", "owner_groupeO_details"})
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Utilisateur_avm
     * @Expose
     * @Groups({"others_groupeO_details", "owner_groupeO_details"})
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="groupesOffres")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="createur_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $createur;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="APM\VenteBundle\Entity\Offre")
     */
    private $offres;

    /**
     * Constructor
     * @param string $var
     */
    public function __construct($var)
    {
        $this->offres = new ArrayCollection();
        $this->code = "GO" . $var;
        $this->dateCreation = new \DateTime();
    }

    /**
     * Get dateDeVigueur
     *
     * @return \DateTime
     */
    public function getDateDeVigueur()
    {
        return $this->dateDeVigueur;
    }

    /**
     * Set dateDeVigueur
     *
     * @param \DateTime $dateDeVigueur
     *
     * @return Groupe_offre
     */
    public function setDateDeVigueur($dateDeVigueur)
    {
        $this->dateDeVigueur = $dateDeVigueur;

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
     * Set description
     *
     * @param string $description
     *
     * @return Groupe_offre
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get recurrent
     *
     * @return boolean
     */
    public function isRecurrent()
    {
        return $this->recurrent;
    }

    /**
     * Get recurrent
     *
     * @return boolean
     */
    public function getRecurrent()
    {
        return $this->recurrent;
    }

    /**
     * Set recurrent
     *
     * @param boolean $recurrent
     *
     * @return Groupe_offre
     */
    public function setRecurrent($recurrent)
    {
        $this->recurrent = $recurrent;

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
     * @return Groupe_offre
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;

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
     * Get createur
     *
     * @return Utilisateur_avm
     */
    public function getCreateur()
    {
        return $this->createur;
    }

    /**
     * Set createur
     *
     * @param Utilisateur_avm $createur
     *
     * @return Groupe_offre
     */
    public function setCreateur(Utilisateur_avm $createur)
    {
        $this->createur = $createur;

        return $this;
    }

    /**
     * Add offre
     *
     * @param Offre $offre
     *
     * @return Groupe_offre
     */
    public function addOffre(Offre $offre)
    {
        $this->offres[] = $offre;
        return $this;
    }

    /**
     * Remove offre
     *
     * @param Offre $offre
     */
    public function removeOffre(Offre $offre)
    {
        $this->offres->removeElement($offre);
    }

    /**
     * Get offres
     *
     * @return Collection
     */
    public function getOffres()
    {
        return $this->offres;
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
     * @return Groupe_offre
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get propriete
     *
     * @return integer
     */
    public function getPropriete()
    {
        return $this->propriete;
    }

    /**
     * Set propriete
     *
     * @param integer $propriete
     *
     * @return Groupe_offre
     */
    public function setPropriete($propriete)
    {
        $this->propriete = $propriete;

        return $this;
    }

    public function __toString()
    {
        return $this->designation;
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
     * @return Groupe_offre
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }
}
