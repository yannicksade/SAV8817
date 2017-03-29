<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 26/01/2017
 * Time: 10:19
 */

namespace APM\CoreBundle\DataFixtures\ORM;


use APM\UserBundle\Entity\Admin;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadAdminData extends AbstractFixture implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    public $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        $admin = new Admin();
        $admin->setUsername("SINENOU");
        $admin->setEmail("admin@avm.com");
        $admin->setCode("ADMIN125T");
        $admin->setNom("YANNICK AVM");
        $admin->setRoles(array('ROLE_SUPER_ADMIN'));
        $encoder = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($admin, 'sinenou');
        $admin->setPassword($password);
        $this->addReference('admin', $admin);
        $manager->persist($admin);
        $manager->flush();
    }
}