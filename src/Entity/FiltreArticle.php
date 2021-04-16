<?php
namespace App\Entity;

class FiltreArticle{
	/*
	* @	var String|null
	*/
	private $nom;

	public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }
}
?>