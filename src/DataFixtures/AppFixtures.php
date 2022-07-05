<?php

namespace App\DataFixtures;

use App\Entity\Instructor;
use App\Entity\Module;
use App\Entity\Session;
use App\Entity\Student;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct (UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $this->userPasswordHasherInterface = $userPasswordHasherInterface;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create();
        //Module
        for ($i=0; $i<10; $i++){

            $word = $faker->word();
            $module = new Module();
            $module->setTitle($word);
            $module->setNumberOfWeeks(rand(1,10));
            $manager->flush();
//            array_push($this->categories, $category);
        }

        //Session
        for ($i=0; $i<10; $i++){

            $word = $faker->word();
            $session = new Session();
            $session->setName($word);
            $session->setSchoolYear(2021);
            $manager->flush();
//            array_push($this->categories, $category);
        }

        //Instructeur
        for ($i=0; $i<10; $i++){
            $name = $faker->text();
            $arrayName = explode(' ', $name);
            $instructor = new Instructor();

            $instructor->setFirstname($arrayName[0]);
            $instructor->setLastname($arrayName[1]);
            $instructor->setEmail($faker->email());
            $instructor->setPhoneNumber('06xxxxxxxxxx');
            $instructor->setBirthDate(new \DateTime("22/05/1957"));

            $instructor->setPassword(
                $this->userPasswordHasherInterface->hashPassword(
                    $instructor, "password"
                )
            );

            $manager->flush();
//            array_push($this->categories, $category);
        }

        //Student
        for ($i=0; $i<50; $i++){
            $name = $faker->text();
            $arrayName = explode(' ', $name);
            $instructor = new Student();

            $instructor->setFirstname($arrayName[0]);
            $instructor->setLastname($arrayName[1]);
            $instructor->setIdModule('12345');
            $instructor->setMail3wa('xxxxx@3wa.io');
            $instructor->setBirthDate(new \DateTime("22/05/1997"));

            $instructor->setBadges([

            ]);

            $manager->flush();
//            array_push($this->categories, $category);
        }





    }
}
