<?php

namespace APM\AchatBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use APM\AchatBundle\Factory\TradeFactory;
use Symfony\Component\Validator\Constraints as Assert;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Offre;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Exclude;
/**
 * Service_apres_vente
 *
 * @ORM\Table(name="Service_apres_vente")
 * @ORM\Entity(repositoryClass="APM\AchatBundle\Repository\Service_apres_venteRepository")
 * @UniqueEntity("code")
 * @ExclusionPolicy("all")
 */
class Service_apres_vente extends TradeFactory
{
    /**
     * @var string
     * @Expose
     * @Groups({"owner_list", "others_sav_details", "owner_sav_details"})
     * @ORM\Column(name="codeSAV", type="string", length=255, nullable=false)
     *
     */
    private $code;

    /**
     * @var \DateTime
     * @Expose
     * @Groups({"others_sav_details", "owner_sav_details"})
     * @Assert\DateTime
     * @ORM\Column(name="dateDue", type="datetime", nullable=true)
     */
    private $dateDue;

    /**
     * @var string
     * @Expose
     * @Groups({"owner_list", "others_list", "others_sav_details", "owner_sav_details"})
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="descriptionPanne", type="string", length=255, nullable=true)
     */
    private $descriptionPanne;

    /**
     * @var integer
     * @Expose
     * @Groups({"others_sav_details", "owner_sav_details"})
     * @Assert\Choice({0,1,2,3})
     * @ORM\Column(name="etat", type="integer", nullable=true)
     */
    private $etat;

    /**
     * @var string
     * @Expose
     * @Groups({"others_sav_details", "owner_sav_details"})
     * @Assert\Length(min=2)
     * @ORM\Column(name="commentaire", type="string", length=255, nullable=true)
     */
    private $commentaire;


    /**
     * @var integer
     * @Expose
     * @Groups({"owner_list", "others_list", "others_sav_details", "owner_sav_details"})
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Utilisateur_avm
     * @Expose
     * @Groups({"others_sav_details", "owner_sav_details"})
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="servicesApresVentes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $client;

    /**
     * @var Offre
     * @Expose
     * @Groups({"others_sav_details", "owner_sav_details"})
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Offre", inversedBy="service_apres_ventes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="offre_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $offre;


    /**
     * Constructor
     * @param string $var
     */
    public function __construct($var)
    {
        $this->etat = 8;
        $this->code = "SC" . $var;
        $this->dateDue = new \DateTime("now");
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
     * Set codeSav
     *
     * @param string $code
     *
     * @return Service_apres_vente
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get dateDue
     *
     * @return \DateTime
     */
    public function getDateDue()
    {
        return $this->dateDue;
    }

    /**
     * Set dateDue
     *
     * @param \DateTime $dateDue
     *
     * @return Service_apres_vente
     */
    public function setDateDue($dateDue)
    {
        $this->dateDue = $dateDue;

        return $this;
    }

    /**
     * Get descriptionPanne
     *
     * @return string
     */
    public function getDescriptionPanne()
    {
        return $this->descriptionPanne;
    }

    /**
     * Set descriptionPanne
     *
     * @param string $descriptionPanne
     *
     * @return Service_apres_vente
     */
    public function setDescriptionPanne($descriptionPanne)
    {
        $this->descriptionPanne = $descriptionPanne;

        return $this;
    }

    /**
     * Get etat
     *
     * @return  integer
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
     * @return Service_apres_vente
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Id
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Get client
     *
     * @return Utilisateur_avm
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set client
     *
     * @param Utilisateur_avm $client
     *
     * @return Service_apres_vente
     */
    public function setClient(Utilisateur_avm $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get offre
     *
     * @return \APM\VenteBundle\Entity\Offre
     */
    public function getOffre()
    {
        return $this->offre;
    }

    /**
     * Set offre
     *
     * @param Offre $offre
     *
     * @return Service_apres_vente
     */
    public function setOffre(Offre $offre)
    {
        $this->offre = $offre;

        return $this;
    }

    public function __toString()
    {
        return $this->code;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return Service_apres_vente
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }
}
