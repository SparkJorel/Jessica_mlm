<?php

namespace App\DataFixtures;

use App\Entity\Membership;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->saveMembership($manager);
        $this->saveUser($manager);
    }

    private function saveMembership(ObjectManager $manager)
    {
        $fp = (new Membership())
            ->setName('Family Pack')
            ->setCode('FP')
            ->setCoefficent(1);
        $bp = (new Membership())
            ->setName('Business Pack')
            ->setCode('BP')
            ->setCoefficent(2);
        $pp = (new Membership())
            ->setName('Pro Pack')
            ->setCode('PP')
            ->setCoefficent(3);

        $manager->persist($fp);
        $manager->persist($bp);
        $manager->persist($pp);

        $manager->flush();
    }
    private function saveUser(ObjectManager $manager)
    {
        /**
         * @var Membership $membership
         */
        $membership = $manager
            ->getRepository(Membership::class)
            ->findOneBy([
                'code' => 'PP'
            ]);

        $jtwc = (new User())
            ->setPosition('left')
            ->setTitle('Dr')
            ->setFullname('GUENGANG DOUANLA Serges')
            ->setDateOfBirth(new DateTime("1983-05-14"))
            ->setGender('M')
            ->setEmail('racsafrique@gmail.com')
            ->setDocumentType('CNI')
            ->setCni(110981706)
            ->setMobilePhone('670913721')
            ->setUsername('jtwc')
            ->setCity('Yaoundé')
            ->setCategory('admin')
            ->setCountry('CM')
            ->setPosition('left')
            ->setState("Actif")
            ->setMembership($membership)
            ->setRoles(['ROLE_JTWC_USER', 'ROLE_JTWC_ADMIN'])
        ;

        $password = $this->encoder->encodePassword($jtwc, $jtwc->getEmail());
        $jtwc->setPassword($password);
        $manager->persist($jtwc);
        $manager->flush();
    }
}
