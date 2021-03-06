<?php

namespace APM\TransportBundle\Entity;


use APM\TransportBundle\Factory\TradeFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Exclude;
/**
 * Zone_intervention
 *
 * @ORM\Table(name="Zone_intervention")
 * @ORM\Entity(repositoryClass="APM\TransportBundle\Repository\Zone_interventionRepository")
 * @UniqueEntity("code")
 * @ExclusionPolicy("all")
 */
class Zone_intervention extends TradeFactory
{
    /**
     * @var string
     * @Expose
     * @Groups({"owner_list", "owner_zone_details", "others_zone_details"})
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @var string
     * @Expose
     * @Groups({"owner_zone_details", "others_zone_details"})
     * @ORM\Column(name="zonetime", type="string", length=255, nullable=true)
     */
    private $zoneTime;

    /**
     * @var string
     * @Expose
     * @Groups({"owner_zone_details", "others_zone_details"})
     * @ORM\Column(name="ville", type="string", length=255, nullable=true)
     */
    private $ville;

    /**
     * @var string
     * @Expose
     * @Groups({"owner_zone_details", "others_zone_details"})
     * @Assert\Locale
     * @ORM\Column(name="language", type="string", length=255, nullable=true)
     */
    private $language;

    /**
     * @var string
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_zone_details", "others_zone_details"})
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_zone_details", "others_zone_details"})
     * @Assert\Length(min=2, max=100)
     * @ORM\Column(name="designation", type="string", length=255, nullable=true)
     */
    private $designation;

    /**
     * @var string
     * @Expose
     * @Groups({"owner_zone_details", "others_zone_details"})
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="adresse", type="string", length=255, nullable=true)
     */
    private $adresse;

    /**
     * @var string
     * @Expose
     * @Groups({"owner_zone_details", "others_zone_details"})
     * @Assert\Country
     * @ORM\Column(name="pays", type="string", length=255, nullable=true)
     */
    private $pays;

    /**
     * @var integer
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_zone_details", "others_zone_details"})
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     * @Expose
     * @Groups({"owner_zone_details"})
     * @Assert\DateTime
     * @ORM\Column(name="dateEnregistrement", type="datetime", nullable=false)
     */
    private $dateEnregistrement;


    /**
     * @var Profile_transporteur
     * @Expose
     * @Groups({"owner_zone_details", "others_zone_details"})
     * @ORM\ManyToOne(targetEntity="APM\TransportBundle\Entity\Profile_transporteur", inversedBy="zones")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="transporteurProprietaire_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $transporteur;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\TransportBundle\Entity\Transporteur_zoneintervention", mappedBy="zoneIntervention")
     */
    private $zone_transporteurs;

    /**
     * Constructor
     * @param string $var
     */
    public function __construct($var)
    {
        $this->code = "ZO" . $var;
        $this->zone_transporteurs =new  ArrayCollection();
        $this->dateEnregistrement = new \DateTime('now');
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set language
     *
     * @param string $language
     *
     * @return Zone_intervention
     */
    public function setLanguage($language)
    {
        $this->language = $language;

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
     * @return Zone_intervention
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }


    /**
     * Get pays
     *
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set pays
     *
     * @param string $pays
     *
     * @return Zone_intervention
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

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
     * @return Zone_intervention
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;

        return $this;
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
     * @return Zone_intervention
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get zoneTime
     *
     * @return string
     */
    public function getZoneTime()
    {
        return $this->zoneTime;
    }

    /**
     * Set zoneTime
     *
     * @param string $zoneTime
     *
     * @return Zone_intervention
     */
    public function setZoneTime($zoneTime)
    {
        $this->zoneTime = $zoneTime;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Zone_intervention
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
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
     * @return Zone_intervention
     */
    public function setTransporteur(Profile_transporteur $transporteur)
    {
        $this->transporteur = $transporteur;

        return $this;
    }

    /**
     * Add zoneTransporteur
     *
     * @param Transporteur_zoneintervention $zoneTransporteur
     *
     * @return Zone_intervention
     */
    public function addZoneTransporteur(Transporteur_zoneintervention $zoneTransporteur)
    {
        $this->zone_transporteurs[] = $zoneTransporteur;

        return $this;
    }

    /**
     * Remove zoneTransporteur
     *
     * @param Transporteur_zoneintervention $zoneTransporteur
     */
    public function removeZoneTransporteur(Transporteur_zoneintervention $zoneTransporteur)
    {
        $this->zone_transporteurs->removeElement($zoneTransporteur);
    }

    /**
     * Get zoneTransporteurs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getZoneTransporteurs()
    {
        return $this->zone_transporteurs;
    }

    public function __toString()
    {
        return $this->designation;
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
     * @return Zone_intervention
     */
    public function setDateEnregistrement($dateEnregistrement)
    {
        $this->dateEnregistrement = $dateEnregistrement;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return Zone_intervention
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }
}
