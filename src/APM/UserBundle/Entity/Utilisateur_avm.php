<?php

namespace APM\UserBundle\Entity;

use APM\AchatBundle\Entity\Groupe_offre;
use APM\AchatBundle\Entity\Service_apres_vente;
use APM\AchatBundle\Entity\Specification_achat;
use APM\AnimationBundle\Entity\Base_documentaire;
use APM\CoreBundle\Trade\CodeGenerator;
use APM\MarketingDistribueBundle\Entity\Conseiller;
use APM\TransportBundle\Entity\Livraison;
use APM\TransportBundle\Entity\Profile_transporteur;
use APM\VenteBundle\Entity\Boutique;
use APM\VenteBundle\Entity\Offre;
use APM\VenteBundle\Entity\Rabais_offre;
use APM\VenteBundle\Entity\Suggestion_produit;
use APM\VenteBundle\Entity\Transaction;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="utilisateur_avm")
 *
 */
class Utilisateur_avm extends Utilisateur
{

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @var boolean
     * @Assert\Choice({0,1})
     * @ORM\Column(name="estAcheteur", type="boolean", nullable=true)
     */
    private $isAcheteur;

    /**
     * @var boolean
     * @Assert\Choice({0,1})
     * @ORM\Column(name="estConseillerA1", type="boolean", nullable=true)
     */
    private $isConseillerA1;

    /**
     * @var boolean
     * @Assert\Choice({0,1})
     * @ORM\Column(name="estGerantBoutique", type="boolean", nullable=true)
     */
    private $isGerantBoutique;


    /**
     * @var boolean
     * @Assert\Choice({0,1})
     * @ORM\Column(name="estTransporteur", type="boolean", nullable=true)
     */
    private $isTransporteurLivreur;

    /**
     * @var boolean
     * @Assert\Choice({0,1})
     * @ORM\Column(name="estVendeur", type="boolean", nullable=true)
     */
    private $isVendeur;

