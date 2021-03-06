<?php

namespace APM\VenteBundle\Entity;

use APM\MarketingDistribueBundle\Entity\Conseiller_boutique;
use APM\MarketingDistribueBundle\Entity\Quota;
use APM\TransportBundle\Entity\Livraison;
use APM\TransportBundle\Entity\Livreur_boutique;
use APM\UserBundle\Entity\Groupe_relationnel;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Factory\TradeFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Exclude;

/**
 * Boutique
 * @ORM\Table(name="boutique")
 * @ORM\Entity(repositoryClass="APM\VenteBundle\Repository\BoutiqueRepository")
 * @Vich\Uploadable
 * @UniqueEntity("code")
 * @ExclusionPolicy("all")
 */
class Boutique extends TradeFactory
{
    /**
     * @var string
     * @Type("string")
     * @Expose
     * @Groups({"owner_list", "owner_boutique_details", "others_boutique_details"})
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private $code;

    /**
     * @var integer
     * @Assert\NotNull
     * @Type("int")
     * @Expose
     * @Groups({"owner_boutique_details", "others_boutique_details"})
     * @ORM\Column(name="etat", type="integer", nullable=false)
     */
    private $etat;

    /**
     * @var \DateTime
     * @Type("DateTime<'d-m-Y H:i'>")
     * @Expose
     * @Groups({"owner_boutique_details"})
     * @ORM\Column(name="dateCreation", type="datetime", nullable=false)
     */
    private $dateCreation;

    /**
     * @Type("DateTime<'d-m-Y H:i'>")
     * @Expose
     * @Groups({"owner_boutique_details"})
     * @ORM\Column(name="updatedAt", type="datetime", nullable= true)
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var string
     * @Type("string")
     * @Expose
     * @Groups({"owner_boutique_details", "others_boutique_details", "owner_list", "others_list"})
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     * @Type("string")
     * @Expose
     * @Groups({"owner_boutique_details", "others_boutique_details"})
     * @Assert\Country
     * @ORM\Column(name="nationalite", type="string", length=255, nullable=true)
     */
    private $nationalite;

    /**
     * @Expose
     * @Groups({"owner_boutique_details", "others_boutique_details"})
     * @var string
     * @Type("string")
     * @ORM\Column(name="brochure", type="string", length=255, nullable=true)
     */
    private $brochure;

    /**
     * @Exclude
     * @Assert\File(
     *     maxSize = "10024k",
     *     mimeTypes = {"application/x-pdf", "application/pdf", "text/plain"},
     *     mimeTypesMessage = "Please upload a valid PDF"
     * )
     * @Vich\UploadableField(mapping="boutique_files", fileNameProperty="brochure")
     * @var UploadedFile
     */
    private $brochurefile;

    /**
     * @var boolean
     * @Type("bool")
     * @Expose
     * @Groups({"owner_boutique_details"})
     * @Assert\Choice({0,1})
     * @ORM\Column(name="publiable", type="boolean", nullable=true)
     */
    private $publiable;

    /**
     * @var string
     * @Type("string")
     * @Expose
     * @Groups({"owner_boutique_details", "others_boutique_details", "owner_list", "others_list"})
     * @Assert\NotBlank
     * @Assert\Length(min=2, max=105)
     * @ORM\Column(name="designation", type="string", length=255, nullable=true)
     */
    private $designation;

    /**
     * @var string
     * @Type("string")
     * @Expose
     * @Groups({"owner_boutique_details", "others_boutique_details"})
     * @Assert\Length(min=2, max=105)
     * @ORM\Column(name="raisonSociale", type="string", length=255, nullable=true)
     */
    private $raisonSociale;

    /**
     * @var string
     * @Type("string")
     * @Expose
     * @Groups({"owner_boutique_details", "others_boutique_details"})
     * @ORM\Column(name="statutSocial", type="string", length=255, nullable=true)
     */
    private $statutSocial;

    /**
     * Id
     * @var integer
     * @Type("int")
     * @Expose
     * @Groups({"test", "owner_boutique_details", "others_boutique_details", "owner_list", "others_list"})
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Utilisateur_avm
     * @Type("APM\UserBundle\Entity\Utilisateur_avm")
     * @Expose
     * @Groups({"owner_boutique_details"})
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="boutiquesGerant")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="gerant_id", referencedColumnName="id")
     * })
     */
    private $gerant;

