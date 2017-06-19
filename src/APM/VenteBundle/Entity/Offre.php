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
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Offre
 *
 * @ORM\Table(name="offre")
 * @ORM\Entity(repositoryClass="APM\VenteBundle\Repository\OffreRepository")
 * @Vich\Uploadable
 * @UniqueEntity("code", message="impossible de creer l'offre, veuillez ressayer plus tard! ")
 */
class Offre extends TradeFactory
{

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private $code;

    /**
     * @var boolean
     * @Assert\Choice({0,1})
     * @ORM\Column(name="Garantie", type="boolean", nullable=true)
     */
    private $garantie;

    /**
     * @var string
     * @Assert\Url
     * @ORM\Column(name="dataSheet", type="string", length=255, nullable=true)
     */
    private $dataSheet;

    /**
     * @var DateTime
     * @Assert\DateTime
     * @ORM\Column(name="dateCreation", type="datetime", nullable=true)
     */
    private $dateCreation;

    /**
     * @var DateTime
     * @Assert\DateTime
     * @ORM\Column(name="dateExpiration", type="datetime", nullable=true)
     */
    private $dateExpiration;

    /**
     * @var string
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\Length(min=2, max=105)
     * @ORM\Column(name="designation", type="string", length=255, nullable=true)
     */
    private $designation;

    /**
     * @var \DateTime
     * @Assert\DateTime
     * @ORM\Column(name="dateFinGarantie", type="datetime", nullable=true)
     */
    private $dateFinGarantie;

    /**
     * @var boolean
     * @Assert\Choice({0,1})
     * @ORM\Column(name="estRetourne", type="boolean", nullable=true)
     */
    private $retourne;

    /**
     * @var string
     * @Assert\length(min=2, max=254)
     * @ORM\Column(name="unite", type="string", length=255, nullable=true)
     */
    private $unite;
    /**
     * @var string
     * @Assert\Choice({0,1})
     * @ORM\Column(name="etat", type="string", length=255, nullable=true)
     */
    private $etat;

    /**
     * @var string
     * @ORM\Column(name="image", type="string", nullable=true)
     */
    private $image;

