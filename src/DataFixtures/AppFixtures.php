<?php

namespace App\DataFixtures;

use App\Entity\Enum\Difficulty;
use App\Entity\Enum\Level;
use App\Entity\Instructor;
use App\Entity\LinkInstructorSessionModule;
use App\Entity\LinkSessionModule;
use App\Entity\LinkSessionStudent;
use App\Entity\Module;
use App\Entity\Proposal;
use App\Entity\Qcm;
use App\Entity\QcmInstance;
use App\Entity\Question;
use App\Entity\Result;
use App\Entity\Session;
use App\Entity\Student;
use App\Repository\InstructorRepository;
use App\Repository\ModuleRepository;
use App\Repository\QcmInstanceRepository;
use App\Repository\QcmRepository;
use App\Repository\QuestionRepository;
use App\Repository\SessionRepository;
use App\Repository\StudentRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $faker;

    public function __construct (
        private UserPasswordHasherInterface $userPasswordHasherInterface,
        private InstructorRepository $instructorRepository,
        private ModuleRepository $moduleRepository,
        private SessionRepository $sessionRepository,
        private QuestionRepository $questionRepository,
        private QcmRepository $qcmRepository,
        private StudentRepository $studentRepository,
        private QcmInstanceRepository $qcmInstanceRepository
    )
    {
        $this->faker = Faker\Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        //Module
//        $this->generateModules( $manager );

        //Session
//        $this->generateSessions( $manager );

        //LinkSessionModule
//        $this->generateLinksSessionModule( $manager );

        //Instructeur
//        $this->generateInstructors( $manager );

        //Student
//        $this->generateStudents( $manager );

        //Question + Proposal
//        $this->generateQuestions( $manager );

        //Qcm
//        $this->generateQcm( $manager );

        //Qcm avec le module de démo (réelles data)
//        $this->generateQcmWithSpecifyModule($manager);

        // QcmInstances
//        $this->generateQcmInstances( $manager );

        //QcmInstance avec le module de démo (réelles data)
//        $this->generateQcmInstancesWithSpecifyModule($manager);

        // Results
//        $this->generateResults( $manager );

//        $this->generateJson();
    }

    public function generateModules( $manager ) :void
    {
        for ($i=0; $i < 10; $i++)
        {
            $module = new Module();

            $module->setTitle( $this->faker->word() );
            $module->setWeeks( $this->faker->numberBetween(1,10) );

            $manager->persist($module);
        }
        $manager->flush();
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
        }
        $manager->flush();
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
        }
        $manager->flush();
    }

    public function generateInstructors( $manager ) :void
    {
        $dbModules  = $this->moduleRepository->findAll();
        $dbSessions = $this->sessionRepository->findAll();

        for ($i=0; $i<10; $i++)
        {
            $instructor = new Instructor();

            $instructorFirstName = $this->faker->firstName();
            $instructor->setFirstname($instructorFirstName);
            $instructorLastName = $this->faker->lastName();
            $instructor->setLastname($instructorLastName);
            $instructor->setBirthDate( $this->faker->dateTimeBetween('-40 years', '-18 years') );
            $instructor->setPhone($this->faker->numerify('+33########'));
            $instructor->setEmail($this->faker->email());
            $instructor->setPassword(
                $this->userPasswordHasherInterface->hashPassword(
                    $instructor, "password"
                )
            );
            $instructor->setMoodleId($this->faker->randomNumber(5, true));
            $instructor->setIsReferent($this->faker->numberBetween(0, 1));
            $instructor->setRoles(['ROLE_INSTRUCTOR']);
            $instructor->setEmail3wa($instructorFirstName . '.' . $instructorLastName . '@3wa.io');
            $manager->persist($instructor);

            $linkInstructorSessionModule = new linkInstructorSessionModule();
            $linkInstructorSessionModule->setModule($dbModules[array_rand($dbModules)]);
            $linkInstructorSessionModule->setSession($dbSessions[array_rand($dbSessions)]);
            $linkInstructorSessionModule->setInstructor($instructor);
            $manager->persist($linkInstructorSessionModule);
        }
        $manager->flush();
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
                array_rand($dbModules,1) => "Découvre",
                array_rand($dbModules,1) => "Explore",
                array_rand($dbModules,1) => "Domine",
            ]);
            $student->setEmail3wa($studentFirstName . '.' . $studentLastName . '@3wa.io');
            $student->setEmail($studentFirstName . '.' . $studentLastName . '@yahoo.fr');
            $student->setMoodleId( $this->faker->randomNumber(5, true) );
            $student->setPassword(
                $this->userPasswordHasherInterface->hashPassword(
                    $student, "password"
                )
            );
            $student->setRoles(['ROLE_STUDENT']);
            $manager->persist($student);
            $manager->flush();

            //LinkSessionStudent
            $lms = new linkSessionStudent();
            $lms->setIsEnabled( $this->faker->numberBetween(0, 1) );
            $lms->setStudent($student);
            $lms->setSession($dbSessions[array_rand($dbSessions)]);

            $manager->persist($lms);
        }
        $manager->flush();
    }

    public function generateQuestions( $manager ) :void
    {
        $dbModules  = $this->moduleRepository->findAll();
        $dbInstructors = $this->instructorRepository->findAll();

        // 10 questions par module
        foreach ($dbModules as $dbModule){
            for ($i=0; $i<10; $i++)
            {
                $question = new Question();
                $question->setModule($dbModule);
                $question->setWording( $this->faker->sentence() );
                $question->setAuthor( $dbInstructors[array_rand($dbInstructors)] );
                $question->setIsEnabled( $this->faker->numberBetween(0, 1) );
                $question->setIsMandatory(0);
                $question->setIsOfficial(0);
                $count = $this->generateProposals($manager, $question);
                $question->setDifficulty($this->faker->numberBetween(1, 3));
                $question->setExplanation($this->faker->paragraph());

                if($count > 1){
                    $question->setIsMultiple(true);

                }else{
                    $question->setIsMultiple(false);
                }

                $manager->persist($question);
            }
        }
        $manager->flush();
    }

    public function generateProposals( $manager, $question )
    {
        $count=0;
        for ($i=0; $i<rand(2,6); $i++)
        {
            $proposal = new Proposal();

            $proposal->setQuestion($question);
            $proposal->setWording( $this->faker->word() );
            $isCorrect = $this->faker->numberBetween(0, 1);
            $proposal->setIsCorrectAnswer( $isCorrect );
            if( $isCorrect )
            {
                $count ++;
            }
            $manager->persist($proposal);
        }
            return $count;
    }

    public function generateQcm( $manager ) :void
    {
        $dbModules = $this->moduleRepository->findAll();

        $qcm = new Qcm();

        $qcm->setIsEnabled('1');
        $relatedModule = $dbModules[array_rand($dbModules)];
        $qcm->setModule($relatedModule);
        $qcm->setIsOfficial( $this->faker->numberBetween(0, 1) );
        $qcm->setTitle( $this->faker->word() );
        $instructors = [];
        foreach ($relatedModule->getLinksInstructorSessionModule() as $test){
            $instructors[] = $test->getInstructor();
        }
        $qcm->setAuthor($instructors[array_rand($instructors)]);
        $qcm->setIsPublic( $this->faker->numberBetween(0, 1) );

        $arrayQuestionAnswers = [];
        $arrayDifficulty=[];
        $questions = [];
        while(count($questions) == 0){
            $questions = $this->questionRepository->findBy( [ 'module' => $relatedModule->getId() ] );
        }

        $pickedQuestions = [];
        for( $i = 0; $i < 5; $i++ )
        {
            $isInArray = true;
            $randomQuestion = $questions[array_rand($questions)];
            while ($isInArray){
                if(in_array($randomQuestion->getId(), $pickedQuestions)){
                    $randomQuestion = $questions[array_rand($questions)];
                }else{
                    $isInArray = false;
                    $pickedQuestions[] = $randomQuestion->getId();
                }
            }

            $qcm->addQuestion($randomQuestion);
            $answers = $randomQuestion->getProposals();
            $difficulty = $randomQuestion->getDifficulty();
            $arrayDifficulty[] = $difficulty;
            $arrayAnswers = [];

            foreach ($answers as $answer)
            {
                $arrayAnswers[] =  [
                    "id"                => $answer->getId(),
                    "wording"           => $answer->getWording(),
                    "isCorrectAnswer"   => $answer->getIsCorrectAnswer()
                ];
            }
            $arrayQuestionAnswers[] = [
                "id"                => $randomQuestion->getId(),
                "wording"           => $randomQuestion->getWording(),
                "isMultiple"        => $randomQuestion->getIsMultiple(),
                "proposals"         => $arrayAnswers
            ];
        }
        $qcm->setQuestionsCache($arrayQuestionAnswers);
        $nrbValues = array_count_values($arrayDifficulty);
        $valueKey = array_search(max($nrbValues),$nrbValues);
        switch ($valueKey){
                case 1:
                    $qcm->setDifficulty(Difficulty::Easy->value);
                    break;
                case 2:
                    $qcm->setDifficulty(Difficulty::Medium->value);
                    break;
                case 3:
                    $qcm->setDifficulty(Difficulty::Difficult->value);
                    break;
            }
        $manager->persist($qcm);
        $manager->flush();
    }

    public function generateQcmWithSpecifyModule($manager) :void
    {
        $dbModule = $this->moduleRepository->find(11);
        $dbInstructors = $dbModule->getInstructors();
        $key = $dbModule->getInstructors()->getKeys();

        $qcm = new Qcm();

        $qcm->setEnabled('1');
        $qcm->setModule($dbModule);
        $qcm->setIsOfficial( $this->faker->numberBetween(0, 1) );
        $qcm->setName( $this->faker->word() );
        $qcm->setAuthorId( $dbInstructors[array_rand($key)]->getId() );
        $qcm->setPublic( $this->faker->numberBetween(0, 1) );

        $arrayQuestionAnswers = [];
        $arrayDifficulty=[];
        $questions = [];
        while(count($questions) == 0){
            $questions = $this->questionRepository->findBy( [ 'module' => $dbModule->getId() ] );
        }

        $pickedQuestions = [];
        for( $i = 0; $i < 5; $i++ )
        {
            $isInArray = true;
            $randomQuestion = $questions[array_rand($questions)];

            while ($isInArray){
                if(in_array($randomQuestion->getId(), $pickedQuestions)){
                    $randomQuestion = $questions[array_rand($questions)];
                }else{
                    $isInArray = false;
                    $pickedQuestions[] = $randomQuestion->getId();
                }
            }
            $qcm->addQuestion($randomQuestion);
            $answers = $randomQuestion->getProposals();
            $difficulty = $randomQuestion->getDifficulty()->value;
            array_push($arrayDifficulty, $difficulty);
            $arrayAnswers = [];
            foreach ($answers as $answer){
                array_push($arrayAnswers, ["id" => $answer->getId(), "libelle" => $answer->getWording(), "is_correct" => $answer->getIsCorrect()]);
            }

            $questionAnswer =
                [
                    [
                        "question"=>
                            [
                                "id"=> $randomQuestion->getId(),
                                "libelle"=>$randomQuestion->getWording(),
                                "responce_type"=>$randomQuestion->getResponseType(),
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

    public function generateQcmInstances( $manager ) :void
    {
        $dbQcms = $this->qcmRepository->findAll();
        $dbStudents = $this->studentRepository->findByEnabled();
        for( $i = 0; $i < 10; $i++ )
        {
            $relatedQcm = $dbQcms[array_rand($dbQcms)];

            $qcmInstance = new QcmInstance();
            $qcmInstance->setQcm( $relatedQcm );
            $qcmInstance->setStartTime( $this->faker->dateTimeBetween('-1 year', 'now') );
            $qcmInstance->setEndTime( $this->faker->dateTimeBetween('now', '+1 month') );
            $qcmInstance->setStudent($dbStudents[array_rand($dbStudents,1)]);
            $manager->persist($qcmInstance);
        }
        $manager->flush();
    }

    public function generateQcmInstancesWithSpecifyModule( $manager ) :void
    {
        $dbQcms = $this->qcmRepository->findBy(['module' => 11]);
//        dd($dbQcm);
        $dbStudents = $this->studentRepository->findByEnabled();
        for( $i = 0; $i < 3; $i++ )
        {
            $relatedQcm = $dbQcms[array_rand($dbQcms)];

            $qcmInstance = new QcmInstance();
            $qcmInstance->setQcm( $relatedQcm );
            $qcmInstance->setStartTime( $this->faker->dateTimeBetween('-1 year', 'now') );
            $qcmInstance->setEndTime( $this->faker->dateTimeBetween('now', '+1 month') );
            $qcmInstance->setStudent($dbStudents[array_rand($dbStudents,1)]);
            $manager->persist($qcmInstance);
        }
        $manager->flush();
    }

    public function generateResults( $manager ) :void
    {
        $dbQcmInstances = $this->qcmInstanceRepository->findAll();

        $dbQcmInstanceUsed = [];

        for( $i = 0; $i < 5; $i++ )
        {
            $result = new Result();

            $dbQcmInstanceWithoutResult = $this->qcmInstanceRepository->AllQcmInstanceWithoutResult();
            $dbQcmInstance = $dbQcmInstanceWithoutResult[array_rand($dbQcmInstanceWithoutResult)];

            $dbQcmInstanceUsed[] = $dbQcmInstance->getId();

            $result->setQcmInstance( $dbQcmInstance );
            $score = $this->faker->numberBetween(0,100);
            $result->setIsFirstTry(rand(0,1));
            $result->setScore( $score );

            if( $score < 25 )
            {
                $result->setLevel(Level::Discover->value);
            }
            elseif( $score >= 25 && $score < 50 )
            {
                $result->setLevel(Level::Explore->value);
            }
            elseif( $score >= 50 && $score < 75 )
            {
                $result->setLevel(Level::Master->value);
            }
            elseif( $score >= 75 && $score <= 100 )
            {
                $result->setLevel(Level::Dominate->value);
            }

            $result->setInstructorComment( $this->faker->sentence() );
            $result->setStudentComment( $this->faker->sentence() );

            $questionAnswers = $dbQcmInstance->getQcm()->getQuestionsCache();
            $resultAnswers = [];
            foreach($questionAnswers as $questionAnswer)
            {
                $proposalDetails = [];
                foreach( $questionAnswer['proposals'] as $proposal )
                {
                    $proposalDetails[] = [
                        'id'              => $proposal['id'],
                        'isStudentAnswer' => rand(0,1),
                        'isCorrectAnswer' => $proposal['isCorrectAnswer']
                    ];
                }
                $resultAnswers[] = [
                    "question"   => [
                        'id'        => $questionAnswer['id'],
                        'answers'   => $proposalDetails
                    ],
                    "totalScore" => $score
                ];
            }

            $result->setAnswers($resultAnswers);

            $manager->persist($result);
            $manager->flush();
        }
    }
}
