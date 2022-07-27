<?php

namespace App\Entity\Enum;

enum Level: int
{
    case Discover = 1;
    case Explore = 2;
    case Master = 3;
    case Dominate = 4;
}