<?php

namespace APM\TransportBundle\Entity;

use APM\TransportBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Exclude;
/**
 * Profile_transporteur
 *
 * @ORM\Table(name="profile_transporteur")
 * @ORM\Entity(repositoryClass="APM\TransportBundle\Repository\TransporteurRepository")
 * @UniqueEntity("code", message="Ce code est déjà pris.")
 * @UniqueEntity("matricule", message="Ce matricule existe déja.")
 * @ExclusionPolicy("all")
 */
class Profile_transporteur extends TradeFactory
{
    /**
     * @var string
     * @Expose
     * @Groups({"owner_list", "owner_transporteur_details", "others_transporteur_details"})
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private $code;

    /**
     * @var \DateTime
     * @Expose
     * @Groups({"owner_transporteur_details"})
     * @Assert\DateTime
     * @ORM\Column(name="dateEnregistrement", type="datetime", nullable=false)
     */
    private $dateEnregistrement;

    /**
     * @var string
     * @Expose
     * @Groups({"owner_transporteur_details", "others_transporteur_details"})
     * @Assert\NotBlank
     * @Assert\Length(min=2, max=205)
     * @ORM\Column(name="matricule", type="string", length=255, nullable=false)
     */
    private $matricule;

    /**
     * @var string
     * @Expose
     * @Groups({"others_transporteur_details", "owner_transporteur_details"})
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private $description;

    /**
     * Id
     * @var integer
     * @Expose
     * @Groups({"owner_list", "others_list", "others_transporteur_details", "owner_transporteur_details"})
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Utilisateur_avm
     * @Expose
     * @Groups({"owner_transporteur_details"})
     * @ORM\OneToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="transporteur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $utilisateur;

    /**
     * @var Livreur_boutique
     * @Expose
     * @Groups({"others_transporteur_details", "owner_transporteur_details"})
     * @ORM\OneToOne(targetEntity="APM\TransportBundle\Entity\Livreur_boutique", mappedBy="transporteur")
     */
    private $livreurBoutique;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\TransportBundle\Entity\Zone_intervention", mappedBy="transporteur")
     */
    private $zones;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\TransportBundle\Entity\Livraison", mappedBy="livreur")
     */
    private $livraisons;


    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\TransportBundle\Entity\Transporteur_zoneintervention", mappedBy="transporteur")
     */
    private $transporteur_zones;


    /**
     * Constructor
     * @param string $var
     */
    public function __construct($var)
    {
        $this->transporteur_zones = new ArrayCollection();
        $this->zones = new ArrayCollection();
        $this->dateEnregistrement = new \DateTime('now');
        $this->code = "XP" . $var;
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
     * @return Profile_transporteur
     */
    public function setCode($code)
    {
        $this->code = $code;

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
     * @return Profile_transporteur
     */
    public function setMatricule($matricule)
    {
        $this->matricule = $matricule;

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
     * @return Profile_transporteur
     */
    public function setUtilisateur(Utilisateur_avm $utilisateur)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Add livraison
     *
     * @param Livraison $livraison
     *
     * @return Profile_transporteur
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
     * Add zone
     *
     * @param Zone_intervention $zone
     *
     * @return Profile_transporteur
     */
    public function addZone(Zone_intervention $zone)
    {
        $this->zones[] = $zone;

        return $this;
    }

    /**
     * Remove zone
     *
     * @param Zone_intervention $zone
     */
    public function removeZone(Zone_intervention $zone)
    {
        $this->zones->removeElement($zone);
    }

    /**
     * Get zones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getZones()
    {
        return $this->zones;
    }

    /**
     * Add transporteurZone
     *
     * @param Transporteur_zoneintervention $transporteurZone
     *
     * @return Profile_transporteur
     */
    public function addTransporteurZone(Transporteur_zoneintervention $transporteurZone)
    {
        $this->transporteur_zones[] = $transporteurZone;

        return $this;
    }

    /**
     * Remove transporteurZone
     *
     * @param Transporteur_zoneintervention $transporteurZone
     */
    public function removeTransporteurZone(Transporteur_zoneintervention $transporteurZone)
    {
        $this->transporteur_zones->removeElement($transporteurZone);
    }

    /**
     * Get transporteurZones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransporteurZones()
    {
        return $this->transporteur_zones;
    }

    /**
     * Get livreurBoutique
     *
     * @return Livreur_boutique
     */
    public function getLivreurBoutique()
    {
        return $this->livreurBoutique;
    }

    /**
     * Set livreurBoutique
     *
     * @param Livreur_boutique $livreurBoutique
     *
     * @return Profile_transporteur
     */
    public function setLivreurBoutique(Livreur_boutique $livreurBoutique = null)
    {
        $this->livreurBoutique = $livreurBoutique;

        return $this;
    }

    public function __toString()
    {
        return $this->matricule;
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
     * @return Profile_transporteur
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
     * @return Profile_transporteur
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}
