<?php

namespace AppBundle\DataFixtures;

use App\Entity\User;
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
        $user->setRole('ROLE_ADMIN');

        $manager->persist($user);

        $manager->flush();
    }
}