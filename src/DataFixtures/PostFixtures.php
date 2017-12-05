<?php

namespace App\DataFixtures;

use App\DataFixtures\Util\FixturesUtil;
use App\Entity\Post;
use App\Entity\Topic;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Class PostFixtures
 * @package App\DataFixtures
 */
class PostFixtures extends Fixture implements DependentFixtureInterface
{
    protected const COUNT = 2000;

    /** @var \Faker\Generator $faker */
    protected $faker;

    /**
     * PostFixtures constructor.
     */
    public function __construct()
    {
        $this->faker = Factory::create();
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        /** @var Topic[] $topics */
        $topics = FixturesUtil::filterReferences($this->referenceRepository->getReferences(), Topic::class);
        for ($i = 0; $i < self::COUNT; ++$i) {
            $topic = $topics[array_rand($topics)];
            $post = Post::create($this->faker->paragraphs(mt_rand(1, 5), true), $topic);
            $this->setReference("post_$i", $post);
            $manager->persist($post);
        }

        $manager->flush();
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            TopicFixtures::class
        ];
    }
}