    /**
     * @var Utilisateur_avm
     * @Type("APM\UserBundle\Entity\Utilisateur_avm")
     * @Expose
     * @Groups({"owner_boutique_details"})
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="boutiquesProprietaire")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="proprietaire_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $proprietaire;

    /**
     * @Exclude
     * @Assert\File(
     *     maxSize = "5024k",
     *     mimeTypes = {"image/jpeg", "image/png", "image/gif"},
     *     mimeTypesMessage = "Please upload a valid PDF"
     * )
     * @Vich\UploadableField(mapping="boutique_images", fileNameProperty="image1")
     * @var UploadedFile
     */
    private $imagefile1;

    /**
     * @Exclude
     * @Assert\File(
     *     maxSize = "5024k",
     *     mimeTypes = {"image/jpeg", "image/png", "image/gif"},
     *     mimeTypesMessage = "Please upload a valid PDF"
     * )
     * @Vich\UploadableField(mapping="boutique_images", fileNameProperty="image2")
     * @var File
     */
    private $imagefile2;

    /**
     * @Exclude
     * @Assert\File(
     *     maxSize = "5024k",
     *     mimeTypes = {"image/jpeg", "image/png", "image/gif"},
     *     mimeTypesMessage = "Please upload a valid PDF"
     * )
     * @Vich\UploadableField(mapping="boutique_images", fileNameProperty="image3")
     * @var File
     */
    private $imagefile3;

    /**
     * @Exclude
     * @Assert\File(
     *     maxSize = "5024k",
     *     mimeTypes = {"image/jpeg", "image/png", "image/gif"},
     *     mimeTypesMessage = "Please upload a valid PDF"
     * )
     * @Vich\UploadableField(mapping="boutique_images", fileNameProperty="image4")
     * @var File
     */
    private $imagefile4;
    /**
     * @Expose
     * @Groups({"owner_boutique_details", "others_boutique_details", "owner_list", "others_list"})
     * @var string
     * @Type("string")
     * @ORM\Column(name="image1", type="string", nullable=true)
     */
    private $image1;
    /**
     * @Expose
     * @Groups({"owner_boutique_details", "others_boutique_details", "owner_list", "others_list"})
     * @var string
     * @Type("string")
     * @ORM\Column(name="image2", type="string", nullable=true)
     */
    private $image2;

    /**
     * @Expose
     * @Groups({"owner_boutique_details", "others_boutique_details", "owner_list", "others_list"})
     * @var string
     * @Type("string")
     * @ORM\Column(name="image3", type="string", nullable=true)
     */
    private $image3;


