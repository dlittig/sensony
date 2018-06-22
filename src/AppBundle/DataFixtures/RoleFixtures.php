<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RoleFixtures extends Fixture {

    public function load(ObjectManager $manager) {
        $role = new Role('ROLE_ADMIN');

        $role->setName('ROLE_ADMIN');
        $role->setFriendlyName('Admin');

        $manager->persist($role);
        $this->addReference('admin-role', $role);

        $role = new Role('ROLE_USER');

        $role->setName('ROLE_USER');
        $role->setFriendlyName('User');

        $manager->persist($role);
        $this->addReference('user-role', $role);

        $manager->flush();
    }
}