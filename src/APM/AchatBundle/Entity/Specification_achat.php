<?php

namespace APM\AchatBundle\Entity;

use APM\AchatBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Offre;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Specification_achat
 *
 * @ORM\Table(name="Specification_achat")
 * @ORM\Entity(repositoryClass="APM\AchatBundle\Repository\Specification_achatRepository")
 * @UniqueEntity("code")
 */
class Specification_achat extends TradeFactory
{
    /**
     *
     * @var string
     * @ORM\Column(name="code", type="string", length=255, nullable=false )
     */
    private $code;
    /**
     * @var boolean
     * @Assert\Choice({0,1})
     * @ORM\Column(name="avecDemandeRabais", type="boolean", nullable=false)
     */
    private $demandeRabais;

    /**
     * @var boolean
     * @Assert\Choice({0,1})
     * @ORM\Column(name="avecLivraison", type="boolean", nullable=false)
     */
    private $livraison;

    /**
     * @var string
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="avis", type="string", length=255, nullable=true)
     */
    private $avis;

    /**
     * @var \DateTime
     * @Assert\DateTime()
     * @ORM\Column(name="dateLivraisonSouhaite", type="datetime", nullable=true)
     */
    private $dateLivraisonSouhaite;

    /**
     * @var boolean
     * @Assert\Choice({0,1})
     * @ORM\Column(name="echantillon", type="boolean", nullable=false)
     */
    private $echantillon;

    /**
     * Id
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @var Offre
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Offre", inversedBy="specifications")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="offre_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $offre;


    /**
     *
     * @var Utilisateur_avm
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="specifications")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $utilisateur;


    /**
     * Constructor
     * @param string $var
     */
    public function __construct($var)
    {
        $this->demandeRabais = false;
        $this->livraison = false;
        $this->echantillon = false;
        $this->code = "OA" . $var;

    }

    /**
     * Get avecDemandeRabais
     *
     * @return boolean
     */
    public function isDemandeRabais()
    {
        return $this->demandeRabais;
    }

    /**
     * Get demandeRabais
     *
     * @return boolean
     */
    public function getDemandeRabais()
    {
        return $this->demandeRabais;
    }

    /**
     * Set avecDemandeRabais
     *
     * @param boolean $avecDemandeRabais
     *
     * @return Specification_achat
     */
    public function setDemandeRabais($avecDemandeRabais)
    {
        $this->demandeRabais = $avecDemandeRabais;

        return $this;
    }

    /**
     * Get livraison
     *
     * @return boolean
     */
    public function isLivraison()
    {
        return $this->livraison;
    }

    /**
     * Get livraison
     *
     * @return boolean
     */
    public function getLivraison()
    {
        return $this->livraison;
    }

    /**
     * Set aveclivraison
     *
     * @param boolean $aveclivraison
     *
     * @return Specification_achat
     */
    public function setLivraison($aveclivraison)
    {
        $this->livraison = $aveclivraison;

        return $this;
    }

    /**
     * Get avis
     *
     * @return string
     */
    public function getAvis()
    {
        return $this->avis;
    }

    /**
     * Set avis
     *
     * @param string $avis
     *
     * @return Specification_achat
     */
    public function setAvis($avis)
    {
        $this->avis = $avis;

        return $this;
    }

    /**
     * Get datelivraisonsouhaite
     *
     * @return \DateTime
     */
    public function getDateLivraisonSouhaite()
    {
        return $this->dateLivraisonSouhaite;
    }

    /**
     * Set datelivraisonSouhaite
     *
     * @param \DateTime $datelivraisonsouhaite
     *
     * @return Specification_achat
     */
    public function setDateLivraisonSouhaite($datelivraisonsouhaite)
    {
        $this->dateLivraisonSouhaite = $datelivraisonsouhaite;

        return $this;
    }

    /**
     * Get echantillon
     *
     * @return boolean
     */
    public function isEchantillon()
    {
        return $this->echantillon;
    }

    /**
     * Get echantillon
     *
     * @return boolean
     */
    public function getEchantillon()
    {
        return $this->echantillon;
    }

    /**
     * Set echantillon
     *
     * @param boolean $echantillon
     *
     * @return Specification_achat
     */
    public function setEchantillon($echantillon)
    {
        $this->echantillon = $echantillon;

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
     * @return Specification_achat
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
     * @return Specification_achat
     */
    public function setOffre(Offre $offre = null)
    {
        $this->offre = $offre;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return Utilisateur_avm
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * Set utilisateur
     *
     * @param Utilisateur_avm $utilisateur
     *
     * @return Specification_achat
     */
    public function setUtilisateur(Utilisateur_avm $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
}
