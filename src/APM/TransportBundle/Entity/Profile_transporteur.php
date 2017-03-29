<?php

namespace APM\TransportBundle\Entity;

use APM\UserBundle\Entity\Utilisateur_avm;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * Profile_transporteur
 *
 * @ORM\Table(name="profile_transporteur")
 * @ORM\Entity(repositoryClass="APM\TransportBundle\Repository\TranspoteurRepository")
 * @UniqueEntity("code", message="Ce code est déjà pris.")
 * @UniqueEntity("matricule", message="Ce matricule existe déja.")
 *
 */
class Profile_transporteur
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
     * @ORM\Column(name="estLivreur_boutique", type="boolean", nullable=true)
     */
    private $livreurBoutique;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(min=2, max=55)
     * @ORM\Column(name="matricule", type="string", length=255, nullable=false)
     */
    private $matricule;

    /**
     * Id
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Utilisateur_avm
     *
     * @ORM\OneToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="transporteur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $utilisateur;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="APM\TransportBundle\Entity\Zone_intervention", mappedBy="zoneTransporteurs")
     */
    private $transporteurZones;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="APM\TransportBundle\Entity\Livraison", mappedBy="livreur")
     */
    private $livraisons;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->transporteurZones = new ArrayCollection();
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
     * Get estLivreurBoutique
     *
     * @return boolean
     */
    public function isLivreurBoutique()
    {
        return $this->livreurBoutique;
    }

    /**
     * Get livreurBoutique
     *
     * @return boolean
     */
    public function getLivreurBoutique()
    {
        return $this->livreurBoutique;
    }

    /**
     * Set estLivreurBoutique
     *
     * @param boolean $estLivreurBoutique
     *
     * @return Profile_transporteur
     */
    public function setLivreurBoutique($estLivreurBoutique)
    {
        $this->livreurBoutique = $estLivreurBoutique;

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
     * Add zone
     *
     * @param Zone_intervention $zone
     *
     * @return Profile_transporteur
     */
    public function addTransporteurZone(Zone_intervention $zone)
    {
        $this->transporteurZones[] = $zone;

        return $this;
    }

    /**
     * Remove zone
     *
     * @param Zone_intervention $zone
     */
    public function removeTransporteurZone(Zone_intervention $zone)
    {
        $this->transporteurZones->removeElement($zone);
    }

    /**
     * Get TransporteurZones
     *
     * @return Collection
     */
    public function getTransporteurZones()
    {
        return $this->transporteurZones;
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
}
