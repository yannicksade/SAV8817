<?php

namespace APM\MarketingDistribueBundle\Entity;

use APM\MarketingDistribueBundle\Factory\TradeFactory;
use APM\MarketingReseauBundle\Entity\Reseau_conseillers;
use APM\UserBundle\Entity\Utilisateur_avm;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Conseiller
 *
 * @ORM\Table(name="conseiller")
 * @ORM\Entity(repositoryClass="APM\MarketingDistribueBundle\Repository\ConseillerRepository")
 * @UniqueEntity("code", message="Ce code est déjà pris.")
 * @UniqueEntity("matricule", message="ce matricule existe déjà.")
 */
class Conseiller extends TradeFactory
{
    public static $MAX_INSTANCE_RESEAU = 2;
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private $code;
    /**
     * @var \DateTime
     * @Assert\DateTime
     * @ORM\Column(name="dateEnregistrement", type="datetime", nullable=true)
     */
    private $dateEnregistrement;
    /**
     * @var string
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var boolean
     * @Assert\Choice({0,1})
     * @ORM\Column(name="estConseillerA2", type="boolean", nullable=true)
     */
    private $conseillerA2;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(min=2, max=100)
     * @ORM\Column(name="matricule", type="string", length=255, nullable=true)
     */
    private $matricule;

    /**
     * @var integer
     * @Assert\Range(min=0)
     * @ORM\Column(name="valeurquota", type="integer", nullable=true)
     */
    private $valeurQuota;

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
     * @ORM\OneToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="profileConseiller")
     * @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id", nullable=false)
     *
     */
    private $utilisateur;


    /**
     * @var Reseau_conseillers
     *
     * @ORM\ManyToOne(targetEntity="APM\MarketingReseauBundle\Entity\Reseau_conseillers", inversedBy="advisors")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="reseau_id", referencedColumnName="id")
     * })
     */
    private $reseau;

    /**
     * @var Reseau_conseillers
     * @ORM\OneToMany(targetEntity="APM\MarketingReseauBundle\Entity\Reseau_conseillers", mappedBy="conseillerProprietaire")
     */
    private $reseauxProprietaire;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\MarketingDistribueBundle\Entity\Conseiller_boutique", mappedBy="conseiller", cascade={"persist","remove"})
     */
    private $conseillerBoutiques;

    /**
     * Constructor
     * @param string $var
     */
    public function __construct($var)
    {
        $this->conseillerBoutiques = new ArrayCollection();
        $this->reseauxProprietaire = new ArrayCollection();
        $this->code = "AD" . $var;
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
     * @return Conseiller
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get dateEnregistrement
     *
     * @return \DateTime
     */
    public function getDateEnregistrement()
    {
        return $this->dateEnregistrement;
    }

    /**
     * Set dateEnregistrement
     *
     * @param \DateTime $dateEnregistrement
     *
     * @return Conseiller
     */
    public function setDateEnregistrement($dateEnregistrement)
    {
        $this->dateEnregistrement = $dateEnregistrement;

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
     * @return Conseiller
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get estConseillerA2
     *
     * @return boolean
     */
    public function isConseillerA2()
    {
        return $this->conseillerA2;
    }

    /**
     * Get conseillerA2
     *
     * @return boolean
     */
    public function getConseillerA2()
    {
        return $this->conseillerA2;
    }

    /**
     * Set estConseillerA2
     *
     * @param boolean $estConseillerA2
     *
     * @return Conseiller
     */
    public function setConseillerA2($estConseillerA2)
    {
        $this->conseillerA2 = $estConseillerA2;

        return $this;
    }

    /**
     * Get matricule
     *
     * @return string
     */
    public function getMatricule()
    {
        return $this->matricule;
    }

    /**
     * Set matricule
     *
     * @param string $matricule
     *
     * @return Conseiller
     */
    public function setMatricule($matricule)
    {
        $this->matricule = $matricule;

        return $this;
    }

    /**
     * Get valeurQuota
     *
     * @return integer
     */
    public function getValeurQuota()
    {
        return $this->valeurQuota;
    }

    /**
     * Set valeurQuota
     *
     * @param integer $valeurQuota
     *
     * @return Conseiller
     */
    public function setValeurQuota($valeurQuota)
    {
        $this->valeurQuota = $valeurQuota;

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
     * @return Conseiller
     */
    public function setUtilisateur(Utilisateur_avm $utilisateur)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get reseau
     *
     * @return Reseau_conseillers
     */
    public function getReseau()
    {
        return $this->reseau;
    }

    /**
     * Set reseau
     *
     * @param Reseau_conseillers $reseau
     *
     * @return Conseiller
     */
    public function setReseau(Reseau_conseillers $reseau)
    {
        $this->reseau = $reseau;

        return $this;
    }


    /**
     * Add conseillerBoutique
     *
     * @param Conseiller_boutique $conseillerBoutique
     *
     * @return Conseiller
     */
    public function addConseillerBoutique(Conseiller_boutique $conseillerBoutique)
    {
        $this->conseillerBoutiques[] = $conseillerBoutique;

        return $this;
    }

    /**
     * Remove conseillerBoutique
     *
     * @param Conseiller_boutique $conseillerBoutique
     */
    public function removeConseillerBoutique(Conseiller_boutique $conseillerBoutique)
    {
        $this->conseillerBoutiques->removeElement($conseillerBoutique);
    }

    /**
     * Get conseillerBoutiques
     *
     * @return Collection
     */
    public function getConseillerBoutiques()
    {
        return $this->conseillerBoutiques;
    }


    /**
     * Add reseauxProprietaire
     *
     * @param Reseau_conseillers $reseauxProprietaire
     *
     * @return Conseiller
     */
    public function addReseauxProprietaire(Reseau_conseillers $reseauxProprietaire)
    {
        $this->reseauxProprietaire[] = $reseauxProprietaire;

        return $this;
    }

    /**
     * Remove reseauxProprietaire
     *
     * @param Reseau_conseillers $reseauxProprietaire
     */
    public function removeReseauxProprietaire(Reseau_conseillers $reseauxProprietaire)
    {
        $this->reseauxProprietaire->removeElement($reseauxProprietaire);
    }

    /**
     * Get reseauxProprietaire
     *
     * @return Collection
     */
    public function getReseauxProprietaire()
    {
        return $this->reseauxProprietaire;
    }
}
