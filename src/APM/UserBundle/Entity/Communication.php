<?php

namespace APM\UserBundle\Entity;


use APM\AnimationBundle\Entity\Base_documentaire;
use APM\UserBundle\Entity\Piece_jointe;
use APM\UserBundle\Factory\TradeFactory;
use APM\VenteBundle\Entity\Offre;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Communication
 *
 * @ORM\Table(name="communication")
 * @ORM\Entity(repositoryClass="APM\UserBundle\Repository\CommunicationRepository")
 * @UniqueEntity("code")
 */
class Communication extends TradeFactory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="code", type="string", nullable=false)
     */
    private $code;

    /**
     * @var \DateTime
     * @Assert\DateTime
     * @ORM\Column(name="dateDeVigueur", type="datetime", nullable=true)
     */
    private $dateDeVigueur;

    /**
     * @var string
     * @Assert\Length(min=0)
     * @ORM\Column(name="contenu", type="text", nullable=true)
     */
    private $contenu;

    /**
     * @var \DateTime
     * @Assert\DateTime
     * @ORM\Column(name="dateEmission", type="datetime", nullable=false)
     */
    private $date;


    /**
     * @var string
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="reference", type="string", length=255, nullable=true)
     */
    private $reference;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="APM\VenteBundle\Entity\Offre", inversedBy="communications")
     */
    private $offres;


    /**
     * @var \DateTime
     * @Assert\DateTime
     * @ORM\Column(name="dateFin", type="datetime", nullable=true)
     */
    private $dateFin;

    /**
     * @var integer
     * @Assert\Choice({0,1,2,3,4,5,6})
     * @ORM\Column(name="etat", type="integer", nullable=true)
     */
    private $etat;

    /**
     * @var integer
     * @Assert\Choice({0,1,2,3,4,5})
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    private $type;

    /**
     * @var boolean
     * @Assert\choice({0,1})
     * @ORM\Column(name="valide", type="boolean", nullable=true)
     */
    private $valide;

    /**
     * @var Utilisateur_avm
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="emetteurCommunications")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="emetteur_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $emetteur;

    /**
     * @var Utilisateur_avm
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="recepteurCommunications")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="recepteur_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $recepteur;

    /**
     * @var
     * @ORM\ManyToMany(targetEntity="APM\AnimationBundle\Entity\Base_documentaire")
     */
    private $documents;


    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="objet", type="string", length=255, nullable=true)
     */
    private $objet;


    /**
     * Communication constructor.
     * @param string $var
     */
    public function __construct($var)
    {
        $this->date = new \DateTime();
        $this->offres= new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->code = 'CO' . $var;
    }

    /**
     * Get dateDeVigueur
     *
     * @return \DateTime
     */
    public function getDateDeVigueur()
    {
        return $this->dateDeVigueur;
    }

    /**
     * Set dateDeVigueur
     *
     * @param \DateTime $dateDeVigueur
     *
     * @return Communication
     */
    public function setDatedevigueur($dateDeVigueur)
    {
        $this->dateDeVigueur = $dateDeVigueur;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     *
     * @return Communication
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

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
     * @return Communication
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Communication
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get estvalide
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
     * @return Communication
     */
    public function setValide($estValide)
    {
        $this->valide = $estValide;

        return $this;
    }

    /**
     * Get emetteur
     *
     * @return Utilisateur_avm
     */
    public function getEmetteur()
    {
        return $this->emetteur;
    }

    /**
     * Set emetteur
     *
     * @param Utilisateur_avm $emetteur
     *
     * @return Communication
     */
    public function setEmetteur(Utilisateur_avm $emetteur)
    {
        $this->emetteur = $emetteur;

        return $this;
    }

    /**
     * Get recepteur
     *
     * @return Utilisateur_avm
     */
    public function getRecepteur()
    {
        return $this->recepteur;
    }

    /**
     * Set recepteur
     *
     * @param Utilisateur_avm $recepteur
     *
     * @return Communication
     */
    public function setRecepteur(Utilisateur_avm $recepteur)
    {
        $this->recepteur = $recepteur;

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
     * @return Communication
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     *
     * @return Communication
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Communication
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Add offre
     *
     * @param Offre $offre
     *
     * @return Communication
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
     * Add document
     *
     * @param Base_documentaire $document
     *
     * @return Communication
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

    public function __toString()
    {
        return $this->code;
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
     * @return Communication
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get objet
     *
     * @return string
     */
    public function getObjet()
    {
        return $this->objet;
    }

    /**
     * Set objet
     *
     * @param string $objet
     *
     * @return Communication
     */
    public function setObjet($objet)
    {
        $this->objet = $objet;

        return $this;
    }
}
