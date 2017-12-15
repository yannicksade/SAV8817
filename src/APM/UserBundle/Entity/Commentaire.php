<?php

namespace APM\UserBundle\Entity;

use APM\UserBundle\Factory\TradeFactory;
use APM\VenteBundle\Entity\Offre;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;

/**
 * Commentaire
 *
 * @ORM\Table(name="commentaire")
 * @ORM\Entity(repositoryClass="APM\UserBundle\Repository\CommentaireRepository")
 * @ExclusionPolicy("all")
 */
class Commentaire extends TradeFactory
{

    /**
     * @var string
     * @Expose
     * @Groups({"others_list", "owner_list", "owner_commentaire_details", "others_commentaire_details"})
     * @Assert\Length(min=1, max=2450)
     * @ORM\Column(name="contenu", type="text", length=2500, nullable=true)
     */
    private $contenu;

    /**
     * @var boolean
     * @Expose
     * @Groups({"owner_commentaire_details"})
     * @ORM\Column(name="publiable", type="boolean", nullable=true)
     */
    private $publiable;

    /**
     * @var \DateTime
     * @Expose
     * @Groups({"owner_commentaire_details", "others_commentaire_details"})
     * @Assert\DateTime
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var integer
     * @Expose
     * @Groups({"owner_commentaire_details", "others_commentaire_details"})
     * @Assert\Range(min=0, max=10)
     * @ORM\Column(name="evaluation", type="smallint", nullable=true)
     */
    private $evaluation;

    /**
     * Id
     * @var integer
     * @Expose
     * @Groups({"others_list", "owner_list", "others_commentaire_details", "owner_commentaire_details"})
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Utilisateur_avm
     * @Expose
     * @Groups({"owner_commentaire_details", "others_commentaire_details"})
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm" , inversedBy="commentaires")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $utilisateur;

    /**
     * @var Offre
     * @Expose
     * @Groups({"owner_commentaire_details", "others_commentaire_details"})
     * @ORM\ManyToOne(targetEntity="APM\VenteBundle\Entity\Offre", inversedBy="commentaires")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="offre_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $offre;

    /**
     * Commentaire constructor.
     */
    public function __construct()
    {
        $this->date = new \DateTime('now');
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
     * @return Commentaire
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
     * @return Commentaire
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get evaluation
     *
     * @return integer
     */
    public function getEvaluation()
    {
        return $this->evaluation;
    }

    /**
     * Set evaluation
     *
     * @param integer $evaluation
     *
     * @return Commentaire
     */
    public function setEvaluation($evaluation)
    {
        $this->evaluation = $evaluation;

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
     * @return Commentaire
     */
    public function setUtilisateur(Utilisateur_avm $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get offre
     *
     * @return Offre
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
     * @return Commentaire
     */
    public function setOffre(Offre $offre)
    {
        $this->offre = $offre;

        return $this;
    }

    public function __toString()
    {
        return substr($this->contenu, 7);
    }

    /**
     * @return boolean
     */
    public function isPubliable()
    {
        return $this->publiable;
    }

    /**
     * Get publiable
     *
     * @return boolean
     */
    public function getPubliable()
    {
        return $this->publiable;
    }

    /**
     * @param boolean $publiable
     */
    public function setPubliable(bool $publiable)
    {
        $this->publiable = $publiable;
    }
}
