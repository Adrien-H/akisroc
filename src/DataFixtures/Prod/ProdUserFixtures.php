<?php

namespace App\DataFixtures\Prod;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\Argon2iPasswordEncoder;

/**
 * Class ProdUserFixtures
 * @package App\DataFixtures
 */
class ProdUserFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $cr = $this->getACredentials();

        $encoder = new Argon2iPasswordEncoder();
        $user = (new User())
            ->setUsername($cr[0])
            ->setEmail($cr[1])
            ->setSalt(User::generateSalt())
        ;
        $user->setAvatar(filter_var($cr[3], FILTER_VALIDATE_URL) ? $cr[3] : null);
        $user->setPassword($encoder->encodePassword($cr[2], $user->getSalt()));

        $manager->persist($user);
        $manager->flush();
    }

    /**
     * @return array
     */
    private function getACredentials(): array
    {
        $apwd = realpath(__DIR__ . DIRECTORY_SEPARATOR . '.apwd');
        if (!file_exists($apwd)) {
            throw new \RuntimeException('Must define admin credentials.');
        }
        $credentials = explode(PHP_EOL, file_get_contents($apwd));

        if (count($credentials) < 4) {
            throw new \RuntimeException('Incorrect credentials format.');
        }

        return array_slice($credentials, 0, 4);
    }
}