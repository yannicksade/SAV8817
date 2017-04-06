<?php

namespace APM\VenteBundle\Entity;

use APM\VenteBundle\Factory\TradeFactory;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Remise
 *
 * @ORM\Table(name="remise")
 * @ORM\Entity(repositoryClass="APM\VenteBundle\Repository\RemiseRepository")
 * @UniqueEntity("code")
 */
class Remise extends TradeFactory
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @var \DateTime|null
     * @Assert\DateTime
     * @ORM\Column(name="dateExpiration", type="datetime", nullable=true)
     */
    private $dateExpiration;

    /**
     * @var boolean
     * @Assert\Choice({0,1})
     * @ORM\Column(name="Restricted", type="boolean", nullable=true)
     */
    private $restreint;

    /**
     * @var integer
     * @Assert\Range(min=0, max=30)
     * @ORM\Column(name="nombreUtilisation", type="smallint", nullable=true)
     */
    private $nombreUtilisation;
    /**
     * @var boolean
     * @Assert\Choice({0,1})
     * @ORM\Column(name="permanence", type="boolean", nullable=true)
     */
    private $permanence;

    /**
     * @var integer
     * @Assert\GreaterThan(0)
     * @ORM\Column(name="quantiteMin", type="integer", nullable=true)
     */
    private $quantiteMin;

    /**
     * @var string
     *
     * @ORM\Column(name="valeur", type="decimal", nullable=true)
     */
    private $valeur;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Offre
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
     * Get nombreUtilisation
     *
     * @return integer
     */
    public function getNombreUtilisationn()
    {
        return $this->nombreUtilisation;
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
}
