<?php
/*les admins representent le staff de l'entreprise (+super admin)
*   il ne doit avoir qu'un seul admin avec le role super-admin
*/
namespace APM\UserBundle\Entity;

use APM\CoreBundle\Trade\CodeGenerator;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use PUGX\MultiUserBundle\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="APM\UserBundle\Repository\AdminRepository")
 * @ORM\Table(name="Admin")
 * @UniqueEntity(fields = "username", targetClass = "APM\UserBundle\Entity\Utilisateur", message="fos_user.username.already_used")
 * @UniqueEntity(fields = "email", targetClass = "APM\UserBundle\Entity\Utilisateur", message="fos_user.email.already_used")
 */
class Admin extends Utilisateur
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct() {

        parent::__construct();

        $this->dateEnregistrement = new \DateTime();
        $this->lastLogin = new \DateTime();
        $this->roles = array("ROLE_STAFF");
        $this->code = "XX" . CodeGenerator::getGenerator(4);
    }

    public function __toString()
    {
        return parent::__toString();
    }

}
