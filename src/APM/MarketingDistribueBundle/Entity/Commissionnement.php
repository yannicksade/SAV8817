<?php

namespace APM\MarketingDistribueBundle\Entity;

use APM\MarketingDistribueBundle\Factory\TradeFactory;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Commissionnement
 *
 * @ORM\Table(name="commissionnement")
 * @ORM\Entity(repositoryClass="APM\MarketingDistribueBundle\Repository\CommissionnementRepository")
 * @UniqueEntity("code")
 */
class Commissionnement extends TradeFactory
{
    /**
     *
     * @var string
     * @ORM\Column(name="code", type="string", length=255, nullable=false )
     */
    private $code;

    /**
     *
     * @var integer
     * @Assert\Range(min=0)
     * @ORM\Column(name="creditDepense", type="integer", nullable=true)
     */
    private $creditDepense;

    /**
     * @var \DateTime
     * @Assert\DateTime
     * @ORM\Column(name="dateCreation", type="datetime", nullable=true)
     */
    private $dateCreation;

    /**
     * @var string
     * @Assert\Length(min=2, max=155)
     * @ORM\Column(name="libelle", type="string", length=255, nullable=true)
     */
    private $libelle;


    /**
     * @var string
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @Assert\GreaterThan(0)
     * @ORM\Column(name="quantite", type="integer", nullable=true)
     */
    private $quantite;

    /**
     * Id
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Conseiller_boutique
     *
     * @ORM\ManyToOne(targetEntity="APM\MarketingDistribueBundle\Entity\Conseiller_boutique", inversedBy="commissionnements")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="conseillerBoutique_id", referencedColumnName="id", nullable=false)
     *     })
     */
    private $conseillerBoutique;

    /**
     * @var Quota
     * @ORM\ManyToOne(targetEntity="APM\MarketingDistribueBundle\Entity\Quota", inversedBy="commissionnements")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="commission_id", referencedColumnName="id", nullable=false)
     *     })
     */
    private $commission;

    /**
     * Commissionnement constructor.
     * @param string $var
     */
    public function __construct($var)
    {
        $this->dateCreation = new \DateTime();
        $this->code = "CS" . $var;

    }

    /**
     * Get creditDepense
     *
     * @return integer
     */
    public function getCreditDepense()
    {
        return $this->creditDepense;
    }

    /**
     * Set creditDepense
     *
     * @param integer $creditDepense
     *
     *
     * @return Commissionnement
     */
    public function setCreditDepense($creditDepense)
    {
        $this->creditDepense = $creditDepense;

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
     * @return Commissionnement
     */
    public function setDatecreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }


    /**
     * Id
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
     * @return Commissionnement
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return Commissionnement
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

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
     * @return Commissionnement
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get quantite
     *
     * @return integer
     */
    public function getQuantite()
    {
        return $this->quantite;
    }

    /**
     * Set quantite
     *
     * @param integer $quantite
     *
     * @return Commissionnement
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;

        return $this;
    }

    /**
     * Get conseillerBoutique
     *
     * @return Conseiller_boutique
     */
    public function getConseillerBoutique()
    {
        return $this->conseillerBoutique;
    }

    /**
     * Set conseillerBoutique
     *
     * @param Conseiller_boutique $conseillerBoutique
     *
     * @return Commissionnement
     */
    public function setConseillerBoutique(Conseiller_boutique $conseillerBoutique)
    {
        $this->conseillerBoutique = $conseillerBoutique;

        return $this;
    }

    /**
     * Get commission
     *
     * @return Quota
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * Set commission
     *
     * @param Quota $commission
     *
     * @return Commissionnement
     */
    public function setCommission(Quota $commission)
    {
        $this->commission = $commission;

        return $this;
    }

    public function __toString()
    {
        return $this->code;
    }
}
