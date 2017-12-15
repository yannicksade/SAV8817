<?php

namespace APM\AchatBundle\Entity;

use APM\AchatBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Offre;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Exclude;
/**
 * Specification_achat
 *
 * @ORM\Table(name="Specification_achat")
 * @ORM\Entity(repositoryClass="APM\AchatBundle\Repository\Specification_achatRepository")
 * @UniqueEntity("code")
 * @ExclusionPolicy("all")
 */
class Specification_achat extends TradeFactory
{
    /**
     *
     * @var string
     * @Expose
     * @Groups({"owner_list", "others_spA_details", "owner_spA_details"})
     * @ORM\Column(name="code", type="string", length=255, nullable=false )
     */
    private $code;
    /**
     * @var boolean
     * @Expose
     * @Groups({"others_spA_details", "owner_spA_details"})
     * @Assert\Choice({0,1})
     * @ORM\Column(name="avecDemandeRabais", type="boolean", nullable=true)
     */
    private $demandeRabais;

    /**
     * @var boolean
     * @Expose
     * @Groups({"others_spA_details", "owner_spA_details"})
     * @Assert\Choice({0,1})
     * @ORM\Column(name="avecLivraison", type="boolean", nullable=true)
     */
    private $livraison;

    /**
     * @var string
     * @Expose
     * @Groups({"others_spA_details", "owner_spA_details"})
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="avis", type="string", length=255, nullable=true)
     */
    private $avis;

    /**
     * @var \DateTime
     * @Expose
     * @Groups({"others_spA_details", "owner_spA_details"})
     * @Assert\DateTime()
     * @ORM\Column(name="dateLivraisonSouhaite", type="datetime", nullable=true)
     */
    private $dateLivraisonSouhaite;

    /**
     * @var \DateTime
     * @Expose
     * @Groups({"others_spA_details", "owner_spA_details"})
     * @Assert\DateTime()
     * @ORM\Column(name="$dateCreation", type="datetime", nullable=true)
     */
    private $dateCreation;

    /**
     * @var boolean
     * @Expose
     * @Groups({"others_spA_details", "owner_spA_details"})
     * @Assert\Choice({0,1})
     * @ORM\Column(name="echantillon", type="boolean", nullable=false)
     */
    private $echantillon;

    /**
     * Id
     * @var integer
     * @Expose
     * @Groups({"owner_list", "others_list", "others_spA_details", "owner_spA_details"})
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Offre
     * @Expose
     * @Groups({"others_spA_details", "owner_spA_details"})
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Offre", inversedBy="specifications")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="offre_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $offre;


    /**
     *
     * @var Utilisateur_avm
     * @Expose
     * @Groups({"others_spA_details", "owner_spA_details"})
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
        $this->dateCreation = new \DateTime('now');

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

    public function __toString()
    {
        return $this->code;
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
     * @return Specification_achat
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }
}
