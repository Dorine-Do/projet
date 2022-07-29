<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigFunctionExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('explode', [$this, 'explode']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('numberOfWord', [$this, 'nOw']),
            new TwigFunction('explode', [$this, 'explode']),
            new TwigFunction('implode', [$this, 'implode']),
        ];
    }

    public function nOw($str)
    {
        // si i > 10 , i= 10-le nombre de mot
        
        return str_word_count($str,0);
      
    }
    public function explode($a)
    {
 
       $b=0;
        return explode(' ',$a,$b);
    }
    
    public function implode($b)
    {
        // ...
       return implode(' ',$b);
    }
    
}
