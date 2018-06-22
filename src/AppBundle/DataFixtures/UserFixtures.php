<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture {

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager) {
        $user = new User();

        $user->setUsername('admin');
        $user->setMail('admin@localhost');
        $user->setPassword($this->encoder->encodePassword($user, 'admin'));
        $user->addRole($this->getReference('admin-role'));

        $manager->persist($user);

        $user = new User();

        $user->setUsername('user');
        $user->setMail('user@localhost');
        $user->setPassword($this->encoder->encodePassword($user, 'user'));
        $user->addRole($this->getReference('user-role'));

        $manager->persist($user);

        $manager->flush();
    }

    public function getDependencies() {
        return array(
            RoleFixtures::class,
        );
    }
}