    /**
     * @Expose
     * @Groups({"owner_boutique_details", "others_boutique_details", "owner_list", "others_list"})
     * @var string
     * @Type("string")
     * @ORM\Column(name="image4", type="string", nullable=true)
     */
    private $image4;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="APM\VenteBundle\Entity\Offre", mappedBy="boutique")
     *
     */
    private $offres;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="APM\TransportBundle\Entity\Livreur_boutique", mappedBy="boutiqueProprietaire")
     *
     */
    private $livreurBoutiques;

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
     * @ORM\OneToMany(targetEntity="APM\VenteBundle\Entity\Categorie", mappedBy="boutique")
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
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\UserBundle\Entity\Groupe_relationnel", mappedBy="boutique", cascade={"remove"})
     */
    private $groupesRelationnels;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\VenteBundle\Entity\Transaction", mappedBy="boutiqueBeneficiaire")
     */
    private $transactionsRecues;


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
        $this->groupesRelationnels = new ArrayCollection();
        $this->transactionsRecues = new ArrayCollection();
        $this->livreurBoutiques = new ArrayCollection();
        $this->code = "BQ" . $var;
        $this->updatedAt = $this->dateCreation =  new \DateTime('now');
    }

    public function __toString()
    {
        return $this->designation;
    }

    public function getBrochurefile()
    {
        return $this->brochurefile;
    }

    public function setBrochurefile(File $brochure = null)
    {
        $this->brochurefile = $brochure;
        if ($brochure) {
            $this->updatedAt = new \DateTime('now');
        }
    }
    public function getImagefile1()
    {
        return $this->imagefile1;
    }

    public function setImagefile1(File $image = null)
    {
        $this->imagefile1 = $image;
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getImagefile2()
    {
        return $this->imagefile2;
    }

    public function setImagefile2(File $image = null)
    {
        $this->imagefile2 = $image;

        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getImagefile3()
    {
        return $this->imagefile3;
    }

    public function setImagefile3(File $image = null)
    {
        $this->imagefile3 = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getImagefile4()
    {
        return $this->imagefile4;
    }

    public function setImagefile4(File $image = null)
    {
        $this->imagefile4 = $image;

        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
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
        $this->updatedAt = new \DateTime('now');
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
        $this->updatedAt = new \DateTime('now');
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
        $this->updatedAt = new \DateTime('now');
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
        $this->updatedAt = new \DateTime('now');
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
        $this->updatedAt = new \DateTime('now');
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
        $this->updatedAt = new \DateTime('now');
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
        $this->updatedAt = new \DateTime('now');
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
        $this->updatedAt = new \DateTime('now');
        $this->offres[] = $offre;

        return $this;
    }

    /**
     * Remove offre
     *
     * @param Offre $offre
     */
    public function removeOffre(Offre $offre)
    {$this->updatedAt = new \DateTime('now');
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

    /**
     * Add groupesRelationnel
     *
     * @param Groupe_relationnel $groupesRelationnel
     *
     * @return Boutique
     */
    public function addGroupesRelationnel(Groupe_relationnel $groupesRelationnel)
    {
        $this->groupesRelationnels[] = $groupesRelationnel;

        return $this;
    }

    /**
     * Remove groupesRelationnel
     *
     * @param Groupe_relationnel $groupesRelationnel
     */
    public function removeGroupesRelationnel(Groupe_relationnel $groupesRelationnel)
    {
        $this->groupesRelationnels->removeElement($groupesRelationnel);
    }

    /**
     * Get groupesRelationnels
     *
     * @return Collection
     */
    public function getGroupesRelationnels()
    {
        return $this->groupesRelationnels;
    }

    /**
     * Add transactionsRecue
     *
     * @param Transaction $transactionsRecue
     *
     * @return Boutique
     */
    public function addTransactionsRecue(Transaction $transactionsRecue)
    {
        $this->transactionsRecues[] = $transactionsRecue;

        return $this;
    }

    /**
     * Remove transactionsRecue
     *
     * @param Transaction $transactionsRecue
     */
    public function removeTransactionsRecue(Transaction $transactionsRecue)
    {
        $this->transactionsRecues->removeElement($transactionsRecue);
    }

    /**
     * Get transactionsRecues
     *
     * @return Collection
     */
    public function getTransactionsRecues()
    {
        return $this->transactionsRecues;
    }

    /**
     * Add livreurBoutique
     *
     * @param Livreur_boutique $livreurBoutique
     *
     * @return Boutique
     */
    public function addLivreurBoutique(Livreur_boutique $livreurBoutique)
    {
        $this->livreurBoutiques[] = $livreurBoutique;

        return $this;
    }

    /**
     * Remove livreurBoutique
     *
     * @param Livreur_boutique $livreurBoutique
     */
    public function removeLivreurBoutique(Livreur_boutique $livreurBoutique)
    {
        $this->livreurBoutiques->removeElement($livreurBoutique);
    }

    /**
     * Get livreurBoutiques
     *
     * @return Collection
     */
    public function getLivreurBoutiques()
    {
        return $this->livreurBoutiques;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Boutique
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

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
     * @return Boutique
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

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
     * @return Boutique
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get image1
     *
     * @return string
     */
    public function getImage1()
    {
        return $this->image1;
    }

    /**
     * Set image1
     *
     * @param string $image1
     *
     * @return Boutique
     */
    public function setImage1($image1)
    {
        $this->image1 = $image1;

        return $this;
    }

    /**
     * Get image2
     *
     * @return string
     */
    public function getImage2()
    {
        return $this->image2;
    }

    /**
     * Set image2
     *
     * @param string $image2
     *
     * @return Boutique
     */
    public function setImage2($image2)
    {
        $this->image2 = $image2;

        return $this;
    }

    /**
     * Get image3
     *
     * @return string
     */
    public function getImage3()
    {
        return $this->image3;
    }

    /**
     * Set image3
     *
     * @param string $image3
     *
     * @return Boutique
     */
    public function setImage3($image3)
    {
        $this->image3 = $image3;

        return $this;
    }

    /**
     * Get image4
     *
     * @return string
     */
    public function getImage4()
    {
        return $this->image4;
    }

    /**
     * Set image4
     *
     * @param string $image4
     *
     * @return Boutique
     */
    public function setImage4($image4)
    {
        $this->image4 = $image4;

        return $this;
    }

    /**
     * Get brochure
     *
     * @return string
     */
    public function getBrochure()
    {
        return $this->brochure;
    }

    /**
     * Set brochure
     *
     * @param string $brochure
     *
     * @return Boutique
     */
    public function setBrochure($brochure)
    {
        $this->brochure = $brochure;

        return $this;
    }
}