    /**
     * @var integer
     * @Assert\GreaterThanOrEqual(0)
     * @ORM\Column(name="pointsDeFidelite", type="integer", nullable=true)
     */
    private $pointsDeFidelite;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\VenteBundle\Entity\Boutique", mappedBy="proprietaire")
     * @ORM\joinColumn(nullable=true)
     */
    private $boutiquesProprietaire; //Ensembles des boutiques_du_proprietaire

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="APM\VenteBundle\Entity\Offre")
     * @ORM\JoinTable(name="utilisateur_offre",
     *   joinColumns={
     *     @ORM\JoinColumn(name="utilisateuravm_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="offre_id", referencedColumnName="id")
     *   }
     * )
     */
    private $utilisateurOffres;


    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\VenteBundle\Entity\Offre", mappedBy="vendeur")
     */
    private $offres;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\UserBundle\Entity\Commentaire", mappedBy="utilisateur")
     */
    private $commentaires;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\VenteBundle\Entity\Transaction", mappedBy="auteur")
     */
    private $transactionsEffectues;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\UserBundle\Entity\Communication", mappedBy="emetteur")
     *
     */
    private $emetteurCommunications;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\UserBundle\Entity\Communication", mappedBy="recepteur")
     *
     */
    private $recepteurCommunications;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\VenteBundle\Entity\Rabais_offre", mappedBy="vendeur")
     */
    private $rabaisAccordes;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\VenteBundle\Entity\Rabais_offre", mappedBy="beneficiaireRabais")
     */
    private $rabaisRecus;


    /**
     *@var Collection
     * @ORM\OneToMany(targetEntity="APM\AnimationBundle\Entity\Base_documentaire", mappedBy="proprietaire")
     */
    private $documents;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\UserBundle\Entity\Groupe_relationnel", mappedBy="proprietaire")
     */
    private $groupesProprietaire;

    /**
     *@var Collection
     * @ORM\OneToMany(targetEntity="APM\UserBundle\Entity\Individu_to_groupe", mappedBy="individu", cascade={"remove"})
     *
     */
    private $individuGroupes;

    /**
     *@var Collection
     * @ORM\OneToMany(targetEntity="APM\AchatBundle\Entity\Groupe_offre", mappedBy="createur")
     */
    private $groupesOffres;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\AchatBundle\Entity\Service_apres_vente", mappedBy="client")
     */
    private $servicesApresVentes;


    /**
     * @var Profile_transporteur
     * @ORM\OneToOne(targetEntity="APM\TransportBundle\Entity\Profile_transporteur", mappedBy="utilisateur")
     */
    private $transporteur;

    /**
     * @ORM\OneToOne(targetEntity="APM\MarketingDistribueBundle\Entity\Conseiller", mappedBy="utilisateur", cascade={"persist","remove"})
     */
    private $profileConseiller;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\VenteBundle\Entity\Boutique", mappedBy="gerant")
     */
    private $boutiquesGerant; //Boutiques managées par le gerant


    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\AchatBundle\Entity\Specification_achat", mappedBy="utilisateur")
     */
    private $specifications;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\VenteBundle\Entity\Transaction", mappedBy="beneficiaire")
     */
    private $transactionsRecues;


    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\TransportBundle\Entity\Livraison", mappedBy="utilisateur")
     */
    private $livraisons;


    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->dateEnregistrement = new \DateTime();
        $this->roles = array('ROLE_USERAVM');
        $this->enabled = true;
        $this->code = "X" . CodeGenerator::getGenerator(6);

        $this->offres = new ArrayCollection();

        $this->servicesApresVentes = new ArrayCollection();
        $this->groupesOffres=new  ArrayCollection();

        $this->individuGroupes = new ArrayCollection();
        $this->groupesProprietaire=new  ArrayCollection();

        $this->documents = new ArrayCollection();
        $this->rabaisRecus = new  ArrayCollection();
        $this->rabaisAccordes = new  ArrayCollection();
        $this->recepteurCommunications = new ArrayCollection();
        $this->emetteurCommunications = new  ArrayCollection();

        $this->transactionsEffectues = new ArrayCollection();
        $this->transactionsRecues = new ArrayCollection();
        $this->commentaires=new  ArrayCollection();

        $this->boutiquesGerant = new ArrayCollection();
        $this->boutiquesProprietaire = new  ArrayCollection();
        $this->utilisateurOffres = new ArrayCollection();
        $this->specifications = new  ArrayCollection();

        $this->livraisons = new ArrayCollection();
        $this->transactionsRecues = new ArrayCollection();
        $this->transactionsEffectues = new ArrayCollection();

    }

    /**
     * Get estAcheteur
     *
     * @return boolean
     */
    public function isAcheteur()
    {
        return $this->isAcheteur;
    }

    /**
     * Get acheteur
     *
     * @return boolean
     */
    public function getIsAcheteur()
    {
        return $this->isAcheteur;
    }

    /**
     * Set estAcheteur
     *
     * @param boolean $estAcheteur
     *
     * @return Utilisateur_avm
     */
    public function setIsAcheteur($estAcheteur)
    {
        $this->isAcheteur = $estAcheteur;

        return $this;
    }


    /**
     * Get estGerantBoutique
     *
     * @return boolean
     */
    public function isGerantBoutique()
    {
        return $this->isGerantBoutique;
    }

    /**
     * Get gerantBoutique
     *
     * @return boolean
     */
    public function getIsGerantBoutique()
    {
        return $this->isGerantBoutique;
    }

    /**
     * Set estGerantBoutique
     *
     * @param boolean $estGerantBoutique
     *
     * @return Utilisateur_avm
     */
    public function setIsGerantBoutique($estGerantBoutique)
    {
        $this->isGerantBoutique = $estGerantBoutique;

        return $this;
    }

    /**
     * Get estTransporteur
     *
     * @return boolean
     */
    public function isTransporteurLivreur()
    {
        return $this->isTransporteurLivreur;
    }

    /**
     * Get transporteurLivreur
     *
     * @return boolean
     */
    public function getIsTransporteurLivreur()
    {
        return $this->isTransporteurLivreur;
    }

    /**
     * Set estTransporteur
     *
     * @param boolean $estTransporteur
     *
     * @return Utilisateur_avm
     */
    public function setIsTransporteurLivreur($estTransporteur)
    {
        $this->isTransporteurLivreur = $estTransporteur;

        return $this;
    }

    /**
     * Get estVendeur
     *
     * @return boolean
     */
    public function isVendeur()
    {
        return $this->isVendeur;
    }

    /**
     * Get vendeur
     *
     * @return boolean
     */
    public function getIsVendeur()
    {
        return $this->isVendeur;
    }

    /**
     * Set estVendeur
     *
     * @param boolean $estVendeur
     *
     * @return Utilisateur_avm
     */
    public function setIsVendeur($estVendeur)
    {
        $this->isVendeur = $estVendeur;

        return $this;
    }

    /**
     * Set latitudeX
     *
     * @param string $latitudeX
     *
     * @return Utilisateur_avm
     */
    public function setLatitudeX($latitudeX)
    {
        $this->latitudeX = $latitudeX;

        return $this;
    }

    /**
     * Get pointsDeFidelite
     *
     * @return integer
     */
    public function getPointsDeFidelite()
    {
        return $this->pointsDeFidelite;
    }

    /**
     * Set pointsDeFidelite
     *
     * @param integer $pointsDeFidelite
     *
     * @return Utilisateur_avm
     */
    public function setPointsDeFidelite($pointsDeFidelite)
    {
        $this->pointsDeFidelite = $pointsDeFidelite;

        return $this;
    }


    /**
     * Add boutiquesProprietaire
     *
     * @param Boutique $boutiquesProprietaire
     *
     * @return Utilisateur_avm
     */
    public function addBoutiquesProprietaire(Boutique $boutiquesProprietaire)
    {
        $this->boutiquesProprietaire[] = $boutiquesProprietaire;

        return $this;
    }

    /**
     * Remove boutiquesProprietaire
     *
     * @param Boutique $boutiquesProprietaire
     */
    public function removeBoutiquesProprietaire(Boutique $boutiquesProprietaire)
    {
        $this->boutiquesProprietaire->removeElement($boutiquesProprietaire);
    }

    /**
     * Get boutiquesProprietaire
     *
     * @return Collection
     */
    public function getBoutiquesProprietaire()
    {
        return $this->boutiquesProprietaire;
    }

    /**
     * Add offre
     *
     * @param Offre $offre
     *
     * @return Utilisateur_avm
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
     * Add commentaire
     *
     * @param Commentaire $commentaire
     *
     * @return Utilisateur_avm
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
     * Add groupesProprietaire
     *
     * @param Groupe_relationnel $groupesProprietaire
     *
     * @return Utilisateur_avm
     */
    public function addGroupesProprietaire(Groupe_relationnel $groupesProprietaire)
    {
        $this->groupesProprietaire[] = $groupesProprietaire;

        return $this;
    }

    /**
     * Remove groupesProprietaire
     *
     * @param Groupe_relationnel $groupesProprietaire
     */
    public function removeGroupesProprietaire(Groupe_relationnel $groupesProprietaire)
    {
        $this->groupesProprietaire->removeElement($groupesProprietaire);
    }

    /**
     * Get groupesProprietaire
     *
     * @return Collection
     */
    public function getGroupesProprietaire()
    {
        return $this->groupesProprietaire;
    }

    /**
     * Add groupesOffre
     *
     * @param Groupe_offre $groupesOffre
     *
     * @return Utilisateur_avm
     */
    public function addGroupesOffre(Groupe_offre $groupesOffre)
    {
        $this->groupesOffres[] = $groupesOffre;

        return $this;
    }

    /**
     * Remove groupesOffre
     *
     * @param Groupe_offre $groupesOffre
     */
    public function removeGroupesOffre(Groupe_offre $groupesOffre)
    {
        $this->groupesOffres->removeElement($groupesOffre);
    }

    /**
     * Get groupesOffres
     * @return Collection
     */
    public function getGroupesOffres()
    {
        return $this->groupesOffres;
    }

    /**
     * Add servicesApresVente
     *
     * @param Service_apres_vente $servicesApresVente
     *
     * @return Utilisateur_avm
     */
    public function addServicesApresVente(Service_apres_vente $servicesApresVente)
    {
        $this->servicesApresVentes[] = $servicesApresVente;

        return $this;
    }

    /**
     * Remove servicesApresVente
     *
     * @param Service_apres_vente $servicesApresVente
     */
    public function removeServicesApresVente(Service_apres_vente $servicesApresVente)
    {
        $this->servicesApresVentes->removeElement($servicesApresVente);
    }

    /**
     * Get servicesApresVentes
     *
     * @return Collection
     */
    public function getServicesApresVentes()
    {
        return $this->servicesApresVentes;
    }

    /**
     * Get transporteur
     *
     * @return Profile_transporteur
     */
    public function getTransporteur()
    {
        return $this->transporteur;
    }

    /**
     * Set transporteur
     *
     * @param Profile_transporteur $transporteur
     *
     * @return Utilisateur_avm
     */
    public function setTransporteur(Profile_transporteur $transporteur = null)
    {
        $this->transporteur = $transporteur;

        return $this;
    }

    /**
     * Get profileConseiller
     *
     * @return Conseiller
     */
    public function getProfileConseiller()
    {
        return $this->profileConseiller;
    }

    /**
     * Set profileConseiller
     *
     * @param Conseiller $profileConseiller
     *
     * @return Utilisateur_avm
     */
    public function setProfileConseiller(Conseiller $profileConseiller = null)
    {
        $this->profileConseiller = $profileConseiller;

        return $this;
    }

    /**
     * Add utilisateurOffre
     *
     * @param Offre $utilisateurOffre
     *
     * @return Utilisateur_avm
     */
    public function addUtilisateurOffre(Offre $utilisateurOffre)
    {
        $this->utilisateurOffres[] = $utilisateurOffre;

        return $this;
    }

    /**
     * Remove utilisateurOffre
     *
     * @param Offre $utilisateurOffre
     */
    public function removeUtilisateurOffre(Offre $utilisateurOffre)
    {
        $this->utilisateurOffres->removeElement($utilisateurOffre);
    }

    /**
     * Get utilisateurOffres
     *
     * @return Collection
     */
    public function getUtilisateurOffres()
    {
        return $this->utilisateurOffres;
    }

    /**
     * Add emetteurCommunication
     *
     * @param Communication $emetteurCommunication
     *
     * @return Utilisateur_avm
     */
    public function addEmetteurCommunication(Communication $emetteurCommunication)
    {
        $this->emetteurCommunications[] = $emetteurCommunication;

        return $this;
    }

    /**
     * Remove emetteurCommunication
     *
     * @param Communication $emetteurCommunication
     */
    public function removeEmetteurCommunication(Communication $emetteurCommunication)
    {
        $this->emetteurCommunications->removeElement($emetteurCommunication);
    }

    /**
     * Get emetteurCommunications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEmetteurCommunications()
    {
        return $this->emetteurCommunications;
    }

    /**
     * Add recepteurCommunication
     *
     * @param Communication $recepteurCommunication
     *
     * @return Utilisateur_avm
     */
    public function addRecepteurCommunication(Communication $recepteurCommunication)
    {
        $this->recepteurCommunications[] = $recepteurCommunication;

        return $this;
    }

    /**
     * Remove recepteurCommunication
     *
     * @param Communication $recepteurCommunication
     */
    public function removeRecepteurCommunication(Communication $recepteurCommunication)
    {
        $this->recepteurCommunications->removeElement($recepteurCommunication);
    }

    /**
     * Get recepteurCommunications
     *
     * @return Collection
     */
    public function getRecepteurCommunications()
    {
        return $this->recepteurCommunications;
    }

    /**
     * Add individuGroupe
     *
     * @param Individu_to_groupe $individuGroupe
     *
     * @return Utilisateur_avm
     */
    public function addIndividuGroupe(Individu_to_groupe $individuGroupe)
    {
        $this->individuGroupes[] = $individuGroupe;

        return $this;
    }

    /**
     * Remove individuGroupe
     *
     * @param Individu_to_groupe $individuGroupe
     */
    public function removeIndividuGroupe(Individu_to_groupe $individuGroupe)
    {
        $this->individuGroupes->removeElement($individuGroupe);
    }

    /**
     * Get individuGroupes
     *
     * @return Collection
     */
    public function getIndividuGroupes()
    {
        return $this->individuGroupes;
    }

    /**
     * Add document
     *
     * @param Base_documentaire $document
     *
     * @return Utilisateur_avm
     */
    public function addDocument(Base_documentaire $document)
    {
        $this->documents[] = $document;

        return $this;
    }

    /**
     * Remove document
     *
     * @param Base_documentaire $document
     */
    public function removeDocument(Base_documentaire $document)
    {
        $this->documents->removeElement($document);
    }

    /**
     * Get documents
     *
     * @return Collection
     */
    public function getDocuments()
    {
        return $this->documents;
    }


    /**
     * Add livraison
     *
     * @param Livraison $livraison
     *
     * @return Utilisateur_avm
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
     * Add specification
     *
     * @param Specification_achat $specification
     *
     * @return Utilisateur_avm
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
     * Add boutiquesGerant
     *
     * @param Boutique $boutiquesGerant
     *
     * @return Utilisateur_avm
     */
    public function addBoutiquesGerant(Boutique $boutiquesGerant)
    {
        $this->boutiquesGerant[] = $boutiquesGerant;

        return $this;
    }

    /**
     * Remove boutiquesGerant
     *
     * @param Boutique $boutiquesGerant
     */
    public function removeBoutiquesGerant(Boutique $boutiquesGerant)
    {
        $this->boutiquesGerant->removeElement($boutiquesGerant);
    }

    /**
     * Get boutiquesGerant
     *
     * @return Collection
     */
    public function getBoutiquesGerant()
    {
        return $this->boutiquesGerant;
    }

    public function __toString()
    {
        return parent::__toString();
    }

    /**
     * Add rabais Accorde
     *
     *
     * @param Rabais_offre $rabaisAccorde
     * @return Utilisateur_avm
     */
    public function addRabaisAccorde(Rabais_offre $rabaisAccorde)
    {
        $this->rabaisAccordes[] = $rabaisAccorde;

        return $this;
    }

    /**
     * Remove rabaisAccorde
     *
     * @param Rabais_offre $rabaisAccorde
     */
    public function removeRabaisAccorde(Rabais_offre $rabaisAccorde)
    {
        $this->rabaisAccordes->removeElement($rabaisAccorde);
    }

    /**
     * Get rabaisAccordes
     *
     * @return Collection
     */
    public function getRabaisAccordes()
    {
        return $this->rabaisAccordes;
    }

    /**
     * Add rabaisRecus
     *
     * @param Rabais_offre $rabaisRecus
     *
     * @return Utilisateur_avm
     */
    public function addRabaisRecus(Rabais_offre $rabaisRecus)
    {
        $this->rabaisRecus[] = $rabaisRecus;

        return $this;
    }

    /**
     * Remove rabaisRecus
     *
     * @param Rabais_offre $rabaisRecus
     */
    public function removeRabaisRecus(Rabais_offre $rabaisRecus)
    {
        $this->rabaisRecus->removeElement($rabaisRecus);
    }

    /**
     * Get rabaisRecus
     *
     * @return Collection
     */
    public function getRabaisRecus()
    {
        return $this->rabaisRecus;
    }


    /**
     * Add transactionsEffectue
     *
     * @param Transaction $transactionsEffectue
     *
     * @return Utilisateur_avm
     */
    public function addTransactionsEffectue(Transaction $transactionsEffectue)
    {
        $this->transactionsEffectues[] = $transactionsEffectue;

        return $this;
    }

    /**
     * Remove transactionsEffectue
     *
     * @param Transaction $transactionsEffectue
     */
    public function removeTransactionsEffectue(Transaction $transactionsEffectue)
    {
        $this->transactionsEffectues->removeElement($transactionsEffectue);
    }

    /**
     * Get transactionsEffectues
     *
     * @return Collection
     */
    public function getTransactionsEffectues()
    {
        return $this->transactionsEffectues;
    }

    /**
     * Add transactionsRecue
     *
     * @param Transaction $transactionsRecue
     *
     * @return Utilisateur_avm
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
     * @return boolean
     */
    public function isConseillerA1()
    {
        return $this->isConseillerA1;
    }

    /**
     * Get isConseillerA1
     *
     * @return boolean
     */
    public function getIsConseillerA1()
    {
        return $this->isConseillerA1;
    }

    /**
     * @param boolean $isConseillerA1
     */
    public function setIsConseillerA1(bool $isConseillerA1)
    {
        $this->isConseillerA1 = $isConseillerA1;
    }

}
