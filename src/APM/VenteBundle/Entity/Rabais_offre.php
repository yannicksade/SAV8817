<?php

namespace APM\VenteBundle\Entity;

use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Factory\TradeFactory;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Rabais_offre
 *
 * @ORM\Table(name="rabais_offre")
 * @ORM\Entity(repositoryClass="APM\VenteBundle\Repository\Rabais_offreRepository")
 * @UniqueEntity("code")
 */
class Rabais_offre extends TradeFactory
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255 , nullable=false)
     */
    private $code;

    /**
     * @var \DateTime|null
     * @Assert\DateTime
     * @ORM\Column(name="dateLimite", type="datetime", nullable=true)
     */
    private $dateLimite;

    /**
     * @var integer
     * @Assert\Range(min=1,max=10) avec <null> le nombre d'occurrence est indefini
     * @ORM\Column(name="nombreDefois", type="smallint", nullable=true)
     */
    private $nombreDefois;

    /**
     * @var string
     *
     * @ORM\Column(name="prixUpdate", type="decimal",nullable=true)
     */
    private $prixUpdate;

    /**
     * @var integer
     * @Assert\GreaterThan(0)
     * @ORM\Column(name="quantiteMin", type="integer", nullable=true)
     */
    private $quantiteMin;

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
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="rabais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vendeur_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $vendeur;

    /**
     * @var Utilisateur_avm
     *
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="beneficiaire_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $beneficiaireRabais;

    /**
     * @var Offre
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Offre", inversedBy="rabais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="offre_id", referencedColumnName="id")
     * })
     */
    private $offre;

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

    /**
     * Get offre
     *
     * @return Offre
     */
    public function getOffre()
    {
        return $this->offre;
    }
}
