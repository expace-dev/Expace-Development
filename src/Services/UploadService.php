<?php

namespace App\Services;

use App\Repository\PortfoliosRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UploadService {

    public function __construct(private ParameterBagInterface $params, private PortfoliosRepository $portfoliosRepository)
    {
        
    }

    public function send($fichier, $directory) {
        
        $nom = md5(uniqid()) . '.' . $fichier->guessExtension();

        
        $fichier->move(
            $this->params->get($directory),
            $nom
        );
        
        return $nom;
    }
}