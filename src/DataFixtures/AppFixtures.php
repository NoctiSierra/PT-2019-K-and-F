<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Fiche;
use App\Entity\TypeFiche;
use App\Entity\SousCategorie;
use App\Entity\Topic;
use App\Entity\TypeUtilisateur;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $tabUser = [
            "user1",
            "user2",
            "user3",
            "user4",
            "user5",
            "user6",
            "user7"
        ];

        $tabNom = [
            "Nom1",
            "Nom2",
            "Nom3",
            "Nom4",
            "Nom5",
            "Nom6",
            "Nom7"
        ];

        $tabCat = [
            "Alimentaires",
            "Jardin",
            "Education",
            "Collectifs citoyens",
            "Mobilité",
            "Sorties",
            "Echange"
        ];

        $tabSousCat = [
            "Anti-Gaspillage",
            "Outils",
            "Fournitures",
            "Association",
            "Covoiturage",
            "Au Musée",
            "Entre particuliers"
        ];
        
        $tabUserEmail=[
            "user1@user.com",
            "user2@user.com",
            "user3@user.com",
            "user4@user.com",
            "user5@user.com",
            "user6@user.com",
            "user7@user.com",
        ];

        $tabTypeUser=[
            "Particulier",
            "Entreprise",
            "Association"
        ];


        $tabTypeFiche=[
            "Don",
            "Projet",
            "Bénévole/Talent"
        ];

        for( $i = 0 ; $i < count($tabCat) ; $i++ ) {

            //Insertion de tuples dans "TypeUtilisateur"
            
            if ($i < count($tabTypeUser)) {
                //Insertion de tuples dans "TypeUtilisateur"
                $typeUtilisateur = new TypeUtilisateur();

                $typeUtilisateur
                ->setNom($tabTypeUser[$i]);

                $manager->persist($typeUtilisateur);
            }

            //Insertion de tuples dans "Utilisateur" (simple utilisateur)
            $utilisateur = new Utilisateur();

            $utilisateur
            ->setTypeUtilisateur($typeUtilisateur)
            ->setNom($tabNom[$i])
            ->setLogin($tabUser[$i])
            ->setEmail($tabUserEmail[$i])
            ->setPassword($this->passwordEncoder->encodePassword(
                $utilisateur,
                '123456'
            ))
            ->setDescription('Une description ici !');
    
            $manager->persist($utilisateur);

            
            if ($i < count($tabTypeFiche)) {
                //Insertion de tuples dans "TypeFiche"
                $typeFiche = new TypeFiche();

                $typeFiche->setNom($tabTypeFiche[$i]);

                $manager->persist($typeFiche);
            }


            //Insertion de tuples dans "Fiche"
            $fiche = new Fiche();

            $fiche->setAuthor($utilisateur)
            ->setTitle('Titre')
            ->setContent('Content')
            ->setTypeFiche($typeFiche)
            ->setCreatedAt(new \DateTime());
            
            $manager->persist($fiche);


            // Insertion de tuples dans "Catégorie"
            $categorie = new Categorie();
            $categorie
                    ->setNom($tabCat[$i])
                    ->setDescription('La description de cette catégorie est ici.');

            $manager->persist($categorie);


            // Insertion de tuples dans "SousCategorie"
            $sousCategorie = new SousCategorie();
            $sousCategorie
                    ->setIdCategorie($categorie)
                    ->setNom($tabSousCat[$i]);

            $manager->persist($sousCategorie);


            // Insertion de tuples dans "Topic"
            $topic = new Topic();
            $topic  ->setIdUtilisateur($utilisateur)
                    ->setIdSousCategorie($sousCategorie)
                    ->setTitre("Titre $i")
                    ->setContenu("Contenu $i")
                    ->setDateHeureCreation(new \DateTime());

            $manager->persist($topic);
        }

        // Insertion d'un tuple dans "User" (admin)
        $admin = new Utilisateur();
        $admin->setTypeUtilisateur($typeUtilisateur);
        $admin->setNom('Admin');
        $admin->setLogin('admin');
        $admin->setEmail('admin@admin.com');
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin,
            '123456U.'
        ));
        $admin->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        $manager->flush();
    }
}
