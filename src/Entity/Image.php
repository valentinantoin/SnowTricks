<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 */
class Image
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tricks", inversedBy="images")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trickId;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $url;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrickId(): ?Tricks
    {
        return $this->trickId;
    }

    public function setTrickId(?Tricks $trickId): self
    {
        $this->trickId = $trickId;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }
}
