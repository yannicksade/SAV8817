<?php

namespace APM\AnimationBundle\Entity;

use APM\AnimationBundle\Factory\TradeFactory;
use APM\UserBundle\Entity\Utilisateur_avm;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Base_documentaire
 *
 * @ORM\Table(name="base_documentaire")
 * @ORM\Entity(repositoryClass="APM\AnimationBundle\Repository\Base_documentaireRepository")
 * @UniqueEntity("code")
 */
class Base_documentaire extends TradeFactory
{

    /**
     * @var string
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    private $code;
    /**
     * @var string
     * @Assert\Length(min=2, max=254)
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\Length(min=2, max=55)
     * @ORM\Column(name="objet", type="string", length=255, nullable=true)
     */
    private $objet;

    /**
     * @var string
     * @Assert\Url()
     * @ORM\Column(name="urlContenu", type="string", length=255, nullable=true)
     */
    private $urlContenu;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     * @Assert\DateTime
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var Utilisateur_avm
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="documents")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="proprietaire_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $proprietaire;



    /**
     * Base_documentaire constructor.
     * @param string $var
     */
    public function __construct($var)
    {
        $this->code = "BD" . $var;
        $this->date = new \DateTime();
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
     * @return Base_documentaire
     */
    public function setDescription($description)
    {
        $this->description = $description;

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
     * @return Base_documentaire
     */
    public function setObjet($objet)
    {
        $this->objet = $objet;

        return $this;
    }

    /**
     * Get urlContenu
     *
     * @return string
     */
    public function getUrlContenu()
    {
        return $this->urlContenu;
    }

    /**
     * Set urlContenu
     *
     * @param string $urlContenu
     *
     * @return Base_documentaire
     */
    public function setUrlContenu($urlContenu)
    {
        $this->urlContenu = $urlContenu;

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
     * Get proprietaire
     *
     * @return Utilisateur_avm
     */
    public function getProprietaire()
    {
        return $this->proprietaire;
    }

    /**
     * Set proprietaire
     *
     * @param Utilisateur_avm $proprietaire
     *
     * @return Base_documentaire
     */
    public function setProprietaire(Utilisateur_avm $proprietaire)
    {
        $this->proprietaire = $proprietaire;

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
     * @return Base_documentaire
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Base_documentaire
     */
    public function setDate($date)
    {
        $this->date = $date;

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
}
