<?php

namespace APM\VenteBundle\Entity;

use APM\MarketingDistribueBundle\Entity\Conseiller_boutique;
use APM\MarketingDistribueBundle\Entity\Quota;
use APM\TransportBundle\Entity\Livraison;
use APM\TransportBundle\Entity\Livreur_boutique;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Factory\TradeFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Boutique
 * @ORM\Table(name="boutique")
 * @ORM\Entity(repositoryClass="APM\VenteBundle\Repository\BoutiqueRepository")
 * @UniqueEntity("code")
 */
class Boutique extends TradeFactory
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private $code;

    /**
     * @var string
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     * @Assert\Country
     * @ORM\Column(name="nationalite", type="string", length=255, nullable=true)
     */
    private $nationalite;

    /**
     * @var boolean
     * @Assert\Choice({0,1})
     * @ORM\Column(name="publiable", type="boolean", nullable=true)
     */
    private $publiable;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(min=2, max=105)
     * @ORM\Column(name="designation", type="string", length=255, nullable=true)
     */
    private $designation;

    /**
     * @var string
     * @Assert\Length(min=2, max=105)
     * @ORM\Column(name="raisonSociale", type="string", length=255, nullable=true)
     */
    private $raisonSociale;

    /**
     * @var string
     * @Assert\Choice({0,1,2,3,4,5})
     * @ORM\Column(name="statutSocial", type="string", length=255, nullable=true)
     */
    private $statutSocial;

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
     * @var Utilisateur_avm
     *
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="boutiques")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="gerant_id", referencedColumnName="id")
     * })
     */
    private $gerant;

    /**
     * @var Utilisateur_avm
     *
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="boutiquesProprietaire")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="proprietaire_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $proprietaire;

    /**
     * @var Offre
     *
     * @ORM\OneToMany(targetEntity="APM\VenteBundle\Entity\Offre", mappedBy="boutique")
     *
     */
    private $offres;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="APM\TransportBundle\Entity\Livreur_boutique", mappedBy="boutiques")
     *
     */
    private $livreurs;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\MarketingDistribueBundle\Entity\Conseiller_boutique", mappedBy="boutique", cascade={"persist","remove"})
     */
    private $boutiqueConseillers;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\MarketingDistribueBundle\Entity\Quota", mappedBy="boutiqueProprietaire", cascade={"persist", "remove"})
     */
    private $commissionnements;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\VenteBundle\Entity\Categorie", mappedBy="boutique", cascade={"remove"})
     */
    private $categories;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\VenteBundle\Entity\Transaction", mappedBy="boutique")
     */
    private $transactions;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\TransportBundle\Entity\Livraison", mappedBy="boutique")
     */
    private $livraisons;

    /**
     * Constructor
     * @param string $var
     */
    public function __construct($var)
    {
        $this->offres = new ArrayCollection();
        $this->livreurs = new ArrayCollection();
        $this->commissionnements = new ArrayCollection();
        $this->boutiqueConseillers = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->livraisons = new ArrayCollection();
        $this->code = "BQ" . $var;
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
     * @return Boutique
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get nationalite
     *
     * @return string
     */
    public function getNationalite()
    {
        return $this->nationalite;
    }

    /**
     * Set nationalite
     *
     * @param string $nationalite
     *
     * @return Boutique
     */
    public function setNationalite($nationalite)
    {
        $this->nationalite = $nationalite;

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
     * @return Boutique
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * Get raisonSociale
     *
     * @return string
     */
    public function getRaisonSociale()
    {
        return $this->raisonSociale;
    }

    /**
     * Set raisonSociale
     *
     * @param string $raisonSociale
     *
     * @return Boutique
     */
    public function setRaisonSociale($raisonSociale)
    {
        $this->raisonSociale = $raisonSociale;

        return $this;
    }

    /**
     * Get statutSocial
     *
     * @return string
     */
    public function getStatutSocial()
    {
        return $this->statutSocial;
    }

    /**
     * Set statutSocial
     *
     * @param string $statutSocial
     *
     * @return Boutique
     */
    public function setStatutSocial($statutSocial)
    {
        $this->statutSocial = $statutSocial;

        return $this;
    }

    /**
     * Get Id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get gerant
     *
     * @return Utilisateur_avm
     */
    public function getGerant()
    {
        return $this->gerant;
    }

    /**
     * Set gerant
     *
     * @param Utilisateur_avm $gerant
     *
     * @return Boutique
     */
    public function setGerant(Utilisateur_avm $gerant = null)
    {
        $this->gerant = $gerant;

        return $this;
    }

    /**
     * Get proprietaire
     *
     * @return Utilisateur_avm
     */
    public function getProprietaire()
    {
        return $this->proprietaire;
    }

    /**
     * Set proprietaire
     *
     * @param Utilisateur_avm $proprietaire
     *
     * @return Boutique
     */
    public function setProprietaire(Utilisateur_avm $proprietaire)
    {
        $this->proprietaire = $proprietaire;

        return $this;
    }

    /**
     * Add offre
     *
     * @param Offre $offre
     *
     * @return Boutique
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
     * Add Livreur_boutique
     *
     * @param Livreur_boutique $livreur
     *
     * @return Boutique
     */
    public function addLivreur(Livreur_boutique $livreur)
    {
        $this->livreurs[] = $livreur;

        return $this;
    }

    /**
     * Remove livreur
     *
     * @param Livreur_boutique $livreur
     */
    public function removeLivreur(Livreur_boutique $livreur)
    {
        $this->livreurs->removeElement($livreur);
    }

    /**
     * Get livreurs
     *
     * @return Collection
     */
    public function getLivreurs()
    {
        return $this->livreurs;
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
     * @return Boutique
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Add boutiqueConseiller
     *
     * @param Conseiller_boutique $boutiqueConseiller
     *
     * @return Boutique
     */
    public function addBoutiqueConseiller(Conseiller_boutique $boutiqueConseiller)
    {
        $this->boutiqueConseillers[] = $boutiqueConseiller;

        return $this;
    }

    /**
     * Remove boutiqueConseiller
     *
     * @param Conseiller_boutique $boutiqueConseiller
     */
    public function removeBoutiqueConseiller(Conseiller_boutique $boutiqueConseiller)
    {
        $this->boutiqueConseillers->removeElement($boutiqueConseiller);
    }

    /**
     * Get conseillerBoutiques
     *
     * @return Collection
     */
    public function getBoutiqueConseillers()
    {
        return $this->boutiqueConseillers;
    }

    /**
     * Add commissionnement
     *
     * @param Quota $commissionnement
     *
     * @return Boutique
     */
    public function addCommissionnement(Quota $commissionnement)
    {
        $this->commissionnements[] = $commissionnement;

        return $this;
    }

    /**
     * Remove commissionnement
     *
     * @param Quota $commissionnement
     */
    public function removeCommissionnement(Quota $commissionnement)
    {
        $this->commissionnements->removeElement($commissionnement);
    }

    /**
     * Get commissionnements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCommissionnements()
    {
        return $this->commissionnements;
    }

    /**
     * Get publiable
     *
     * @return boolean
     */
    public function getPubliable()
    {
        return $this->publiable;
    }

    /**
     * Set publiable
     *
     * @param boolean $publiable
     *
     * @return Boutique
     */
    public function setPubliable($publiable)
    {
        $this->publiable = $publiable;

        return $this;
    }

    /**
     * Add category
     *
     * @param Categorie $category
     *
     * @return Boutique
     */
    public function addCategory(Categorie $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param Categorie $category
     */
    public function removeCategory(Categorie $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add transaction
     *
     * @param Transaction $transaction
     *
     * @return Boutique
     */
    public function addTransaction(Transaction $transaction)
    {
        $this->transactions[] = $transaction;

        return $this;
    }

    /**
     * Remove transaction
     *
     * @param Transaction $transaction
     */
    public function removeTransaction(Transaction $transaction)
    {
        $this->transactions->removeElement($transaction);
    }

    /**
     * Get transactions
     *
     * @return Collection
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * Add livraison
     *
     * @param Livraison $livraison
     *
     * @return Boutique
     */
    public function addLivraison(Livraison $livraison)
    {
        $this->livraisons[] = $livraison;

        return $this;
    }

    /**
     * Remove livraison
     *
     * @param Livraison $livraison
     */
    public function removeLivraison(Livraison $livraison)
    {
        $this->livraisons->removeElement($livraison);
    }

    /**
     * Get livraisons
     *
     * @return Collection
     */
    public function getLivraisons()
    {
        return $this->livraisons;
    }
}
