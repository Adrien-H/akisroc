<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TopicRepository")
 */
class Topic
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=63, nullable=false)
     */
    private $title;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Category",
     *     inversedBy="topics",
     *     cascade={"persist", "remove"}
     * )
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $category;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\Post",
     *     mappedBy="topic",
     *     orphanRemoval=true,
     *     cascade={"persist", "remove"}
     * )
     */
    private $posts;

    /**
     * Topic constructor.
     */
    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @param Category|null $category
     * @param iterable $posts
     * @return Topic
     */
    static public function create(string $title, Category $category = null, iterable $posts = []): Topic
    {
        $topic = new Topic();
        $topic->setTitle($title);
        $topic->setCategory($category);
        $topic->addPosts($posts);

        return $topic;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param null|string $title
     * @return self
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category|null $category
     * @return self
     */
    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @param int|null $id
     * @return self
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return Collection|null
     */
    public function getPosts(): ?Collection
    {
        return $this->posts;
    }

    /**
     * @param Collection|null $posts
     * @return self
     */
    public function setPosts(?Collection $posts): self
    {
        $this->posts = $posts;

        return $this;
    }

    /**
     * @param Post $post
     * @return self
     */
    public function addPost(?Post $post): self
    {
        if ($post && !$this->posts->contains($post)) {
            $this->posts->add($post);
        }

        return $this;
    }

    /**
     * @param iterable $posts
     * @return self
     */
    public function addPosts(?iterable $posts): self
    {
        if ($posts) {
            foreach ($posts as $post) {
                $this->addPost($post);
            }
        }

        return $this;
    }
}
