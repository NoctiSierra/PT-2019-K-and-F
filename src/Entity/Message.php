<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateEnvoi;

    /**
     * @ORM\Column(type="text")
     */
    private $contenu;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idPosteur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", inversedBy="messagesDestinataire")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idDestinataire;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Topic", inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idTopic;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateEnvoi(): ?\DateTimeInterface
    {
        return $this->dateEnvoi;
    }

    public function setDateEnvoi(\DateTimeInterface $dateEnvoi): self
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getIdPosteur(): ?Utilisateur
    {
        return $this->idPosteur;
    }

    public function setIdPosteur(?Utilisateur $idPosteur): self
    {
        $this->idPosteur = $idPosteur;

        return $this;
    }

    public function getIdDestinataire(): ?Utilisateur
    {
        return $this->idDestinataire;
    }

    public function setIdDestinataire(?Utilisateur $idDestinataire): self
    {
        $this->idDestinataire = $idDestinataire;

        return $this;
    }

    public function getIdTopic(): ?Topic
    {
        return $this->idTopic;
    }

    public function setIdTopic(?Topic $idTopic): self
    {
        $this->idTopic = $idTopic;

        return $this;
    }
}
