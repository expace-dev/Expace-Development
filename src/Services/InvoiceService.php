<?php

namespace App\Services;

use Konekt\PdfInvoice\InvoicePrinter;

class InvoiceService {

    public function CreateDevis($type = '', $document = '', $numero = '', $url = '') {
        
        $invoice = new InvoicePrinter('A4', '€', 'fr');
        $invoice->setNumberFormat(',', ' ', 'right', true, false);
        $invoice->hideToFromHeaders();
        if ($type === 'DEVIS') {
            $invoice->changeLanguageTerm('number', 'Devis N°');
            $invoice->changeLanguageTerm('date', 'Date du devis');
            $invoice->changeLanguageTerm('due', 'Date de validité');
        }
        else {
            $invoice->changeLanguageTerm('number', 'Facture N°');
            $invoice->changeLanguageTerm('date', 'Date de facturation');
            $invoice->changeLanguageTerm('due', 'Délai de paiement');
        }

        
        
        $invoice->changeLanguageTerm('product', 'Service');
        $invoice->changeLanguageTerm('qty', 'Qte');
        $invoice->changeLanguageTerm('price', 'PU HT');

        /* Header settings */
        $invoice->setLogo("http://127.0.0.1:8000/images/logo_doc.png");
        $invoice->setColor("#0083c3");      // pdf color scheme
        $invoice->setType($type);    // Invoice Type
        $invoice->setReference($numero);   // Reference
        $invoice->setDate(date('d m Y',time()));   //Billing Date
        $invoice->setDue(date('d m Y',strtotime('+15 days')));    // Due Date
        $invoice->setFrom([
            "Expace Development",
            "Mr HUSSON Frédéric",
            "121 Rue Maurice Burrus",
            "68160 Ste Croix Aux Mines",
            "Téléphone: 07 54 53 55 58",
            "E-mail: contact@expace-development.fr",
            "Site web: https://www.expace-development.fr",
            "RCS: Colmar TI 482 176 740",
            "Siret: 48217674000029",
            "APE: 722C"
        ]);
        if ($document->getProjet()->getClient()->getSociete()) {
            $invoice->setTo([
                strtoupper($document->getProjet()->getClient()->getSociete()),
                $document->getProjet()->getClient()->getNom(). ' ' .$document->getProjet()->getClient()->getPrenom(),
                $document->getProjet()->getClient()->getAdresse(),
                $document->getProjet()->getClient()->getCodePostal(). ' ' .$document->getProjet()->getClient()->getVille(),
                strtoupper($document->getProjet()->getClient()->getPays()),
            ]);
        }
        else {
            $invoice->setTo([
                strtoupper($document->getProjet()->getClient()->getNom(). ' ' .$document->getProjet()->getClient()->getPrenom()),
                $document->getProjet()->getClient()->getAdresse(),
                $document->getProjet()->getClient()->getCodePostal(). ' ' .$document->getProjet()->getClient()->getVille(),
                strtoupper($document->getProjet()->getClient()->getPays()),
            ]);
        }
            
        $tarif_total = null;
        foreach($document->getServices() as $values) {
                 
                
            $invoice->addItem(
                $values['type'], 
                false, 
                $values['quantite'], 
                false, 
                $values['tarif'], 
                false, 
                $values['tarif']*$values['quantite'],
            );

        
           
           $tarif_total += $values['tarif']*$values['quantite'];
        }

        if ($type === 'DEVIS')  {
            $invoice->addTotal("Accompte", $tarif_total*25/100);
        }
        
        $invoice->addTotal("Total", $tarif_total, true);
            
            
        $invoice->addTitle("Informations");
        $invoice->addParagraph("TVA non applicable art. 293B du CGI");

        if ($type === 'DEVIS') {
            $invoice->addParagraph("\n\n\nSignature du client (Précédé de la mention \"Bon pour accord\")");
            $invoice->addParagraph("Signature électronique en date du 14 janvier 2023");
            
        }
        
            
        $invoice->setFooternote("Expace Development");
        
        $invoice->render($url,'F');
    }
}