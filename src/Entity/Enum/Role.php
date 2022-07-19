<?php

namespace App\Entity\Enum;

enum Role: string
{
case Student = "Etudiant";
case Instructor = "Formateur";
case Admin = "Admin";
}
