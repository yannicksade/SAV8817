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
     * @var string
     * @Assert\Url
     * @ORM\Column(name="dataSheet", type="string", length=255, nullable=true)
     */
    private $dataSheet;

    /**
     * @var \DateTime
     * @Assert\DateTime
     * @ORM\Column(name="dateCreation", type="datetime", nullable=false)
     */
    private $dateCreation;

    /**
     * @var \DateTime
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
     * @Assert\Length(min=2)
     * @ORM\Column(name="designation", type="string", length=255, nullable=false)
     */
    private $designation;

    /**
     * @var integer
     * @ORM\Column(name="dureeGarantie", type="integer", nullable=true)
     */
    private $dureeGarantie;

    /**
     * @var boolean
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
     * @var integer
     * @ORM\Column(name="etat", type="integer", nullable=false)
     */
    private $etat;

    /**
     * @var integer
     * @ORM\Column(name="apparenceNeuf", type="integer", nullable=true)
     */
    private $apparenceNeuf;

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
     * @ORM\Column(name="updatedAt", type="datetime", nullable= false)
     * @var \DateTime
     */
    private $updatedAt;
    /**
     * @var integer
     * @ORM\Column(name="modeVente", type="integer", nullable=false)
     */
    private $modeVente;
    /**
     * @var string
     * @Assert\length(min=2, max=254)
     * @ORM\Column(name="modelDeSerie", type="string", length=255, nullable=true)
     */
    private $modelDeSerie;
    /**
     * @var string
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
     *
     * @ORM\Column(name="remiseProduit", type="decimal", nullable=true)
     */
    private $remiseProduit;
    /**
     * @var integer
     * @ORM\Column(name="typeOffre", type="integer", nullable=false)
     */
    private $typeOffre;
    /**
     * @var boolean
     * @ORM\Column(name="valide", type="boolean", nullable=true)
     */
    private $valide;

    /**
     * @var integer
     * @ORM\Column(name="publiable", type="integer", nullable=true)
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
    public function __construct($var = null)
    {
        $this->service_apres_ventes = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->remises = new ArrayCollection();
        $this->code = "OF" . $var;
        $this->specifications = new ArrayCollection();
        $this->communications = new ArrayCollection();
        $this->rabais = new ArrayCollection();
        $this->updatedAt = $this->dateCreation =  new \DateTime('now');
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setImageFile(File $image = null)
    {
        $image !== ''?:$image=null;
        $this->imageFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
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
        $dataSheet!== ''?:$dataSheet=null;
        $this->dataSheet = $dataSheet;
        $this->updatedAt = new \DateTime('now');

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
        $dateExpiration !== ''?:$dateExpiration=null;
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
        $description !== ''?:$description=null;
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
        $designation !== ''?:$designation=null;
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
        $estRetourne !== ''?:$estRetourne=null;
        $this->retourne = $estRetourne;
        $this->updatedAt = new \DateTime('now');
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
        $modelDeSerie !== ''?:$modelDeSerie=null;
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
        $prixUnitaire !== ''?:$prixUnitaire=null;
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
        $quantite !== ''?:$quantite=null;
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
        $evaluation !== ''?:$evaluation=null;
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
        $remiseProduit !== ''?:$remiseProduit=null;
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
        $typeOffre !== ''?:$typeOffre = null;
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
        $estValide !== ''?:$estValide=null;
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
        if($categorie)$this->updatedAt = new \DateTime('now');
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
       if($boutique) $this->updatedAt = new \DateTime('now');
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
        if($vendeur)$this->updatedAt = new \DateTime('now');
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
        $credit !== ''?:$credit=null;
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
        $unite !== ''?:$unite=null;
        $this->unite = $unite;
        $this->updatedAt = new \DateTime('now');
        return $this;
    }

    /**
     * Get publiable
     *
     * @return int
     */
    public function getPubliable()
    {
        return $this->publiable;
    }

    /**
     * Set publiable
     *
     * @param integer $publiable
     *
     * @return Offre
     */
    public function setPubliable($publiable)
    {
        $publiable !== ''?:$publiable=null;
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
     *  @return Offre
     */
    public function setApparenceNeuf($apparenceNeuf)
    {
        $apparenceNeuf !== ''?:$apparenceNeuf=null;
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
        $dureeGarantie !== ''?:$dureeGarantie=null;
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
}
