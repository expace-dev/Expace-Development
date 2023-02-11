<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Users;
use App\Entity\Portfolios;
use App\Entity\Temoignages;
use App\Entity\ImagesPortfolios;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    
    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
        
    }
    
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        $adminUser = new Users();
        $adminUser->setPrenom('Frederic')
                  ->setNom('Husson')
                  ->setEmail('mega-services@hotmail.fr')
                  ->setPassword($this->encoder->hashPassword($adminUser, 'Laura@14111990'))
                  ->setAvatar('https://randomuser.me/api/portraits/men/29.jpg')
                  ->setUsername('fredy34560')
                  ->setRoles(['ROLE_ADMIN'])
                  ->setIsVerified(1)
                  ->setAdresse($faker->streetAddress)
                 ->setCodePostal($faker->postcode)
                 ->setVille($faker->city)
                 ->setPays($faker->country)
                 ->setSociete($faker->company)
                 ->setTelephone($faker->phoneNumber)
                 ->setCreatedAt($faker->dateTime('now'));

        $manager->persist($adminUser);

        // Nous gérons les utilisateurs

        $users = [];
        $genres = ['male', 'female'];

        for ($i=1; $i<=100; $i++) {

            $user = new Users;

            $genre = $faker->randomElement($genres);
            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';
            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;

            $hash = $this->encoder->hashPassword($user, 'password');

            $user->setEmail($faker->email)
                 ->setPassword($hash)
                 ->setIsVerified(1)
                 ->setAvatar($picture)
                 ->setUsername($faker->userName)
                 ->setNom($faker->lastName($genre))
                 ->setPrenom($faker->firstName($genre))
                 ->setAdresse($faker->streetAddress)
                 ->setCodePostal($faker->postcode)
                 ->setVille($faker->city)
                 ->setPays($faker->country)
                 ->setSociete($faker->company)
                 ->setTelephone($faker->phoneNumber)
                 ->setCreatedAt($faker->dateTime('now'));

            $manager->persist($user);
            $users[] = $user;        

        }

        // Nous gérons les Témoignages

        $temoignages = [];
        $note = ['0', '0.5', '1', '1.5', '2', '2.5', '3', '3.5', '4', '4.5', '5'];

        for ($i=1; $i<=5; $i++) {

            $temoignage = new Temoignages();

            $temoignage->setClient($faker->randomElement($users))
                       ->setDescription($faker->text(255))
                       ->setActif(1)
                       ->setNote($faker->randomElement($note))
                       ->setCreatedAt($faker->dateTime('now'));

            $manager->persist($temoignage);
            $temoignages[] = $temoignage;
        }
        
        // Nous gérons le portfolio

        $realisations = [];

        for ($i=1; $i<=5; $i++) {

            $realisation = new Portfolios();

            $realisation->setClient($faker->company())
                        ->setUrl($faker->url())
                        ->setDetails($faker->text(255))
                        ->setDate($faker->dateTime('now'));

            $manager->persist($realisation);
            $realisations[] = $realisation;

            $portfolioImages = [];

            // Nous gérons les images du portfolio
            for ($j = 1; $j <= mt_rand(0, 10); $j++) {

                $portfolioImage = new ImagesPortfolios();
                $realisaton = $realisations[mt_rand(0, count($realisations) -1)];

                $portfolioImage->setProjet($realisation)
                               ->setUrl($faker->imageUrl(1000,350));

                $manager->persist($portfolioImage);
                $portfolioImages[] = $portfolioImage;


            }
        }

        $manager->flush();
    }
}
