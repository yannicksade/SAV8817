<?php

namespace APM\VenteBundle\Entity;

use APM\AchatBundle\Entity\Service_apres_vente;
use APM\AchatBundle\Entity\Specification_achat;
use APM\AnimationBundle\Entity\Base_documentaire;
use APM\UserBundle\Entity\Commentaire;
use APM\UserBundle\Entity\Communication;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Factory\TradeFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\MaxDepth;
use JMS\Serializer\Annotation\Type;

/**
 * Offre
 *
 * @ORM\Table(name="offre")
 * @ORM\Entity(repositoryClass="APM\VenteBundle\Repository\OffreRepository")
 * @Vich\Uploadable
 * @UniqueEntity("code", message="impossible de creer l'offre, veuillez ressayer plus tard! ")
 * @ExclusionPolicy("all")
 */
class Offre extends TradeFactory
{
    /**
     * @Expose
     * @Type("string")
     * @Groups({"owner_list","owner_offre_details"})
     * @var string
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private $code;

    /**
     * @Expose
     * @Groups({"owner_offre_details", "others_offre_details"})
     * @var string
     * @Type("string")
     * @ORM\Column(name="brochure", type="string", length=255, nullable=true)
     */
    private $brochure;

    /**
     * @Exclude
     * @Assert\File(
     *     maxSize = "10024k",
     *     mimeTypes = {"application/x-pdf", "application/pdf", "text/plain", },
     *     mimeTypesMessage = "Please upload a valid file"
     * )
     * @Vich\UploadableField(mapping="offre_files", fileNameProperty="brochure")
     * @var File
     */
    private $brochurefile;

    /**
     * @Expose
     * @Type("DateTime<'d-m-Y H:i'>")
     * @Groups({"owner_offre_details"})
     * @var \DateTime
     * @Assert\DateTime
     * @ORM\Column(name="dateCreation", type="datetime", nullable=false)
     */
    private $dateCreation;

    /**
     * @Expose
     * @Type("DateTime<'d-m-Y H:i'>")
     * @Groups({"owner_offre_details", "others_offre_details"})
     * @var \DateTime
     * @Assert\DateTime
     * @ORM\Column(name="dateExpiration", type="datetime", nullable=true)
     */
    private $dateExpiration;

    /**
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_offre_details", "others_offre_details"})
     * @var string
     * @Type("string")
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @Type("string")
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_offre_details", "others_offre_details"})
     * @Assert\NotBlank
     * @Assert\Length(min=2)
     * @ORM\Column(name="designation", type="string", length=255, nullable=false)
     */
    private $designation;

    /**
     * @Expose
     * @Groups({"owner_offre_details", "others_offre_details"})
     * @var integer
     * @Type("integer")
     * @ORM\Column(name="dureeGarantie", type="integer", nullable=true)
     */
    private $dureeGarantie;

    /**
     * @Expose
     * @Groups({"owner_offre_details"})
     * @var boolean
     * @Type("bool")
     * @ORM\Column(name="estRetourne", type="boolean", nullable=true)
     */
    private $retourne;

    /**
     * @Expose
     * @Groups({"owner_offre_details", "others_offre_details"})
     * @Type("string")
     * @var string
     * @Assert\length(min=2, max=254)
     * @ORM\Column(name="unite", type="string", length=255, nullable=true)
     */
    private $unite;

    /**
     * @Expose
     * @Groups({"owner_offre_details", "others_offre_details"})
     * @var integer
     * @Assert\NotNull
     * @Type("integer")
     * @ORM\Column(name="etat", type="integer", nullable=false)
     */
    private $etat;

    /**
     * @Expose
     * @Groups({"owner_offre_details", "others_offre_details"})
     * @Type("int")
     * @var integer
     * @ORM\Column(name="apparenceNeuf", type="integer", nullable=true)
     */
    private $apparenceNeuf;

