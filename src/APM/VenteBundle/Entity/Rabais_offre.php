<?php

namespace APM\VenteBundle\Entity;

use APM\UserBundle\Entity\Groupe_relationnel;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Factory\TradeFactory;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
/**
 * Rabais_offre
 *
 * @ORM\Table(name="rabais_offre")
 * @ORM\Entity(repositoryClass="APM\VenteBundle\Repository\Rabais_offreRepository")
 * @UniqueEntity("code")
 * @ExclusionPolicy("all")
 */
class Rabais_offre extends TradeFactory
{
    /**
     * @var integer
     * @Assert\NotNull
     * @Expose
     * @Groups({"owner_rabais_details", "others_rabais_details"})
     * @ORM\Column(name="etat", type="integer", nullable=false)
     */
    private $etat;

    /**
     * @var \DateTime|null
     * @Expose
     * @Groups({"owner_rabais_details", "others_rabais_details"})
     * @Assert\DateTime
     * @ORM\Column(name="dateExpiration", type="datetime", nullable=true)
     */
    private $dateExpiration;

    /**
     * @var boolean
     * @Expose
     * @Groups({"owner_rabais_details"})
     * @Assert\Choice({0,1})
     * @ORM\Column(name="Restricted", type="boolean", nullable=true)
     */
    private $restreint;

    /**
     * @var boolean
     * @Expose
     * @Groups({"owner_rabais_details", "others_rabais_details"})
     * @Assert\Choice({0,1})
     * @ORM\Column(name="permanence", type="boolean", nullable=true)
     */
    private $permanence;


    /**
     * @var string
     * @Groups({"owner_rabais_details", "others_rabais_details"})
     * @ORM\Column(name="valeur", type="decimal", nullable=true)
     */
    private $valeur;

    /**
     * @var string
     * @Expose
     * @Groups({"owner_list", "owner_rabais_details", "others_rabais_details"})
     * @ORM\Column(name="code", type="string", length=255 , nullable=false)
     */
    private $code;

    /**
     * @var \DateTime|null
     * @Expose
     * @Groups({"owner_rabais_details", "others_rabais_details"})
     * @Assert\DateTime
     * @ORM\Column(name="dateLimite", type="datetime", nullable=true)
     */
    private $dateLimite;

    /**
     * @var integer
     * @Expose
     * @Groups({"owner_rabais_details", "others_rabais_details"})
     * @Assert\Range(min=0, max=30) avec <null> le nombre d'occurrence est indefini
     * @ORM\Column(name="nombreUtilisation", type="smallint", nullable=true)
     */
    private $nombreUtilisation;

    /**
     * @var string
     * @Expose
     * @Groups({"owner_rabais_details","others_rabais_details"})
     * @ORM\Column(name="prixUpdate", type="decimal",nullable=true)
     */
    private $prixUpdate;

    /**
     * @var string
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_rabais_details", "others_rabais_details"})
     * @ORM\Column(name="$description", type="string",nullable=true)
     */
    private $description;

    /**
     * @var integer
     * @Expose
     * @Groups({"owner_rabais_details", "others_rabais_details"})
     * @Assert\GreaterThan(0)
     * @ORM\Column(name="quantiteMin", type="integer", nullable=true)
     */
    private $quantiteMin;

    /**
     * @var integer
     * @Expose
     * @Groups({"owner_rabais_details", "others_rabais_details", "owner_list", "others_list"})
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Utilisateur_avm
     * @Expose
     * @Groups({"owner_rabais_details","others_rabais_details"})
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="rabaisAccordes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vendeur_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $vendeur;

    /**
     * @var Utilisateur_avm
     * @Expose
     * @Groups({"owner_rabais_details", "others_rabais_details"})
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="rabaisRecus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="beneficiaire_id", referencedColumnName="id")
     * })
     */
    private $beneficiaireRabais;

    /**
     * @var Offre
     * @Expose
     * @Groups({"owner_rabais_details","others_rabais_details"})
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Offre", inversedBy="rabais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="offre_id", referencedColumnName="id", nullable = false)
     * })
     */
    private $offre;


    /**
     * @var Groupe_relationnel
     * @Expose
     * @Groups({"owner_rabais_details", "others_rabais_details"})
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Groupe_relationnel", inversedBy="rabais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groupe_id", referencedColumnName="id")
     * })
     */
    private $groupe;

    /**
     * @var \DateTime
     * @Expose
     * @Groups({"owner_rabais_details", "others_rabais_details"})
     * @Assert\DateTime
     * @ORM\Column(name="dateCreation", type="datetime", nullable=false)
     */
    private $date;

    /**
     * Constructor
     * @param string $var
     */
    public function __construct($var)
    {
        $this->code = "RB" . $var;
    }

    /**
     * Get datelLimite
     *
     * @return \DateTime
     */
    public function getDateLimite()
    {
        return $this->dateLimite;
    }

    /**
     * Set dateLimite
     *
     * @param \DateTime $dateLimite
     *
     * @return Rabais_offre
     */
    public function setDateLimite($dateLimite)
    {
        $this->dateLimite = $dateLimite;

        return $this;
    }