    /**
     * @Assert\Image()
     * @Vich\UploadableField(mapping="entity_images", fileNameProperty="image")
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(name="updatedAt", type="datetime", nullable= true)
     * @var \DateTime
     */
    private $updatedAt;
    /**
     * @var string
     * @Assert\Choice({0,1,2,3})
     * @ORM\Column(name="modeVente", type="string", length=255, nullable=true)
     */
    private $modeVente;
    /**
     * @var string
     * @Assert\Issn
     * @ORM\Column(name="numeroDeSerie", type="string", length=255, nullable=true)
     */
    private $numeroDeSerie;
    /**
     * @var string
     *
     * @ORM\Column(name="prixUnitaire", type="decimal", nullable=true)
     */
    private $prixUnitaire;
    /**
     * @var integer
     * @Assert\GreaterThan(0)
     * @ORM\Column(name="quantite", type="integer", nullable=true)
     */
    private $quantite;
    /**
     * @var integer
     * @Assert\Range(min=0)
     * @ORM\Column(name="credit", type="integer", nullable=true)
     */
    private $credit;
    /**
     * @var integer
     * @Assert\Range(min=0, max=10)
     * @ORM\Column(name="rateEvaluation", type="smallint", nullable=true)
     */
    private $evaluation;
    /**
     * @var string
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="reference", type="string", length=255, nullable=true)
     */
    private $reference;
    /**
     * @var string
     *
     * @ORM\Column(name="remiseProduit", type="decimal", nullable=true)
     */
    private $remiseProduit;
    /**
     * @var string
     * @Assert\Choice({0,1,2})
     * @ORM\Column(name="typeOffre", type="string", length=255, nullable=true)
     */
    private $typeOffre;
    /**
     * @var boolean
     * @Assert\Choice({0,1})
     * @ORM\Column(name="valide", type="boolean", nullable=true)
     */
    private $valide;
    /**
     * @var boolean
     * @Assert\Choice({0,1})
     * @ORM\Column(name="disponibleEnStock", type="boolean", nullable=true)
     */
    private $disponibleEnStock;
    /**
     * @var boolean
     * @Assert\Choice({0,1})
     * @ORM\Column(name="publiable", type="boolean", nullable=true)
     */
    private $publiable;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var Categorie
     *
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Categorie", inversedBy="offres")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categorie_id", referencedColumnName="id")
     * })
     */
    private $categorie;
    /**
     * @var Boutique
     *
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Boutique", inversedBy="offres")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="boutique_id", referencedColumnName="id")
     * })
     */
    private $boutique;
    /**
     * @var Utilisateur_avm
     *
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="offres")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vendeur_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $vendeur;
    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\AchatBundle\Entity\Service_apres_vente", mappedBy="offre")
     *
     */
    private $service_apres_ventes;
    /**
     * @ORM\OneToMany(targetEntity="APM\UserBundle\Entity\Commentaire", mappedBy="offre")
     */
    private $commentaires;
    /**
     * @ORM\OneToMany(targetEntity="APM\VenteBundle\Entity\Remise", mappedBy="offre")
     */
    private $remises;
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
     * @var Base_documentaire
     *
     * @ORM\ManyToOne(targetEntity="APM\AnimationBundle\Entity\Base_documentaire", inversedBy="produits")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="document_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $document;

    /**
     * Constructor
     * @param string $var
     */
    public function __construct($var)
    {
        $this->service_apres_ventes = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->remises = new ArrayCollection();
        $this->code = "OF" . $var;
        $this->specifications = new ArrayCollection();
        $this->communications = new ArrayCollection();
        $this->rabais = new ArrayCollection();
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * Get garantie
     *
     * @return boolean
     */
    public function isGarantie()
    {
        return $this->garantie;
    }

    /**
     * Get garantie
     *
     * @return boolean
     */
    public function getGarantie()
    {
        return $this->garantie;
    }

    /**
     * Set garantie
     *
     * @param boolean $garantie
     *
     * @return Offre
     */
    public function setGarantie($garantie)
    {
        $this->garantie = $garantie;

        return $this;
    }

    /**
     * Get dataSheet
     *
     * @return string
     */
    public function getDataSheet()
    {
        return $this->dataSheet;
    }

    /**
     * Set dataSheet
     *
     * @param string $dataSheet
     *
     * @return Offre
     */
    public function setDataSheet($dataSheet)
    {
        $this->dataSheet = $dataSheet;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return DateTime
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

        return $this;
    }

    /**
     * Get dateExpiration
     *
     * @return DateTime
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
        $this->dateExpiration = $dateExpiration;

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
        $this->description = $description;

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
        $this->designation = $designation;

        return $this;
    }

    /**
     * Get dateGarantie
     *
     * @return \DateTime
     */
    public function getDateFinGarantie()
    {
        return $this->dateFinGarantie;
    }

    /**
     * Set dateFinGarantie
     *
     * @param integer $dateFinGarantie
     *
     * @return Offre
     */
    public function setDateFinGarantie($dateFinGarantie)
    {
        $this->dateFinGarantie = $dateFinGarantie;

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
        $this->retourne = $estRetourne;

        return $this;
    }

    /**
     * Get etat
     *
     * @return string
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set etat
     *
     * @param string $etat
     *
     * @return Offre
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Offre
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get modeVente
     *
     * @return string
     */
    public function getModeVente()
    {
        return $this->modeVente;
    }

    /**
     * Set modeVente
     *
     * @param string $modeVente
     *
     * @return Offre
     */
    public function setModeVente($modeVente)
    {
        $this->modeVente = $modeVente;

        return $this;
    }

    /**
     * Get numeroDeSerie
     *
     * @return string
     */
    public function getNumeroDeSerie()
    {
        return $this->numeroDeSerie;
    }

    /**
     * Set numeroDeSerie
     *
     * @param string $numeroDeSerie
     *
     * @return Offre
     */
    public function setNumeroDeSerie($numeroDeSerie)
    {
        $this->numeroDeSerie = $numeroDeSerie;

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
    public function setPrixunitaire($prixUnitaire)
    {
        $this->prixUnitaire = $prixUnitaire;

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
        $this->quantite = $quantite;

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
        $this->evaluation = $evaluation;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return Offre
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

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
        $this->remiseProduit = $remiseProduit;

        return $this;
    }

    /**
     * Get typeOffre
     *
     * @return string
     */
    public function getTypeOffre()
    {
        return $this->typeOffre;
    }

    /**
     * Set typeOffre
     *
     * @param string $typeOffre
     *
     * @return Offre
     */
    public function setTypeOffre($typeOffre)
    {
        $this->typeOffre = $typeOffre;

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
        $this->valide = $estValide;

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
    public function setVendeur(Utilisateur_avm $vendeur = null)
    {
        $this->vendeur = $vendeur;

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
     * Add remise
     *
     * @param Remise $remise
     *
     * @return Offre
     */
    public function addRemise(Remise $remise)
    {
        $this->remises[] = $remise;

        return $this;
    }

    /**
     * Remove remise
     *
     * @param Remise $remise
     */
    public function removeRemise(Remise $remise)
    {
        $this->remises->removeElement($remise);
    }

    /**
     * Get remises
     *
     * @return Collection
     */
    public function getRemises()
    {
        return $this->remises;
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
        $this->unite = $unite;

        return $this;
    }

    /**
     * Get disponibleEnStock
     *
     * @return boolean
     */
    public function getDisponibleEnStock()
    {
        return $this->disponibleEnStock;
    }

    /**
     * Set disponibleEnStock
     *
     * @param boolean $disponibleEnStock
     *
     * @return Offre
     */
    public function setDisponibleEnStock($disponibleEnStock)
    {
        $this->disponibleEnStock = $disponibleEnStock;

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
     * @param boolean $publiable
     *
     * @return Offre
     */
    public function setPubliable($publiable)
    {
        $this->publiable = $publiable;

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
     * @return \Doctrine\Common\Collections\Collection
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getServiceApresVentes()
    {
        return $this->service_apres_ventes;
    }

    public function __toString()
    {
        return $this->designation;
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
     * @param \APM\AnimationBundle\Entity\Base_documentaire $document
     *
     * @return Offre
     */
    public function setDocument(\APM\AnimationBundle\Entity\Base_documentaire $document = null)
    {
        $this->document = $document;

        return $this;
    }
}