    /**
     * @Exclude
     * @Assert\File(
     *     maxSize = "5024k",
     *     mimeTypes = {"image/jpeg", "image/png", "image/gif"},
     *     mimeTypesMessage = "Please upload a valid PDF"
     * )
     * @Vich\UploadableField(mapping="offre_images", fileNameProperty="image1")
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
     * @Vich\UploadableField(mapping="offre_images", fileNameProperty="image2")
     * @var File
     */
    private $imagefile2;

    /**
     * @Exclude
     * @Assert\File(
     *     maxSize = "5024k",
     *     mimeTypes = {"image/jpeg", "image/png", "image/gif"},
     *     mimeTypesMessage = "Please upload a valid image"
     * )
     * @Vich\UploadableField(mapping="offre_images", fileNameProperty="image3")
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
     * @Vich\UploadableField(mapping="offre_images", fileNameProperty="image4")
     * @var File
     */
    private $imagefile4;

    /**
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_offre_details", "others_offre_details"})
     * @var string
     * @Type("string")
     * @ORM\Column(name="image1", type="string", nullable=true)
     */
    private $image1;

    /**
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_offre_details", "others_offre_details"})
     * @var string
     * @Type("string")
     * @ORM\Column(name="image2", type="string", nullable=true)
     */
    private $image2;


    /**
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_offre_details", "others_offre_details"})
     * @var string
     * @Type("string")
     * @ORM\Column(name="image3", type="string", nullable=true)
     */
    private $image3;


    /**
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_offre_details", "others_offre_details"})
     * @var string
     * @Type("string")
     * @ORM\Column(name="image4", type="string", nullable=true)
     */
    private $image4;


    /**
     * @Expose
     * @Type("DateTime<'d-m-Y H:i'>")
     * @Groups({"owner_offre_details"})
     * @Assert\NotNull
     * @Assert\DateTime
     * @ORM\Column(name="updatedAt", type="datetime", nullable= false)
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @Expose
     * @Groups({"owner_offre_details","others_offre_details"})
     * @var integer
     * @Assert\NotNull
     * @Type("int")
     * @ORM\Column(name="modeVente", type="integer", nullable=false)
     */
    private $modeVente;

    /**
     * @Expose
     * @Groups({"owner_offre_details", "others_offre_details"})
     * @var string
     * @Type("string")
     * @Assert\length(min=2, max=254)
     * @ORM\Column(name="modelDeSerie", type="string", length=255, nullable=true)
     */
    private $modelDeSerie;

    /**
     * @Expose
     * @Groups({"owner_offre_details", "others_offre_details"})
     * @var string
     * @Type("string")
     * @ORM\Column(name="prixUnitaire", type="decimal", nullable=true)
     */
    private $prixUnitaire;

    /**
     * @Expose
     * @Groups({"owner_offre_details"})
     * @var integer
     * @Type("int")
     * @Assert\GreaterThan(0)
     * @ORM\Column(name="quantite", type="integer", nullable=true)
     */
    private $quantite;

    /**
     * @Expose
     * @Groups({"owner_offre_details"})
     * @var integer
     * @Type("int")
     * @Assert\Range(min=0)
     * @ORM\Column(name="credit", type="integer", nullable=true)
     */
    private $credit;

    /**
     * @Expose
     * @Groups({"owner_offre_details", "others_offre_details"})
     * @var integer
     * @Type("int")
     * @ORM\Column(name="rateEvaluation", type="smallint", nullable=true)
     */
    private $evaluation;

    /**
     * @Expose
     * @Groups({"owner_offre_details", "others_offre_details"})
     * @var string
     * @Type("string")
     * @ORM\Column(name="remiseProduit", type="decimal", nullable=true)
     */
    private $remiseProduit;

    /**
     * @Expose
     * @Groups({"owner_offre_details", "others_offre_details"})
     * @var integer
     * @Type("int")
     * @Assert\NotNull
     * @ORM\Column(name="typeOffre", type="integer", nullable=false)
     */
    private $typeOffre;

