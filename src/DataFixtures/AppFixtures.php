<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Devis;
use App\Entity\Users;
use App\Entity\Projets;
use App\Entity\Articles;
use App\Entity\Comments;
use App\Entity\Factures;
use App\Entity\Categories;
use App\Entity\Portfolios;
use App\Entity\Temoignages;
use App\Entity\ImagesPortfolios;
use App\Services\InvoiceService;
use App\Repository\DevisRepository;
use App\Services\NumInvoiceService;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    
    
    public function __construct(UserPasswordHasherInterface $encoder, private NumInvoiceService $numInvoiceService, private DevisRepository $devisRepository, private InvoiceService $invoiceService, private ParameterBagInterface $params)
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
                 ->setCreatedAt($faker->dateTime('now'))
                 ->setFullName('Husson Frederic');

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

            $nom = $faker->lastName($genre);
            $prenom = $faker->firstName($genre);

            $user->setEmail($faker->email)
                 ->setPassword($hash)
                 ->setIsVerified(1)
                 ->setAvatar($picture)
                 ->setUsername($faker->userName)
                 ->setNom($nom)
                 ->setPrenom($prenom)
                 ->setAdresse($faker->streetAddress)
                 ->setCodePostal($faker->postcode)
                 ->setVille($faker->city)
                 ->setPays($faker->country)
                 ->setSociete($faker->company)
                 ->setTelephone($faker->phoneNumber)
                 ->setCreatedAt($faker->dateTime('now'))
                 ->setFullName($nom. ' ' .$prenom);

            $manager->persist($user);
            $users[] = $user;        

            dump('On crée le client' .$i);
        }

        // Nous gérons les Témoignages

        $temoignages = [];
        $note = ['0', '0.5', '1', '1.5', '2', '2.5', '3', '3.5', '4', '4.5', '5'];

        for ($i=1; $i<=50; $i++) {

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

        for ($i=1; $i<=50; $i++) {

            $realisation = new Portfolios();

            $realisation->setClient($faker->company())
                        ->setUrl($faker->url())
                        ->setDetails($faker->text(255))
                        ->setCreatedAt($faker->dateTime('now'));

            $manager->persist($realisation);
            $realisations[] = $realisation;

            $portfolioImages = [];

            // Nous gérons les images du portfolio
            for ($j = 1; $j <= mt_rand(1, 10); $j++) {

                $portfolioImage = new ImagesPortfolios();
                $realisaton = $realisations[mt_rand(0, count($realisations) -1)];

                $portfolioImage->setProjet($realisation)
                               ->setUrl($faker->imageUrl(800,600));

                $manager->persist($portfolioImage);
                $portfolioImages[] = $portfolioImage;


            }
        }

        $projets = [];

        for ($n=1; $n<=50; $n++) {

            $projet = new Projets();

            $statut = ['en_cours', 'terminé', 'ouverture'];

            $projet->setClient($faker->randomElement($users))
                   ->setTitre($faker->sentence())
                   ->setStatut($faker->randomElement($statut))
                   ->setBesoinClient('<p>' . join ('</p><p>', $faker->paragraphs(5)) . '</p>')
                   ->setCreatedAt($faker->dateTime('now'));
                   //->setUpdatedAt($faker->dateTime('now'));
                   

            $manager->persist($projet);
            $projets[] = $projet;
        }

        $devis = [];

        for ($n=1; $n<=50; $n++) {
            
            $devi = new Devis();

            $statut = ['en_attente', 'accepte', 'refus'];

            $numero = $this->numInvoiceService->Generate(
                numInvoice: $n,
                type: 'DEVIS'
            );

            

            $documentClient = $this->params->get('clients_directory');

            $url = $documentClient. '/' . $numero . '.pdf';
            
            $slug = $numero . '.pdf';
            $devi->setSlug($slug);

            $devi->setProjet($faker->randomElement($projets));
            $devi->setClient($devi->getProjet()->getClient());
            
            $services = [];

            for ($j = 1; $j <= mt_rand(1, 10); $j++) {
                
                $service = [
                    'type' => $faker->sentence(),
                    'quantite' => $faker->numberBetween(1, 100),
                    'tarif' => $faker->numberBetween(50, 450)
                ];

                $services[] = $service;
            }

            $devi->setServices($services);

            $tarif_total = null;
            foreach ($services as $values) {
                $tarif_total += $values['tarif']*$values['quantite'];
            }

            $this->invoiceService->CreateDevis(
                numero: $numero,
                url: $url,
                type: 'DEVIS',
                document: $devi
            );

            
            
            $devi->setStatut($faker->randomElement($statut))
                 ->setUrl($url)
                 ->setCreatedAt('123456789')
                 ->setDate($faker->dateTime('now'))
                 ->setAmount($tarif_total);

            //dd($devi);     

            $manager->persist($devi);
            $devis[] = $devi;
        }


        $projets = [];

        for ($n=1; $n<=50; $n++) {

            $projet = new Projets();

            $statut = ['en_cours', 'terminé', 'ouverture'];

            $projet->setClient($faker->randomElement($users))
                   ->setTitre($faker->sentence())
                   ->setStatut($faker->randomElement($statut))
                   ->setBesoinClient('<p>' . join ('</p><p>', $faker->paragraphs(5)) . '</p>')
                   ->setCreatedAt($faker->dateTime('now'));
                   //->setUpdatedAt($faker->dateTime('now'));
                   

            $manager->persist($projet);
            $projets[] = $projet;
        }

        $factures = [];

        for ($n=1; $n<=50; $n++) {
            
            $facture = new Factures();

            $statut = ['en_attente', 'paye'];

            $numero = $this->numInvoiceService->Generate(
                numInvoice: $n,
                type: 'FACTURE'
            );

            

            $documentClient = $this->params->get('clients_directory');

            $url = $documentClient. '/' . $numero . '.pdf';
            
            $slug = $numero . '.pdf';
            $facture->setSlug($slug);

            $facture->setProjet($faker->randomElement($projets));
            $facture->setClient($devi->getProjet()->getClient());
            
            $services = [];

            for ($j = 1; $j <= mt_rand(1, 10); $j++) {
                
                $service = [
                    'type' => $faker->sentence(),
                    'quantite' => $faker->numberBetween(1, 100),
                    'tarif' => $faker->numberBetween(50, 450)
                ];

                $services[] = $service;
            }

            $facture->setServices($services);

            $tarif_total = null;
            foreach ($services as $values) {
                $tarif_total += $values['tarif']*$values['quantite'];
            }

            $this->invoiceService->CreateDevis(
                numero: $numero,
                url: $url,
                type: 'FACTURE',
                document: $facture
            );

            
            
            $facture->setStatut($faker->randomElement($statut))
                 ->setUrl($url)
                 ->setCreatedAt($faker->dateTime('now'))
                 ->setAmount($tarif_total);

            //dd($devi);     

            $manager->persist($facture);
            $factures[] = $facture;
        }

        $categories = [];

        for ($i=1; $i<=10; $i++) {

            $categorie = new Categories;

            $mots = rand(1,3);

            $categorie->setName($faker->words($mots, true));

            $manager->persist($categorie);
            $categories[] = $categorie;


        }

        $articles = [];
        for ($i=1; $i<=500; $i++) {

            $article = new Articles();

            $article->setDate($faker->dateTime('now'))
                    ->setContent($faker->paragraph(5))
                    ->setTitle($faker->text(50))
                    ->setImg($faker->imageUrl(1024, 768))
                    ->setCategories($faker->randomElement($categories))
                    ->setAuteur($faker->randomElement($users));

            $manager->persist($article);
            $articles[] = $article;
        }

        $commentaires = [];
        for ($i=1; $i<=500; $i++) {

            $commentaire = new Comments();

            $commentaire->setArticles($faker->randomElement($articles))
                        ->setAuteur($faker->randomElement($users))
                        ->setContent($faker->paragraph(1))
                        ->setActive(true)
                        ->setCreatedAt($faker->dateTime('now'));

            $manager->persist($commentaire);
            $commentaires[] = $commentaire;
        }

        $manager->flush();
    }

    
}
