<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 02/04/2017
 * Time: 08:21
 */
namespace APM\TransportBundle\Entity;

use APM\TransportBundle\Factory\TradeFactory;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Exclude;
/**
 * Transporteur Zone Intervention
 * @ORM\Table(name="transporteur_zoneintervention")
 * @ORM\Entity(repositoryClass="APM\TransportBundle\Repository\Transporteur_zoneinterventionRepository")
 * @ExclusionPolicy("all")
 */
class Transporteur_zoneintervention extends TradeFactory
{

    /**
     * Id
     * @var integer
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_transporteurZ_details", "others_transporteurZ_details"})
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     * @Expose
     * @Groups({"owner_transporteurZ_details", "others_transporteurZ_details"})
     * @ORM\Column(name="dateEnregistrement", type="datetime", nullable=false)
     */
    private $dateEnregistrement;

    /**
     * @var Profile_transporteur
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_transporteurZ_details", "others_transporteurZ_details"})
     * @ORM\ManyToOne(targetEntity="APM\TransportBundle\Entity\Profile_transporteur", inversedBy="transporteur_zones")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="transporteur_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $transporteur;

    /**
     * @var Zone_intervention
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_transporteurZ_details", "others_transporteurZ_details"})
     * @ORM\ManyToOne(targetEntity="APM\TransportBundle\Entity\Zone_intervention", inversedBy="zone_transporteurs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="zoneIntervention_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $zoneIntervention;


    function __construct()
    {
        $this->dateEnregistrement = new \DateTime('now');
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
     * @return Transporteur_zoneintervention
     */
    public function setTransporteur(Profile_transporteur $transporteur)
    {
        $this->transporteur = $transporteur;

        return $this;
    }

    /**
     * Get zoneIntervention
     *
     * @return Zone_intervention
     */
    public function getZoneIntervention()
    {
        return $this->zoneIntervention;
    }

    /**
     * Set zoneIntervention
     *
     * @param Zone_intervention $zoneIntervention
     *
     * @return Transporteur_zoneintervention
     */
    public function setZoneIntervention(Zone_intervention $zoneIntervention)
    {
        $this->zoneIntervention = $zoneIntervention;

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
     * @return Transporteur_zoneintervention
     */
    public function setDateEnregistrement($dateEnregistrement)
    {
        $this->dateEnregistrement = $dateEnregistrement;

        return $this;
    }
}
