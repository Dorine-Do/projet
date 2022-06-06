<?php

namespace App\Entity\Enum;

enum Level: string
{
    case Discover = "Découvre";
    case Explore = "Explore";
    case Master = "Maîtrise";
    case Dominate = "Domine";
}