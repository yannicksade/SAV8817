<?php

namespace APM\MarketingDistribueBundle\Entity;

use APM\MarketingDistribueBundle\Factory\TradeFactory;

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
     * @var \DateTime
     * @Assert\DateTime
     * @ORM\Column(name="dateCreationReseau", type="datetime", nullable=true)
     */
    private $dateCreationReseau;
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
    private $isConseillerA2;

    /**
     * @var integer
     * @ORM\Column(name="nombreInstance", type="integer", nullable=true)
     */
    private $nombreInstanceReseau;

    /**
     * @var string
     * @Assert\Length(min=2, max=100)
     * @ORM\Column(name="matricule", type="string", length=255, nullable=true)
     */
    private $matricule; // à utiliser dans le réseau de conseiller pour distinguer les membres plus facilement

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
     * @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id" , nullable=true)
     */
    private $utilisateur;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\MarketingDistribueBundle\Entity\Conseiller_boutique", mappedBy="conseiller", cascade={"persist","remove"})
     */
    private $conseillerBoutiques;

    /**
     * @var Conseiller
     *
     * @ORM\ManyToOne(targetEntity="APM\MarketingDistribueBundle\Entity\Conseiller")
     */
    private $masterConseiller;


    /**
     * @var Conseiller
     *
     * @ORM\OneToOne(targetEntity="APM\MarketingDistribueBundle\Entity\Conseiller")
     */
    private $conseillerDroite;

    /**
     * @var Conseiller
     *
     * @ORM\OneToOne(targetEntity="APM\MarketingDistribueBundle\Entity\Conseiller")
     */
    private $conseillerGauche;


    /**
     * Constructor
     * @param string $var
     */
    public function __construct($var)
    {
        $this->conseillerBoutiques = new ArrayCollection();
        $this->code = "AD" . $var;
        $this->nombreInstanceReseau = 0;
        $this->dateEnregistrement = new \DateTime();
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
        return $this->isConseillerA2;
    }

    /**
     * Get isConseillerA2
     *
     * @return boolean
     */
    public function getIsConseillerA2()
    {
        return $this->isConseillerA2;
    }

    /**
     * Set isConseillerA2
     *
     * @param boolean $isConseillerA2
     *
     * @return Conseiller
     */
    public function setIsConseillerA2($isConseillerA2)
    {
        $this->isConseillerA2 = $isConseillerA2;

        return $this;
    }

    /**
     * Get conseillerA2
     *
     * @return boolean
     */
    public function getConseillerA2()
    {
        return $this->isConseillerA2;
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
        $this->isConseillerA2 = $estConseillerA2;

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
     * @return int
     */
    public function getNombreInstanceReseau()
    {
        return $this->nombreInstanceReseau;
    }

    /**
     * @param int $nombreInstanceReseau
     */
    public function setNombreInstanceReseau(int $nombreInstanceReseau)
    {
        $this->nombreInstanceReseau = $nombreInstanceReseau;
    }

    /**
     * Get masterConseiller
     *
     * @return Conseiller
     */
    public function getMasterConseiller()
    {
        return $this->masterConseiller;
    }

    /**
     * Set masterConseiller
     *
     * @param Conseiller $masterConseiller
     *
     * @return Conseiller
     */
    public function setMasterConseiller(Conseiller $masterConseiller = null)
    {
        $this->masterConseiller = $masterConseiller;

        return $this;
    }

    /**
     * Get conseillerDroite
     *
     * @return Conseiller
     */
    public function getConseillerDroite()
    {
        return $this->conseillerDroite;
    }

    /**
     * Set conseillerDroite
     *
     * @param Conseiller $conseillerDroite
     *
     * @return Conseiller
     */
    public function setConseillerDroite(Conseiller $conseillerDroite = null)
    {
        $this->conseillerDroite = $conseillerDroite;

        return $this;
    }

    /**
     * Get conseillerGauche
     *
     * @return Conseiller
     */
    public function getConseillerGauche()
    {
        return $this->conseillerGauche;
    }

    /**
     * Set conseillerGauche
     *
     * @param Conseiller $conseillerGauche
     *
     * @return Conseiller
     */
    public function setConseillerGauche(Conseiller $conseillerGauche = null)
    {
        $this->conseillerGauche = $conseillerGauche;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreationReseau()
    {
        return $this->dateCreationReseau;
    }

    /**
     * @param \DateTime $dateCreationReseau
     */
    public function setDateCreationReseau(\DateTime $dateCreationReseau = null)
    {
        $this->dateCreationReseau = $dateCreationReseau;
    }

    public function __toString()
    {
        return $this->matricule ? $this->matricule : $this->code;
    }

    /* public function __clone()
     {
         $this->conseillerGauche = clone $this->conseillerGauche;
         $this->conseillerDroite = clone $this->conseillerDroite;
     }*/
}
