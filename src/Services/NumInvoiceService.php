<?php

namespace App\Services;

class NumInvoiceService {

    public function Generate($numInvoice = '', $type = '') {

        if ($type === 'DEVIS') {
            $titre = 'DEV';
        }
        else {
            $titre = 'FACT';
        }


        if (strlen($numInvoice) == 1) {
            $numero = $titre. '00000000' .$numInvoice;
        }
        elseif (strlen($numInvoice) == 2) {
            $numero = $titre. '0000000' .$numInvoice;
        }
        elseif (strlen($numInvoice) == 3) {
            $numero = $titre. '000000' .$numInvoice;
        }
        elseif (strlen($numInvoice) == 4) {
            $numero = $titre. '00000' .$numInvoice;
        }
        elseif (strlen($numInvoice) == 5) {
            $numero = $titre. '0000' .$numInvoice;
        }
        elseif (strlen($numInvoice) == 6) {
            $numero = $titre. '000' .$numInvoice;
        }
        elseif (strlen($numInvoice) == 7) {
            $numero = $titre. '00' .$numInvoice;
        }
        elseif (strlen($numInvoice) == 8) {
            $numero = $titre. '0' .$numInvoice;
        }
        elseif (strlen($numInvoice) == 9) {
            $numero = $titre .$numInvoice;
        }
        return $numero;
    }



    
}