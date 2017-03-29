<?php

namespace APM\UserBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Communication
 *
 * @ORM\Table(name="communication")
 * @ORM\Entity(repositoryClass="APM\UserBundle\Repository\CommunicationRepository")
 */
class Communication
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var \DateTime
     * @Assert\DateTime
     * @ORM\Column(name="dateDeVigueur", type="datetime", nullable=true)
     */
    private $dateDeVigueur;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="reference", type="string", length=255, nullable=true)
     */
    private $reference;


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
     * Communication
     *
     * @ORM\OneToOne(targetEntity="APM\UserBundle\Entity\Message", cascade={"persist","remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="message_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $message;


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
     * Get communication
     *
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }


    /**
     * Set communication
     *
     * @param Message $communication
     *
     * @return Communication
     */
    public function setMessage(Message $communication)
    {
        $this->message = $communication;

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
     * @return Communication
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }
}
