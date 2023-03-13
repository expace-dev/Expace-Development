<?php

namespace App\Controller\Client;

use DateTime;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Entity\Factures;
use App\Entity\Paiements;
use Stripe\Checkout\Session;
use App\Repository\FacturesRepository;
use App\Repository\PaiementsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Exception\SignatureVerificationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/profile/paiement')]
class PaiementController extends AbstractController {

    /**
     * Permet de vérifier la validité d'un paiement
     * et le traiter en conséquence
     *
     * @param FacturesRepository $facturesRepository
     * @param PaiementsRepository $paiementsRepository
     * @return void
     */
    #[Route('/webhook', name: 'app_paiement_webhook', methods:['POST'])]
    public function webhook(FacturesRepository $facturesRepository, PaiementsRepository $paiementsRepository)
    {
        // This is your Stripe CLI webhook secret for testing your endpoint locally.
        $endpoint_secret = 'whsec_a665c62b8bd86288b8f925483780f2b8cb9b54831951e8086d1016792cc7d287';

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        

        try {
            $event = \Stripe\Webhook::constructEvent(
              $payload, $sig_header, $endpoint_secret
            );
        } 
        catch(\UnexpectedValueException $e) {
            // Invalid payload
            return new Response(Response::HTTP_BAD_REQUEST);
            exit();
        } 
        catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return new Response(Response::HTTP_BAD_REQUEST);
            exit();
        }

        if ($event->type === 'checkout.session.completed') {

            $facture = $facturesRepository->findOneBy(['paiementIntent' => $event->data->object->payment_intent]);

            $facture->setStatut($event->data->object->status);

            $paiement = new Paiements();

            $paiement->setCreatedAt(new DateTime())
                    ->setMontant($event->data->object->amount_total / 100)
                    ->setStatut($event->data->object->status)
                    ->setFacture($facture)
                    ->setClient($facture->getClient());

            $facturesRepository->save($facture, true);
            $paiementsRepository->save($paiement, true);
        }


        return new Response();


    }

    /**
     * Retour d'un succès après paiement
     *
     * @param FacturesRepository $facturesRepository
     * @return void
     */
    #[Route('/success', name: 'app_paiement_success', methods:['GET'])]
    public function success(FacturesRepository $facturesRepository)
    {
        $factures = $facturesRepository->findBy(['client' => $this->getUser()]);

        $this->addFlash(
            'success',
            "Le paiement a été enregistré avec succès"
        );

        return $this->render('client/factures/index.html.twig', [
            'factures' => $factures,
        ]);
    }

    /**
     * Retour d'une erreur après paiement
     *
     * @param FacturesRepository $facturesRepository
     * @return void
     */
    #[Route('/error', name: 'app_paiement_error', methods:['GET'])]
    public function error(FacturesRepository $facturesRepository)
    {
        $factures = $facturesRepository->findBy(['client' => $this->getUser()]);

        $this->addFlash(
            'danger',
            "Il y a eu une erreur lors de votre paiement"
        );

        return $this->render('client/factures/index.html.twig', [
            'factures' => $factures,
        ]);
    }
    /**
     * Redirection vers stripe pour que le client effectue son paiement
     * 
     * @param Factures $facture
     * @param FacturesRepository $facturesRepository
     */
    #[Route('/{id}', name: 'app_paiement', methods:['GET'])]
    public function paie(Factures $facture, FacturesRepository $facturesRepository)
    {
        Stripe::setApiKey('sk_test_AEgwPLi1oCP4VrRgPjUoiUWL00bWphQrlb');

        


        $checkout_session = Session::create([
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'EUR',
                    'product_data' => [
                        'name' => $facture->getProjet()->getTitre()
                    ],
                    'unit_amount' => $facture->getAmount() * 100
                ]
            ]],
            'mode' => 'payment',
            'success_url' => 'http://127.0.0.1:8000/client/paiement/success',
            'cancel_url' => 'http://127.0.0.1:8000/client/paiement/error',
        ]);

        $facture->setPaiementIntent($checkout_session->payment_intent);
        $facturesRepository->save($facture, true);
        //dd($facture);


        return $this->redirect($checkout_session->url);

        //dd($checkout_session);
    }

}