<?php

namespace App\DataFixtures;

use App\Entity\Enum\Difficulty;
use App\Entity\Instructor;
use App\Entity\LinkSessionModule;
use App\Entity\LinkSessionStudent;
use App\Entity\Module;
use App\Entity\Proposal;
use App\Entity\Qcm;
use App\Entity\Question;
use App\Entity\Session;
use App\Entity\Student;
use App\Repository\InstructorRepository;
use App\Repository\ModuleRepository;
use App\Repository\QuestionRepository;
use App\Repository\SessionRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasherInterface;
    private InstructorRepository $instructorRepository;
    private ModuleRepository $moduleRepository;
    private SessionRepository $sessionRepository;
    private QuestionRepository $questionRepository;


    public function __construct (
        UserPasswordHasherInterface $userPasswordHasherInterface,
        InstructorRepository $instructorRepository,
        ModuleRepository $moduleRepository,
        SessionRepository $sessionRepository,
        QuestionRepository $questionRepository

    )
    {
        $this->userPasswordHasherInterface = $userPasswordHasherInterface;
        $this->instructorRepository = $instructorRepository;
        $this->moduleRepository = $moduleRepository;
        $this->sessionRepository = $sessionRepository;
        $this->questionRepository = $questionRepository;
    }

    public function load(ObjectManager $manager): void
    {

        $arrayModule = [];
        $arraySession = [];
        $arrayStudent = [];
        $arrayInstructor = [];
        $ChoicesDifficulty = [ Difficulty::Easy, Difficulty::Medium, Difficulty::Difficult];

        $faker = Faker\Factory::create();
        //Module
        for ($i=0; $i<10; $i++)
        {
            $word = $faker->word();
            $module = new Module();
            $module->setTitle($word);
            $module->setNumberOfWeeks(rand(1,10));

            $manager->persist($module);
            $manager->flush();
            array_push($arrayModule, $module);
        }

        //Session
        for ($i=0; $i<10; $i++)
        {
            $word = $faker->word();
            $session = new Session();
            $session->setName($word);
            $session->setSchoolYear(2021);

            array_push($arraySession, $session);
            $manager->persist($session);
            $manager->flush();
        }

        //LinkSessionModule
        foreach( $arrayModule as $module )
        {
            $linkSessionModule = new LinkSessionModule();
            $linkSessionModule->setModule( $module );
            $linkSessionModule->setSession( $arraySession[array_rand($arraySession)] );
            $linkSessionModule->setStartDate( $faker->dateTimeBetween('-1 year', 'now') );
            $linkSessionModule->setEndDate( $faker->dateTimeBetween( 'now', '+1 month' ) );
            $manager->persist($linkSessionModule);
            $manager->flush();
        }

        //Instructeur
        for ($i=0; $i<10; $i++)
        {
            $name = $faker->name();
            $arrayName = explode(' ', $name);
            $instructor = new Instructor();

            $instructor->setFirstname($arrayName[0]);
            $instructor->setLastname($arrayName[1]);
            $instructor->setBirthDate(new \DateTime("22-05-1957"));
            $instructor->setPhoneNumber('06xxxxxxxxxx');
            $instructor->setEmail($faker->email());
            $instructor->setPassword(
                $this->userPasswordHasherInterface->hashPassword(
                    $instructor, "password"
                )
            );
            $instructor->setRoles(['instructor']);

            $instructorModule = $arrayModule[array_rand($arrayModule)];
            $instructor->addModule($instructorModule);

            // $sessionInstructor = $arraySession[array_rand($arraySession)];
            // $instructor->addSession($sessionInstructor);

            array_push($arrayInstructor, $instructor);
            $manager->persist($instructor);
            $manager->flush();
        }

        //Student
        for ($i=0; $i<20; $i++)
        {
            $name = $faker->name();
            $arrayName = explode(' ', $name);
            $student = new Student();

            $student->setFirstname($arrayName[0]);
            $student->setLastname($arrayName[1]);
            $student->setIdModule('12345');
            $student->setMail3wa('xxxxx@3wa.io');
            $student->setBirthDate(new \DateTime("22-05-1997"));

            $student->setBadges([
                array_rand($arrayModule,1) => "Découvre",
                array_rand($arrayModule,1) => "Explore",
                array_rand($arrayModule,1) => "Domine",
            ]);

            array_push($arrayStudent, $student);
            $manager->persist($student);
            $manager->flush();

            //LinkSessionStudent
            $lms = new linkSessionStudent();
            $lms->setEnabled(1);
            $lms->setStudent($student);
            $lms->setSession($arraySession[array_rand($arraySession)]);

            $manager->persist($lms);
            $manager->flush();
        }

        //Question
        for ($i=0; $i<20; $i++)
        {
            $text = $faker->sentence();
            $question = new Question();

            $question->setWording($text);
            $instructorEntity = $this->instructorRepository->findAll();
            $instructorEntity = $instructorEntity[array_rand($instructorEntity)];
            $instructorId = $instructorEntity->getId();
            $question->setIdAuthor($instructorId);

            $question->setEnabled(1);
            $question->setIsMandatory(0);
            $question->setIsOfficial(0);


            $difficulty = $ChoicesDifficulty[rand(0,2)];
            $question->setDifficulty($difficulty);

            // $instructorModules = $instructorEntity->getModules();
            $question->setModule($arrayModule[array_rand($arrayModule)]);
            $question->setResponseType('checkbox');

            $manager->persist($question);
            $manager->flush();
        }

        //Proposal
        $count=0;
        $length = rand(0,6);
        $questions = $this->questionRepository->findAll();

        for ($i=0; $i<20; $i++)
        {
            $word = $faker->word();
            $proposal = new Proposal();

            $proposal->setWording($word);
            $isCorrect = rand(0,1);
            $proposal->setIsCorrect($isCorrect);
            if($isCorrect){
                $count ++;
            }

            $proposal->setQuestion($questions[array_rand($questions)]);
            $manager->persist($proposal);
            $manager->flush();
        }

        //Qcm
        for ($i=0; $i<5; $i++)
        {
            $word = $faker->word();
            $qcm = new Qcm();
            $qcm->setEnabled('1');
            $modules = $this->moduleRepository->findAll();
            $module = $modules[array_rand($modules)];
//            $module = $this->moduleRepository->find(347);
            $qcm->addModule($module);
            $qcm->setIsOfficial('0');
            $qcm->setName($word);
            $qcm->setAuthorId($this->instructorRepository->findAll()[rand(0,9)]->getId());
            $qcm->setPublic("1");

            $arrayQuestionAnswers = [];
            $arrayDifficulty=[];
            $questions = [];
            while(count($questions) == 0){
                $questions = $this->questionRepository->findBy(array('module' => $module->getId()));
            }
//            $allQuestions = $this->questionRepository->findAll();
//            $questions = $allQuestions->array_filter(
//                function(Question $question, $module){
//                    return $question->getModule() == $module;
//                });

//            $questions = array_filter($allQuestions,function(Question $question) use($module){
//                return $question->getModule() === $module;
//            });

            for($i=0; $i<5; $i++){
                $question=$questions[array_rand($questions)];
                $qcm->addQuestion($question);
                $answers = $question->getProposals();
                $difficulty = $question->getDifficulty()->value;
                array_push($arrayDifficulty, $difficulty);
                $arrayAnswers = [];
                foreach ($answers as $answer){
                    array_push($arrayAnswers, ["id" => $answer->getWording(), "libelle" => $answer->getId()]);
                }
                $questionAnswer =
                    [
                        [
                        "question"=>
                            [
                            "id"=> $question->getId(),
                            "libelle"=>$question->getWording(),
                            "answers"=>
                                $arrayAnswers
                            ],
                        ]
                    ];
                $questionAnswerJson = json_encode($questionAnswer);
                array_push($arrayQuestionAnswers,$questionAnswerJson);
            }
            $qcm->setQuestionsAnswers($arrayQuestionAnswers);

            $nrbValues = array_count_values($arrayDifficulty);
            $valueKey = array_search(max($nrbValues),$nrbValues);
            switch ($valueKey){
                case "Facile":
                    $qcm->setDifficulty(Difficulty::Easy);
                    break;
                case "Moyen":
                    $qcm->setDifficulty(Difficulty::Medium);
                    break;
                case "Difficile":
                    $qcm->setDifficulty(Difficulty::Difficult);
                    break;
            }


            $manager->persist($qcm);
            $manager->flush();
        }
    }
}
