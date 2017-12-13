<?php
namespace APM\VenteBundle\Entity;

use APM\VenteBundle\Factory\TradeFactory;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;

/**
 * Remise
 *
 * @ORM\Table(name="remise")
 * @ORM\Entity(repositoryClass="APM\VenteBundle\Repository\RemiseRepository")
 * @UniqueEntity("code")
 * @ExclusionPolicy("all")
 */
class Remise extends TradeFactory
{
    /**
     * @var string
     * @Expose
     * @Groups({"owner_list", "owner_remise_details", "others_remise_details"})
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @var integer
     * @Expose
     * @Groups({"owner_remise_details", "others_remise_details"})
     * @ORM\Column(name="etat", type="integer", nullable=false)
     */
    private $etat;

    /**
     * @var string
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_remise_details", "others_remise_details"})
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     * @Expose
     * @Groups({"owner_remise_details", "others_remise_details"})
     * @Assert\DateTime
     * @ORM\Column(name="dateCreation", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var \DateTime|null
     * @Expose
     * @Groups({"owner_remise_details", "others_remise_details"})
     * @Assert\DateTime
     * @ORM\Column(name="dateExpiration", type="datetime", nullable=true)
     */
    private $dateExpiration;

    /**
     * @var boolean
     * @Expose
     * @Groups({"owner_remise_details"})
     * @Assert\Choice({0,1})
     * @ORM\Column(name="Restricted", type="boolean", nullable=true)
     */
    private $restreint;

    /**
     * @var integer
     * @Expose
     * @Groups({"owner_remise_details", "others_remise_details"})
     * @Assert\Range(min=0, max=30)
     * @ORM\Column(name="nombreUtilisation", type="smallint", nullable=true)
     */
    private $nombreUtilisation;

    /**
     * @var boolean
     * @Expose
     * @Groups({"owner_remise_details", "others_remise_details"})
     * @Assert\Choice({0,1})
     * @ORM\Column(name="permanence", type="boolean", nullable=true)
     */
    private $permanence;

    /**
     * @var integer
     * @Expose
     * @Groups({"owner_remise_details", "others_remise_details"})
     * @Assert\GreaterThan(0)
     * @ORM\Column(name="quantiteMin", type="integer", nullable=true)
     */
    private $quantiteMin;

    /**
     * @var string
     * @Groups({"owner_remise_details", "others_remise_details"})
     * @ORM\Column(name="valeur", type="decimal", nullable=true)
     */
    private $valeur;

    /**
     * @var integer
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_remise_details", "others_remise_details"})
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Offre
     * @Expose
     * @Groups({"owner_remise_details", "others_remise_details"})
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Offre", inversedBy="remises")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="offre_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $offre;

    /**
     * Remise constructor.
     * @param string $var
     */
    function __construct($var)
    {
        $this->code = "RS" . $var;
        $this->date = new \DateTime('now');
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
     * @return Remise
     */
    public function setCode($code)
    {
        $this->code = $code;

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
     * @return Remise
     */
    public function setDateExpiration($dateExpiration)
    {
        $this->dateExpiration = $dateExpiration;

        return $this;
    }

    /**
     * Get estRestreint
     *
     * @return boolean
     */
    public function isRestreint()
    {
        return $this->restreint;
    }

    /**
     * Get estRestreint
     *
     * @return boolean
     */
    public function getRestreint()
    {
        return $this->restreint;
    }

    /**
     * Set estRestreint
     *
     * @param boolean $estRestreint
     *
     * @return Remise
     */
    public function setRestreint($estRestreint)
    {
        $this->restreint = $estRestreint;

        return $this;
    }

    /**
     * Get quantiteMin
     *
     * @return integer
     */
    public function getQuantiteMin()
    {
        return $this->quantiteMin;
    }

    /**
     * Set quantitemin
     *
     * @param integer $quantitemin
     *
     * @return Remise
     */
    public function setQuantiteMin($quantitemin)
    {
        $this->quantiteMin = $quantitemin;

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
     * @return Remise
     */
    public function setValeur($valeur)
    {
        $this->valeur = $valeur;

        return $this;
    }

    /**
     * Get remiseid
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
     * @return Remise
     */
    public function setOffre(Offre $offre)
    {
        $this->offre = $offre;

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
     * @return Remise
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
     * @return Remise
     */
    public function setPermanence($permanence)
    {
        $this->permanence = $permanence;

        return $this;
    }

    public function __toString()
    {
        return $this->code;
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
     * @return Remise
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

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
     * @return Remise
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
     * @return Remise
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}
