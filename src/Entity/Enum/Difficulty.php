<?php

namespace App\Entity\Enum;

enum Difficulty: int
{
    case Easy = 1;
    case Medium = 2;
    case Difficult = 3;
}