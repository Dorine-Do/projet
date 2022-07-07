<?php

namespace App\DataFixtures;

use App\Entity\Enum\Difficulty;
use App\Entity\Instructor;
use App\Entity\LinkSessionModule;
use App\Entity\LinkSessionStudent;
use App\Entity\Module;
use App\Entity\Proposal;
use App\Entity\Qcm;
use App\Entity\QcmInstance;
use App\Entity\Question;
use App\Entity\Session;
use App\Entity\Student;
use App\Repository\InstructorRepository;
use App\Repository\ModuleRepository;
use App\Repository\QcmRepository;
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
    private QcmRepository $qcmRepository;

    private $faker;

    protected array $arrayModule = [];
    protected array $arraySession = [];
    protected array $arrayStudent = [];
    protected array $arrayInstructor = [];
    protected array $ChoicesDifficulty = [ Difficulty::Easy, Difficulty::Medium, Difficulty::Difficult];

    public function __construct (
        UserPasswordHasherInterface $userPasswordHasherInterface,
        InstructorRepository $instructorRepository,
        ModuleRepository $moduleRepository,
        SessionRepository $sessionRepository,
        QuestionRepository $questionRepository,
        QcmRepository $qcmRepository
    )
    {
        $this->userPasswordHasherInterface = $userPasswordHasherInterface;
        $this->instructorRepository = $instructorRepository;
        $this->moduleRepository = $moduleRepository;
        $this->sessionRepository = $sessionRepository;
        $this->questionRepository = $questionRepository;
        $this->qcmRepository = $qcmRepository;
        $this->faker = Faker\Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        //Module
        $this->generateModules( $manager );

        //Session
        $this->generateSessions( $manager );

        //LinkSessionModule
        $this->generateLinksSessionModule( $manager );

        //Instructeur
        $this->generateInstructors( $manager );
        $this->generateLinkSessionInstructor();

        //Student
        $this->generateStudents( $manager );

        //Question
        $this->generateQuestions( $manager );

        //Proposal
        $this->generateProposals( $manager);

        //Qcm
        $this->generateQcms( $manager );

        // QcmInstances
        $this->generateQcmInstances( $manager );

        // Results
        $this->generateResults( $manager );
    }

    public function generateModules( $manager ) :void
    {
        for ($i=0; $i < 10; $i++)
        {
            $module = new Module();

            $module->setTitle( $this->faker->word() );
            $module->setNumberOfWeeks( $this->faker->numberBetween(1,10) );

            $manager->persist($module);
            $manager->flush();
        }
    }

    public function generateSessions( $manager ) :void
    {
        for ($i=0; $i<10; $i++)
        {
            $word = $this->faker->word();
            $session = new Session();
            $session->setName($word);
            $session->setSchoolYear( $this->faker->numberBetween(2019,2022) );
            $manager->persist($session);
            $manager->flush();
        }
    }

    public function generateLinksSessionModule( $manager ) :void
    {
        $dbModules  = $this->moduleRepository->findAll();
        $dbSessions = $this->sessionRepository->findAll();

        foreach( $dbModules as $module )
        {
            $linkSessionModule = new LinkSessionModule();
            $linkSessionModule->setModule( $module );
            $linkSessionModule->setSession( $dbSessions[array_rand($dbSessions)] );
            $linkSessionModule->setStartDate( $this->faker->dateTimeBetween('-1 year', 'now') );
            $linkSessionModule->setEndDate( $this->faker->dateTimeBetween( 'now', '+1 month' ) );
            $manager->persist($linkSessionModule);
            $manager->flush();
        }
    }

    public function generateInstructors( $manager ) :void
    {
        $dbModules  = $this->moduleRepository->findAll();
        $dbSessions = $this->sessionRepository->findAll();

        for ($i=0; $i<10; $i++)
        {
            $instructor = new Instructor();

            $instructor->setFirstname($this->faker->firstName());
            $instructor->setLastname($this->faker->lastName());
            $instructor->setBirthDate( $this->faker->dateTimeBetween('-40 years', '-18 years') );
            $instructor->setPhoneNumber($this->faker->numerify('06########'));
            $instructor->setEmail($this->faker->email());
            $instructor->setPassword(
                $this->userPasswordHasherInterface->hashPassword(
                    $instructor, "password"
                )
            );
            $instructor->setRoles(['instructor']);
            // $instructor->addModule($dbModules[array_rand($dbModules)]);
            // $session = $dbSessions[array_rand($dbSessions)];
            // $session->addInstructor($instructor);
            // $instructor->addSession($session);

            $manager->persist($instructor);
            $manager->flush();
        }
    }

    public function generateLinkSessionInstructor() :void
    {
        $dbSessions    = $this->sessionRepository->findAll();
        $dbInstructors = $this->instructorRepository->findAll();

        foreach( $dbSessions as $session)
        {
            $session->addInstructor( $dbInstructors[array_rand($dbInstructors)] );
        }
    }

    public function generateStudents( $manager ) :void
    {
        $dbModules  = $this->moduleRepository->findAll();
        $dbSessions = $this->sessionRepository->findAll();

        for ($i=0; $i<10; $i++)
        {
            $student = new Student();

            $studentFirstName = $this->faker->firstName();
            $studentLastName = $this->faker->lastName();

            $student->setFirstname( $studentFirstName );
            $student->setLastname( $studentLastName );
            $student->setBirthDate( $this->faker->dateTimeBetween('-40 years', '-18 years') );
            $student->setBadges([
                array_rand($this->arrayModule,1) => "Découvre",
                array_rand($this->arrayModule,1) => "Explore",
                array_rand($this->arrayModule,1) => "Domine",
            ]);
            $student->setMail3wa($studentFirstName . '.' . $studentLastName . '@3wa.io');
            $student->setIdModule( $dbModules[array_rand($dbModules )]->getId() );

            $manager->persist($student);
            $manager->flush();

            //LinkSessionStudent
            $lms = new linkSessionStudent();
            $lms->setEnabled( $this->faker->numberBetween(0, 1) );
            $lms->setStudent($student);
            $lms->setSession($dbSessions[array_rand($dbSessions)]);

            $manager->persist($lms);
            $manager->flush();
        }
    }

    public function generateQuestions( $manager ) :void
    {
        $dbModules  = $this->moduleRepository->findAll();
        $dbInstructors = $this->instructorRepository->findAll();

        for ($i=0; $i<10; $i++)
        {
            $question = new Question();

            $question->setWording( $this->faker->sentence() );
            $question->setIdAuthor( $dbInstructors[array_rand($dbInstructors)]->getId() );

            $question->setEnabled( $this->faker->numberBetween(0, 1) );
            $question->setIsMandatory(0);
            $question->setIsOfficial(0);

            $question->setDifficulty($this->ChoicesDifficulty[array_rand($this->ChoicesDifficulty)]);

            // $instructorModules = $instructorEntity->getModules();
            $question->setModule($dbModules[array_rand($dbModules)]);
            $question->setResponseType('checkbox');

            $manager->persist($question);
            $manager->flush();
        }
    }

    public function generateProposals( $manager ) :void
    {
        $count=0;
        $dbQuestions = $this->questionRepository->findAll();

        for ($i=0; $i<10; $i++)
        {
            $proposal = new Proposal();

            $proposal->setQuestion($dbQuestions[array_rand($dbQuestions)]);
            $proposal->setWording( $this->faker->word() );
            $isCorrect = $this->faker->numberBetween(0, 1);
            $proposal->setIsCorrect( $isCorrect );
            if( $isCorrect )
            {
                $count ++;
            }

            $manager->persist($proposal);
            $manager->flush();
        }
    }

    public function generateQcms( $manager ) :void
    {
        $dbModules = $this->moduleRepository->findAll();
        $dbInstructors = $this->instructorRepository->findAll();

        for( $i = 0; $i < 10; $i++ )
        {
            $qcm = new Qcm();

            $qcm->setEnabled('1');
            $relatedModule = $dbModules[array_rand($dbModules)];
            $qcm->addModule($relatedModule);
            $qcm->setIsOfficial( $this->faker->numberBetween(0, 1) );
            $qcm->setName( $this->faker->word() );
            $qcm->setAuthorId( $dbInstructors[array_rand($dbInstructors)]->getId() );
            $qcm->setPublic( $this->faker->numberBetween(0, 1) );

            $arrayQuestionAnswers = [];
            $arrayDifficulty=[];
            $questions = [];
            while(count($questions) == 0){
                $questions = $this->questionRepository->findBy( [ 'module' => $relatedModule->getId() ] );
            }

            for( $i = 0; $i < 5; $i++ )
            {
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

    public function generateQcmInstances( $manager ) :void
    {
        $dbQcms = $this->qcmRepository->findAll();

        for( $i = 0; $i < 10; $i++ )
        {
            $relatedQcm = $dbQcms[array_rand($dbQcms)];

            $qcmInstance = new QcmInstance();
            $qcmInstance->setQcm( $relatedQcm );
            $qcmInstance->setQuestionAnswers( $relatedQcm->getQuestionsAnswers() );
            $qcmInstance->setEnabled( $this->faker->numberBetween(0, 1) );
            $qcmInstance->setName( $relatedQcm->getName() );
            $qcmInstance->setReleaseDate( $this->faker->dateTimeBetween('-1 year', 'now') );
            $qcmInstance->setEndDate( $this->faker->dateTimeBetween('now', '+1 momth') );

            $manager->persist($qcmInstance);
            $manager->flush();
        }
    }

    public function generateResults( $manager ) :void
    {

    }
}
