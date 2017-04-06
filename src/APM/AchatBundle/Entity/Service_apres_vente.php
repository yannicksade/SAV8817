<?php

namespace APM\AchatBundle\Entity;

use APM\AchatBundle\Factory\TradeFactory;
use Symfony\Component\Validator\Constraints as Assert;
use APM\UserBundle\Entity\Utilisateur_avm;
use APM\VenteBundle\Entity\Offre;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;



/**
 * Service_apres_vente
 *
 * @ORM\Table(name="Service_apres_vente")
 * @ORM\Entity(repositoryClass="APM\AchatBundle\Repository\Service_apres_venteRepository")
 * @UniqueEntity("codeSav")
 */
class Service_apres_vente extends TradeFactory
{
    /**
     * @var string
     *
     * @ORM\Column(name="codeSAV", type="string", length=255, nullable=false)
     *
     */
    private $codeSav;

    /**
     * @var \DateTime
     * @Assert\DateTime
     * @ORM\Column(name="dateDue", type="datetime", nullable=true)
     */
    private $dateDue;

    /**
     * @var string
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="descriptionPanne", type="string", length=255, nullable=true)
     */
    private $descriptionPanne;

    /**
     * @var string
     * @Assert\Choice({0,1,2,3})
     * @ORM\Column(name="etat", type="string", length=255, nullable=true)
     */
    private $etat;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="servicesApresVentes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $client;

    /**
     * @var Offre
     *
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
        $this->codeSav = "SV" . $var;
        $this->dateDue = new \DateTime();
    }

    /**
     * Get codeSav
     *
     * @return string
     */
    public function getCodeSav()
    {
        return $this->codeSav;
    }

    /**
     * Set codeSav
     *
     * @param string $codeSav
     *
     * @return Service_apres_vente
     */
    public function setCodeSav($codeSav)
    {
        $this->codeSav = $codeSav;

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

    /**
     * Get offre
     *
     * @return \APM\VenteBundle\Entity\Offre
     */
    public function getOffre()
    {
        return $this->offre;
    }
}