    /**
     * @Expose
     * @Groups({"owner_offre_details"})
     * @var boolean
     * @ORM\Column(name="valide", type="boolean", nullable=true)
     */
    private $valide;

    /**
     * @Expose
     * @Groups({"owner_offre_details"})
     * @var boolean
     * @Type("bool")
     * @ORM\Column(name="publiable", type="boolean", nullable=true)
     */
    private $publiable;

    /**
     * @var integer
     * @Type("integer")
     * @ORM\Id
     * @Expose
     * @Groups({"test", "owner_list", "others_list", "owner_offre_details", "others_offre_details"})
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Categorie
     * @Type("APM\VenteBundle\Entity\Categorie")
     * @Expose
     * @Groups({"test", "owner_offre_details", "others_offre_details"})
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Categorie", inversedBy="offres")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categorie_id", referencedColumnName="id")
     * })
     */
    private $categorie;

    /**
     * @var Boutique
     * @Type("APM\VenteBundle\Entity\Boutique")
     * @Expose
     * @Groups({"test", "owner_offre_details", "others_offre_details"})
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Boutique", inversedBy="offres")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="boutique_id", referencedColumnName="id")
     * })
     */
    private $boutique;

    /**
     * @var Utilisateur_avm
     * @Type("APM\UserBundle\Entity\Utilisateur_avm")
     * @Expose
     * @Groups({"test", "owner_offre_details", "others_offre_details"})
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="offres")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vendeur_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $vendeur;

    /**
     * @var Base_documentaire
     * @Type("APM\AnimationBundle\Entity\Base_documentaire")
     * @Expose
     * @Groups({"test", "owner_offre_details", "others_offre_details"})
     * @ORM\ManyToOne(targetEntity="APM\AnimationBundle\Entity\Base_documentaire", inversedBy="produits")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="document_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $document;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\AchatBundle\Entity\Service_apres_vente", mappedBy="offre")
     */
    private $service_apres_ventes;
    /**
     * @ORM\OneToMany(targetEntity="APM\UserBundle\Entity\Commentaire", mappedBy="offre")
     */
    private $commentaires;

