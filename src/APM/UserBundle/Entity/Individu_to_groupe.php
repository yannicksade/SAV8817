<?php

namespace APM\UserBundle\Entity;

use APM\UserBundle\Factory\TradeFactory;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;

/**
 * Individu_to_groupe
 *
 * @ORM\Table(name="individu_to_groupe")
 * @ORM\Entity(repositoryClass="APM\UserBundle\Repository\Individu_to_groupeRepository")
 *
 */
class Individu_to_groupe extends TradeFactory
{
    /**
     * @var \DateTime
     * @Expose
     * @Groups({"owner_individuToG_details", "others_individuToG_details"})
     * @Assert\DateTime
     * @ORM\Column(name="dateCreation", type="datetime", nullable=false)
     */
    private $dateInsertion;

    /**
     * @var integer
     * @Expose
     * @Groups({"owner_individuToG_details"})
     * @Assert\Choice({0,1,2})
     * @ORM\Column(name="propriete", type="integer", length=255, nullable=true)
     */
    private $propriete;

    /**
     * @var integer
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_individuToG_details", "others_individuToG_details"})
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Groupe_relationnel
     * @Expose
     * @Groups({"owner_list", "others_list", "owner_individuToG_details", "others_individuToG_details"})
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Groupe_relationnel", inversedBy="groupeIndividus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groupeRelationnel_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $groupeRelationnel;


    /**
     * @var Utilisateur_avm
     * @Groups({"owner_list", "others_list", "owner_individuToG_details", "others_individuToG_details"})
     * @ORM\ManyToOne(targetEntity="APM\UserBundle\Entity\Utilisateur_avm", inversedBy="individuGroupes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="individu_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $individu;

    function __construct()
    {
        $this->dateInsertion = new  \DateTime('now');
    }

    /**
     * Get propriete
     *
     * @return integer
     */
    public function getPropriete()
    {
        return $this->propriete;
    }

    /**
     * Set propriete
     *
     * @param integer $propriete
     *
     * @return Individu_to_groupe
     */
    public function setPropriete($propriete)
    {
        $this->propriete = $propriete;

        return $this;
    }


    /**
     * Get  Id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Get groupeRelationnel
     *
     * @return Groupe_relationnel
     */
    public function getGroupeRelationnel()
    {
        return $this->groupeRelationnel;
    }

    /**
     * Set groupeRelationnel
     *
     * @param Groupe_relationnel $groupeRelationnel
     *
     * @return Individu_to_groupe
     */
    public function setGroupeRelationnel(Groupe_relationnel $groupeRelationnel = null)
    {
        $this->groupeRelationnel = $groupeRelationnel;

        return $this;
    }

    /**
     * Get individu
     *
     * @return Utilisateur_avm
     */
    public function getIndividu()
    {
        return $this->individu;
    }

    /**
     * Set individu
     *
     * @param Utilisateur_avm $individu
     *
     * @return Individu_to_groupe
     */
    public function setIndividu(Utilisateur_avm $individu = null)
    {
        $this->individu = $individu;

        return $this;
    }

    /**
     * Get dateInsertion
     *
     * @return \DateTime
     */
    public function getDateInsertion()
    {
        return $this->dateInsertion;
    }

    /**
     * Set dateInsertion
     *
     * @param \DateTime $dateInsertion
     *
     * @return Individu_to_groupe
     */
    public function setDateInsertion($dateInsertion)
    {
        $this->dateInsertion = $dateInsertion;

        return $this;
    }
}
