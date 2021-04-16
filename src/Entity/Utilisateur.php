<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UtilisateurRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @UniqueEntity(fields={"login"}, message="There is already an account with this login")
 */
class Utilisateur implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Regex(
     *      pattern="/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{4,255}$/",
     *      match=true,
     *      message="votre mot de passe doit contenir une minuscule, une majuscule et un chiffre(de 4 a 255 caractère)"
     *      )
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $login;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $numero;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TypeUtilisateur", inversedBy="utilisateurs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $typeUtilisateur;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Fiche", mappedBy="author", orphanRemoval=true)
     */
    private $fiches;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="idPosteur", orphanRemoval=true)
     */
    private $messages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="idDestinataire", orphanRemoval=true)
     */
    private $messagesDestinataire;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Topic", mappedBy="idUtilisateur", orphanRemoval=true)
     */
    private $topics;
    
    /**
    * @Assert\EqualTo(
    *   propertyPath = "password",
    *   message= "Vos mots de passe ne sont pas identiques." 
    *   )
    */
    private $confirmMdp;

    private $oldMdp;

    private $newMdp;

    private $confirmNewMdp;

    public function __construct()
    {
        $this->fiches = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->messagesDestinataire = new ArrayCollection();
        $this->topics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTypeUtilisateur(): ?TypeUtilisateur
    {
        return $this->typeUtilisateur;
    }

    public function setTypeUtilisateur(?TypeUtilisateur $typeUtilisateur): self
    {
        $this->typeUtilisateur = $typeUtilisateur;

        return $this;
    }

    /**
     * @return Collection|Fiche[]
     */
    public function getFiches(): Collection
    {
        return $this->fiches;
    }

    public function addFich(Fiche $fich): self
    {
        if (!$this->fiches->contains($fich)) {
            $this->fiches[] = $fich;
            $fich->setAuthor($this);
        }

        return $this;
    }

    public function removeFich(Fiche $fich): self
    {
        if ($this->fiches->contains($fich)) {
            $this->fiches->removeElement($fich);
            // set the owning side to null (unless already changed)
            if ($fich->getAuthor() === $this) {
                $fich->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setIdPosteur($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getIdPosteur() === $this) {
                $message->setIdPosteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessagesDestinataire(): Collection
    {
        return $this->messagesDestinataire;
    }

    public function addMessagesDestinataire(Message $messagesDestinataire): self
    {
        if (!$this->messagesDestinataire->contains($messagesDestinataire)) {
            $this->messagesDestinataire[] = $messagesDestinataire;
            $messagesDestinataire->setIdDestinataire($this);
        }

        return $this;
    }

    public function removeMessagesDestinataire(Message $messagesDestinataire): self
    {
        if ($this->messagesDestinataire->contains($messagesDestinataire)) {
            $this->messagesDestinataire->removeElement($messagesDestinataire);
            // set the owning side to null (unless already changed)
            if ($messagesDestinataire->getIdDestinataire() === $this) {
                $messagesDestinataire->setIdDestinataire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Topic[]
     */
    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function addTopic(Topic $topic): self
    {
        if (!$this->topics->contains($topic)) {
            $this->topics[] = $topic;
            $topic->setIdUtilisateur($this);
        }

        return $this;
    }

    public function removeTopic(Topic $topic): self
    {
        if ($this->topics->contains($topic)) {
            $this->topics->removeElement($topic);
            // set the owning side to null (unless already changed)
            if ($topic->getIdUtilisateur() === $this) {
                $topic->setIdUtilisateur(null);
            }
        }

        return $this;
    }

    public function getOldMdp()
    {
        return $this->oldMdp;
    }

    public function setOldMdp(string $oldMdp): self
    {
        $this->oldMdp = $oldMdp;

        return $this;
    }

    public function getNewMdp()
    {
        return $this->newMdp;
    }

    public function setNewMdp(string $newMdp): self
    {
        $this->newMdp = $newMdp;

        return $this;
    }

    public function getConfirmNewMdp()
    {
        return $this->confirmNewMdp;
    }

    public function setConfirmNewMdp(string $confirmNewMdp): self
    {
        $this->confirmNewMdp = $confirmNewMdp;

        return $this;
    }

    public function getConfirmMdp()
    {
        return $this->confirmMdp;
    }

    public function setConfirmMdp(string $confirmMdp): self
    {
        $this->confirmMdp = $confirmMdp;

        return $this;
    }


    public function isGranted(String $r){
        $ig = false;

        for($i = 0; $i < count($this->roles); $i++){
            if($this->roles[$i] == $r)
                $ig = true;
        }

        return $ig;
    }
}