    /**
     * Get nombreDefois
     *
     * @return integer
     */
    public function getNombreDefois()
    {
        return $this->nombreDefois;
    }

    /**
     * Set nombreDefois
     *
     * @param integer $nombreDefois
     *
     * @return Rabais_offre
     */
    public function setNombreDefois($nombreDefois)
    {
        $this->nombreDefois = $nombreDefois;

        return $this;
    }

    /**
     * Get prixUpdate
     *
     * @return string
     */
    public function getPrixUpdate()
    {
        return $this->prixUpdate;
    }

    /**
     * Set prixUpdate
     *
     * @param string $prixUpdate
     *
     * @return Rabais_offre
     */
    public function setPrixUpdate($prixUpdate)
    {
        $this->prixUpdate = $prixUpdate;

        return $this;
    }

    /**
     * Get quantitemin
     *
     * @return integer
     */
    public function getQuantiteMin()
    {
        return $this->quantiteMin;
    }

    /**
     * Set quantiteMin
     *
     * @param integer $quantiteMin
     *
     * @return Rabais_offre
     */
    public function setQuantiteMin($quantiteMin)
    {
        $this->quantiteMin = $quantiteMin;

        return $this;
    }

    /**
     * Get vendeur
     *
     * @return Utilisateur_avm
     */
    public function getVendeur()
    {
        return $this->vendeur;
    }

    /**
     * Set vendeur
     *
     * @param Utilisateur_avm $vendeur
     *
     * @return Rabais_offre
     */
    public function setVendeur(Utilisateur_avm $vendeur)
    {
        $this->vendeur = $vendeur;

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
     * Get beneficiaireRabais
     *
     * @return Utilisateur_avm
     */
    public function getBeneficiaireRabais()
    {
        return $this->beneficiaireRabais;
    }

    /**
     * Set beneficairerRabais
     *
     * @param Utilisateur_avm $beneficiaireRabais
     *
     * @return Rabais_offre
     */
    public function setBeneficiaireRabais(Utilisateur_avm $beneficiaireRabais)
    {
        $this->beneficiaireRabais = $beneficiaireRabais;

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
     * @return Rabais_offre
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get offre
     *
     * @return Offre
     */
    public function getOffre()
    {
        return $this->offre;
    }

    /**
     * Set offre
     *
     * @param Offre $offre
     *
     * @return Rabais_offre
     */
    public function setOffre(Offre $offre = null)
    {
        $this->offre = $offre;

        return $this;
    }

    public function __toString()
    {
        return $this->code;
    }

    /**
     * Get groupe
     *
     * @return \APM\UserBundle\Entity\Groupe_relationnel
     */
    public function getGroupe()
    {
        return $this->groupe;
    }

    /**
     * Set groupe
     *
     * @param \APM\UserBundle\Entity\Groupe_relationnel $groupe
     *
     * @return Rabais_offre
     */
    public function setGroupe(\APM\UserBundle\Entity\Groupe_relationnel $groupe = null)
    {
        $this->groupe = $groupe;

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
     * @return Rabais_offre
     */
    public function setDate($date)
    {
        $this->date = $date;

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
     * @return Rabais_offre
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get etat
     *
     * @return integer
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set etat
     *
     * @param integer $etat
     *
     * @return Rabais_offre
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get dateExpiration
     *
     * @return \DateTime
     */
    public function getDateExpiration()
    {
        return $this->dateExpiration;
    }

    /**
     * Set dateExpiration
     *
     * @param \DateTime $dateExpiration
     *
     * @return Rabais_offre
     */
    public function setDateExpiration($dateExpiration)
    {
        $this->dateExpiration = $dateExpiration;

        return $this;
    }

    /**
     * Get restreint
     *
     * @return boolean
     */
    public function getRestreint()
    {
        return $this->restreint;
    }

    /**
     * Set restreint
     *
     * @param boolean $restreint
     *
     * @return Rabais_offre
     */
    public function setRestreint($restreint)
    {
        $this->restreint = $restreint;

        return $this;
    }

    /**
     * Get nombreUtilisation
     *
     * @return integer
     */
    public function getNombreUtilisation()
    {
        return $this->nombreUtilisation;
    }

    /**
     * Set nombreUtilisation
     *
     * @param integer $nombreUtilisation
     *
     * @return Rabais_offre
     */
    public function setNombreUtilisation($nombreUtilisation)
    {
        $this->nombreUtilisation = $nombreUtilisation;

        return $this;
    }

    /**
     * Get permanence
     *
     * @return boolean
     */
    public function getPermanence()
    {
        return $this->permanence;
    }

    /**
     * Set permanence
     *
     * @param boolean $permanence
     *
     * @return Rabais_offre
     */
    public function setPermanence($permanence)
    {
        $this->permanence = $permanence;

        return $this;
    }

    /**
     * Get valeur
     *
     * @return string
     */
    public function getValeur()
    {
        return $this->valeur;
    }

    /**
     * Set valeur
     *
     * @param string $valeur
     *
     * @return Rabais_offre
     */
    public function setValeur($valeur)
    {
        $this->valeur = $valeur;

        return $this;
    }
}