    /**
     * @var Specification_achat
     * @ORM\OneToMany(targetEntity="APM\AchatBundle\Entity\Specification_achat" , mappedBy="offre", cascade={"remove"})
     */
    private $specifications;
    /**
     * @var Collection
     * @ORM\oneToMany(targetEntity="APM\VenteBundle\Entity\Transaction_produit", mappedBy="produit")
     */
    private $produitTransactions;
    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="APM\UserBundle\Entity\Communication", mappedBy="offres")
     */
    private $communications;
    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\VenteBundle\Entity\Rabais_offre", mappedBy="offre")
     */
    private $rabais;

    /**
     * Constructor
     * @param string $var
     */
    public function __construct($var)
    {
        $this->service_apres_ventes = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->code = "OF" . $var;
        $this->specifications = new ArrayCollection();
        $this->communications = new ArrayCollection();
        $this->rabais = new ArrayCollection();
        $this->updatedAt = $this->dateCreation = new \DateTime('now');
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
        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        // if 'updatedAt' is not defined in your entity, use another property
        $this->imagefile1 = $image;
        if ($image) {
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
        if ($image) {
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
            $this->updatedAt = new \DateTime('now');
        }
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
     * @return Offre
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;
        $this->updatedAt = new \DateTime('now');
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
     * @return Offre
     */
    public function setDateExpiration($dateExpiration)
    {
        $dateExpiration !== '' ?: $dateExpiration = null;
        $this->dateExpiration = $dateExpiration;
        $this->updatedAt = new \DateTime('now');
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
     * @return Offre
     */
    public function setDescription($description)
    {
        $description !== '' ?: $description = null;
        $this->description = $description;
        $this->updatedAt = new \DateTime('now');
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
     * @return Offre
     */
    public function setDesignation($designation)
    {
        $designation !== '' ?: $designation = null;
        $this->designation = $designation;
        $this->updatedAt = new \DateTime('now');
        return $this;
    }

    /**
     * Get estRetourne
     *
     * @return boolean
     */
    public function isRetourne()
    {
        return $this->retourne;
    }

    /**
     * Get retourne
     *
     * @return boolean
     */
    public function getRetourne()
    {
        return $this->retourne;
    }

    /**
     * Set estRetourne
     *
     * @param boolean $estRetourne
     *
     * @return Offre
     */
    public function setRetourne($estRetourne)
    {
        $estRetourne !== '' ?: $estRetourne = null;
        $this->retourne = $estRetourne;
        $this->updatedAt = new \DateTime('now');
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
     * @return Offre
     */
    public function setImage1($image1)
    {
        $this->image1 = $image1;

        return $this;
    }

    /**
     * Get modeVente
     *
     * @return integer
     */
    public function getModeVente()
    {
        return $this->modeVente;
    }

    /**
     * Set modeVente
     *
     * @param integer $modeVente
     *
     * @return Offre
     */
    public function setModeVente($modeVente)
    {
        $this->modeVente = $modeVente;
        $this->updatedAt = new \DateTime('now');
        return $this;
    }

    /**
     * Get modelDeSerie
     *
     * @return string
     */
    public function getModelDeSerie()
    {
        return $this->modelDeSerie;
    }

    /**
     * Set modelDeSerie
     *
     * @param string $modelDeSerie
     *
     * @return Offre
     */
    public function setModelDeSerie($modelDeSerie)
    {
        $modelDeSerie !== '' ?: $modelDeSerie = null;
        $this->modelDeSerie = $modelDeSerie;
        $this->updatedAt = new \DateTime('now');
        return $this;
    }

    /**
     * Get prixUnitaire
     *
     * @return string
     */
    public function getPrixUnitaire()
    {
        return $this->prixUnitaire;
    }

    /**
     * Set prixUnitaire
     *
     * @param string $prixUnitaire
     *
     * @return Offre
     */
    public function setPrixUnitaire($prixUnitaire)
    {
        $prixUnitaire !== '' ?: $prixUnitaire = null;
        $this->prixUnitaire = $prixUnitaire;
        $this->updatedAt = new \DateTime('now');
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
     * @return Offre
     */
    public function setQuantite($quantite)
    {
        $quantite !== '' ?: $quantite = null;
        $this->quantite = $quantite;
        $this->updatedAt = new \DateTime('now');
        return $this;
    }

    /**
     * Get evaluation
     *
     * @return integer
     */
    public function getEvaluation()
    {
        return $this->evaluation;
    }

    /**
     * Set evaluation
     *
     * @param integer $evaluation
     *
     * @return Offre
     */
    public function setEvaluation($evaluation)
    {
        $evaluation !== '' ?: $evaluation = null;
        $this->evaluation = $evaluation;
        return $this;
    }

    /**
     * Get remiseProduit
     *
     * @return string
     */
    public function getRemiseProduit()
    {
        return $this->remiseProduit;
    }

    /**
     * Set remiseProduit
     *
     * @param string $remiseProduit
     *
     * @return Offre
     */
    public function setRemiseProduit($remiseProduit)
    {
        $remiseProduit !== '' ?: $remiseProduit = null;
        $this->remiseProduit = $remiseProduit;
        $this->updatedAt = new \DateTime('now');
        return $this;
    }

    /**
     * Get typeOffre
     *
     * @return integer
     */
    public function getTypeOffre()
    {
        return $this->typeOffre;
    }

    /**
     * Set typeOffre
     *
     * @param integer $typeOffre
     *
     * @return Offre
     */
    public function setTypeOffre($typeOffre)
    {
        $typeOffre !== '' ?: $typeOffre = null;
        $this->typeOffre = $typeOffre;
        $this->updatedAt = new \DateTime('now');
        return $this;
    }

    /**
     * Get estValide
     *
     * @return boolean
     */
    public function isValide()
    {
        return $this->valide;
    }

    /**
     * Get valide
     *
     * @return boolean
     */
    public function getValide()
    {
        return $this->valide;
    }

    /**
     * Set estValide
     *
     * @param boolean $estValide
     *
     * @return Offre
     */
    public function setValide($estValide)
    {
        $estValide !== '' ?: $estValide = null;
        $this->valide = $estValide;
        $this->updatedAt = new \DateTime('now');
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
     * Get categorie
     *
     * @return Categorie
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Set categorie
     *
     * @param Categorie $categorie
     *
     * @return Offre
     */
    public function setCategorie(Categorie $categorie = null)
    {
        $this->categorie = $categorie;
        if ($categorie) $this->updatedAt = new \DateTime('now');
        return $this;
    }


    /**
     * Get boutique
     *
     * @return Boutique
     */
    public function getBoutique()
    {
        return $this->boutique;
    }

    /**
     * Set boutique
     *
     * @param Boutique $boutique
     *
     * @return Offre
     */
    public function setBoutique(Boutique $boutique = null)
    {
        $this->boutique = $boutique;
        if ($boutique) $this->updatedAt = new \DateTime('now');
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
     * @return Offre
     */
    public function setVendeur(Utilisateur_avm $vendeur)
    {
        $this->vendeur = $vendeur;
        if ($vendeur) $this->updatedAt = new \DateTime('now');
        return $this;
    }


    /**
     * Add commentaire
     *
     * @param Commentaire $commentaire
     *
     * @return Offre
     */
    public function addCommentaire(Commentaire $commentaire)
    {
        $this->commentaires[] = $commentaire;

        return $this;
    }

    /**
     * Remove commentaire
     *
     * @param Commentaire $commentaire
     */
    public function removeCommentaire(Commentaire $commentaire)
    {
        $this->commentaires->removeElement($commentaire);
    }

    /**
     * Get commentaires
     *
     * @return Collection
     */
    public function getCommentaires()
    {
        return $this->commentaires;
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
     * @return Offre
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get credit
     *
     * @return integer
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * Set credit
     *
     * @param integer $credit
     *
     * @return Offre
     */
    public function setCredit($credit)
    {
        $credit !== '' ?: $credit = null;
        $this->credit = $credit;

        return $this;
    }

    /**
     * Get unite
     *
     * @return string
     */
    public function getUnite()
    {
        return $this->unite;
    }

    /**
     * Set unite
     *
     * @param string $unite
     *
     * @return Offre
     */
    public function setUnite($unite)
    {
        $unite !== '' ?: $unite = null;
        $this->unite = $unite;
        $this->updatedAt = new \DateTime('now');
        return $this;
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
     * @param bool $publiable
     *
     * @return Offre
     */
    public function setPubliable($publiable)
    {
        $publiable !== '' ?: $publiable = null;
        $this->publiable = $publiable;
        $this->updatedAt = new \DateTime('now');
        return $this;
    }

    /**
     * Add produitTransaction
     *
     * @param Transaction $produitTransaction
     *
     * @return Offre
     */
    public function addProduitTransaction(Transaction $produitTransaction)
    {
        $this->produitTransactions[] = $produitTransaction;

        return $this;
    }

    /**
     * Remove produitTransaction
     *
     * @param Transaction $produitTransaction
     */
    public function removeProduitTransaction(Transaction $produitTransaction)
    {
        $this->produitTransactions->removeElement($produitTransaction);
    }

    /**
     * Get produitTransactions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProduitTransactions()
    {
        return $this->produitTransactions;
    }


    /**
     * Add specification
     *
     * @param Specification_achat $specification
     *
     * @return Offre
     */
    public function addSpecification(Specification_achat $specification)
    {
        $this->specifications[] = $specification;

        return $this;
    }

    /**
     * Remove specification
     *
     * @param Specification_achat $specification
     */
    public function removeSpecification(Specification_achat $specification)
    {
        $this->specifications->removeElement($specification);
    }

    /**
     * Get specifications
     *
     * @return Collection
     */
    public function getSpecifications()
    {
        return $this->specifications;
    }


    /**
     * Add communication
     *
     * @param Communication $communication
     *
     * @return Offre
     */
    public function addCommunication(Communication $communication)
    {
        $this->communications[] = $communication;

        return $this;
    }

    /**
     * Remove communication
     *
     * @param Communication $communication
     */
    public function removeCommunication(Communication $communication)
    {
        $this->communications->removeElement($communication);
    }

    /**
     * Get communications
     *
     * @return Collection
     */
    public function getCommunications()
    {
        return $this->communications;
    }

    /**
     * Add rabai
     *
     * @param Rabais_offre $rabai
     *
     * @return Offre
     */
    public function addRabai(Rabais_offre $rabai)
    {
        $this->rabais[] = $rabai;

        return $this;
    }

    /**
     * Remove rabai
     *
     * @param Rabais_offre $rabai
     */
    public function removeRabai(Rabais_offre $rabai)
    {
        $this->rabais->removeElement($rabai);
    }

    /**
     * Get rabais
     *
     * @return Collection
     */
    public function getRabais()
    {
        return $this->rabais;
    }

    /**
     * Add serviceApresVente
     *
     * @param Service_apres_vente $serviceApresVente
     *
     * @return Offre
     */
    public function addServiceApresVente(Service_apres_vente $serviceApresVente)
    {
        $this->service_apres_ventes[] = $serviceApresVente;

        return $this;
    }

    /**
     * Remove serviceApresVente
     *
     * @param Service_apres_vente $serviceApresVente
     */
    public function removeServiceApresVente(Service_apres_vente $serviceApresVente)
    {
        $this->service_apres_ventes->removeElement($serviceApresVente);
    }

    /**
     * Get serviceApresVentes
     *
     * @return Collection
     */
    public function getServiceApresVentes()
    {
        return $this->service_apres_ventes;
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
     * @return Offre
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get document
     *
     * @return \APM\AnimationBundle\Entity\Base_documentaire
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set document
     *
     * @param Base_documentaire $document
     *
     * @return Offre
     */
    public function setDocument(Base_documentaire $document = null)
    {
        $this->document = $document;
        $this->updatedAt = new \DateTime('now');
        return $this;
    }

    /**
     * Get apparenceNeuf
     *
     * @return integer
     */
    public function getApparenceNeuf()
    {
        return $this->apparenceNeuf;
    }

    /**
     * @param integer $apparenceNeuf
     * @return Offre
     */
    public function setApparenceNeuf($apparenceNeuf)
    {
        $apparenceNeuf !== '' ?: $apparenceNeuf = null;
        $this->apparenceNeuf = $apparenceNeuf;
        $this->updatedAt = new \DateTime('now');
        return $this;
    }

    /**
     * Get dureeGarantie
     *
     * @return integer
     */
    public function getDureeGarantie()
    {
        return $this->dureeGarantie;
    }

    /**
     * Set dureeGarantie
     *
     * @param integer $dureeGarantie
     * @return Offre
     */
    public function setDureeGarantie($dureeGarantie)
    {
        $dureeGarantie !== '' ?: $dureeGarantie = null;
        $this->dureeGarantie = $dureeGarantie;
        $this->updatedAt = new \DateTime('now');
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
     * @return Offre
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;
        $this->updatedAt = new \DateTime('now');
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
     * @return Offre
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
     * @return Offre
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
     * @return Offre
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
     * @return Offre
     */
    public function setBrochure($brochure)
    {
        $this->brochure = $brochure;

        return $this;
    }

    public function __toString()
    {
        return $this->designation;
    }
}
