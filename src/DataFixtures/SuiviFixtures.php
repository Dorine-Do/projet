<?php

namespace App\DataFixtures;

use App\Entity\Enum\Difficulty;
use App\Entity\Enum\Level;
use App\Entity\Main\Instructor;
use App\Entity\Main\LinkInstructorSessionModule;
use App\Entity\Main\LinkSessionModule;
use App\Entity\Main\LinkSessionStudent;
use App\Entity\Main\Module;
use App\Entity\Main\Proposal;
use App\Entity\Main\Qcm;
use App\Entity\Main\QcmInstance;
use App\Entity\Main\Question;
use App\Entity\Main\Result;
use App\Entity\Main\Session;
use App\Entity\Main\Student;
use App\Repository\InstructorRepository;
use App\Repository\LinkInstructorSessionModuleRepository;
use App\Repository\LinkSessionModuleRepository;
use App\Repository\LinkSessionStudentRepository;
use App\Repository\ModuleRepository;
use App\Repository\QcmInstanceRepository;
use App\Repository\QcmRepository;
use App\Repository\QuestionRepository;
use App\Repository\SessionRepository;
use App\Repository\StudentRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SuiviFixtures extends Fixture
{
    private $faker;

    protected array $modules = [];
    protected array $sessions = [];
    protected array $linksSessionModule = [];
    protected array $students = [];
    protected array $instructors = [];
    protected array $linksSessionStudent = [];
    protected array $linksInstructorSessionModule = [];
    protected array $ChoicesDifficulty = [ Difficulty::Easy, Difficulty::Medium, Difficulty::Difficult];
    protected array $ChoicesLevel = [ Level::Discover, Level::Explore, Level::Master, Level::Dominate];

    public function __construct (
        private UserPasswordHasherInterface $userPasswordHasherInterface,
        private InstructorRepository $instructorRepository,
        private ModuleRepository $moduleRepository,
        private SessionRepository $sessionRepository,
        private QuestionRepository $questionRepository,
        private QcmRepository $qcmRepository,
        private StudentRepository $studentRepository,
        private QcmInstanceRepository $qcmInstanceRepository,
        private LinkSessionModuleRepository $linksSessionModuleRepository,
        private LinkSessionStudentRepository $linksSessionStudentRepository,
        private LinkInstructorSessionModuleRepository $linkInstructorSessionModuleRepository,
        private \Doctrine\Persistence\ManagerRegistry $doctrine
    )
    {
        $this->faker = Faker\Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $this->initSuiviData();

        //Sessions
//        $this->generateSessions( $manager );

        //Modules
//        $this->generateModules( $manager );

        //LinksSessionModule
//        $this->generateLinksSessionModule( $manager );

        //Instructors
//        $this->generateInstructors( $manager );

        //Students
//        $this->generateStudents( $manager );

        // LinksSessionStudent
//        $this->generateLinksSessionStudent( $manager );

        // LinksInstructorSessionModule
        // $this->generateLinkInstructorSessionModule( $manager );

        //Question + Proposal
//        $this->generateQuestions( $manager );

        //Qcm
        // $this->generateQcm( $manager );

        //Qcm avec le module de démo (réelles data)
        // $this->generateQcmWithSpecifyModule($manager);

        // QcmInstances
        // $this->generateQcmInstances( $manager );

        //QcmInstance avec le module de démo (réelles data)
        // $this->generateQcmInstancesWithSpecifyModule($manager);

        // Results
        // $this->generateResults( $manager );
    }

    // HANDLE SUIVI DATA FUNCTIONS -------------------------------------------------------------------------------------
    public function getDataFromSuivi( $sql, $params = [] )
    {
        $conn = $this->doctrine->getConnection('dbsuivi');
        return $conn
            ->prepare($sql)
            ->executeQuery($params)
            ->fetchAll();
    }

    public function initSuiviData()
    {
        $this->sessions = $this->getSuiviSessions();
        $this->modules = $this->getSuiviModules($this->sessions);
        $this->linksSessionModule = $this->getSuiviLinksSessionModule();
        $this->instructors = $this->getSuiviInstructors();
        $this->students = $this->getSuiviStudents();
        $this->linksSessionStudent = $this->getSuiviLinksSessionStudent($this->sessions);
        $this->linksInstructorSessionModule = $this->getSuiviLinksInstructorSessionModule();
    }

    public function getSuiviSessions()
    {
        $suiviSessions = $this->getDataFromSuivi( 'SELECT * FROM sessions WHERE id < 4' );
        $sessions = [];
        foreach ($suiviSessions as $suiviSession)
        {
            $params = [
                'sessionid' => $suiviSession['id']
            ];
            $sessionStart = $this->getDataFromSuivi( 'SELECT DISTINCT MIN(date) as startDate FROM daily WHERE id_session = :sessionid GROUP BY id_session', $params );
            $explodedStartDate = explode('-', $sessionStart[0]['startDate']);
            $sessions[] = [
                'id' => $suiviSession['id'],
                'name' => $suiviSession['name'],
                'school_year' => $explodedStartDate[0],
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ];
        }
        return $sessions;
    }

    public function getSuiviModules( $sessions )
    {
        $moduleByName = [];
        foreach ($sessions as $session)
        {
            $params = [
                'id' => $session["id"]
            ];
            $modulesSql = "SELECT DISTINCT
                sessions.name as session_name,
                sessions.id as session_id,
                modules.name as module_name,
                modules.id as module_id,
                MIN(date) as start_date,
                MAX(date) as end_date,
                COUNT(modules.id) as duration
                FROM users
                LEFT JOIN daily ON daily.id_user = users.id
                LEFT JOIN modules ON modules.id = daily.id_module
                LEFT JOIN categories ON categories.id = modules.id_category
                LEFT JOIN sessions ON sessions.id = daily.id_session
                WHERE sessions.id = :id
                GROUP BY modules.id";

            $suiviModules = $this->getDataFromSuivi($modulesSql, $params);
            $moduleByName = [];
            foreach($suiviModules as $suiviModule)
            {
                $explodedModuleName = explode('.', $suiviModule['module_name']);
                $moduleName = $explodedModuleName[0];
                if( !array_key_exists($moduleName, $moduleByName) )
                {
                    $moduleByName[$moduleName] = [];
                }
                $moduleByName[$moduleName][] = $suiviModule;
            }
        }

        $modules = [];
        foreach( $moduleByName as $name => $submodules )
        {
            $moduleDuration = 0;
            foreach( $submodules as $submodule  )
            {
                $moduleDuration += $submodule['duration'];
            }

            $weeks = ceil( $moduleDuration / 5 );

            $modules[] = [
                'name' => $name,
                'weeks' => $weeks,
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ];
        }
        return $modules;
    }

    public function getSuiviInstructors()
    {
        $params = [
            'maxIdSession' => 4
        ];

        $suiviInstructors = $this->getDataFromSuivi( 'SELECT DISTINCT
                users.*
                FROM users
                LEFT JOIN daily ON daily.id_user = users.id
                LEFT JOIN modules ON modules.id = daily.id_module
                LEFT JOIN categories ON categories.id = modules.id_category
                LEFT JOIN sessions ON sessions.id = daily.id_session
                WHERE sessions.id < :maxIdSession', $params );

        $instructors = [];
        foreach($suiviInstructors as $suiviInstructor)
        {
            $instructors[] = [
                'id' => $suiviInstructor['id'],
                'email' => $suiviInstructor['email'],
                'roles' => ['ROLE_INSTRUCTOR'],
                'password' => 'password',
                'first_name' => $suiviInstructor['firstname'],
                'last_name' => $suiviInstructor['lastname'],
                'birthdate' => null,
                'email3wa' => str_replace( ' ', '',strtolower($suiviInstructor['firstname'])) . '.' . str_replace(' ', '', strtolower($suiviInstructor['lastname'])) . '@3wa.io',
                'id_google' => null,
                'id_moodle' => $suiviInstructor['id_moodle'],
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
                'discr' => 'instructor',
                'phone' => $suiviInstructor['phone'],
                'isReferent' => 0,
                'badges' => null,
            ];
        }
        return $instructors;
    }

    public function getSuiviStudents()
    {
        $params = [
            'maxIdSession' => 4
        ];

        $suiviStudents = $this->getDataFromSuivi( 'SELECT DISTINCT
                users.*
                FROM users
                LEFT JOIN link_students_daily ON link_students_daily.id_student = users.id
                LEFT JOIN daily ON daily.id = link_students_daily.id_daily
                LEFT JOIN modules ON modules.id = daily.id_module
                LEFT JOIN categories ON categories.id = modules.id_category
                LEFT JOIN sessions ON sessions.id = daily.id_session
                WHERE sessions.id  < :maxIdSession', $params );

        $students = [];
        foreach($suiviStudents as $suiviStudent)
        {
            $students[] = [
                'id' => $suiviStudent['id'],
                'email' => $suiviStudent['email'],
                'roles' => ['ROLE_INSTRUCTOR'],
                'password' => 'password',
                'first_name' => $suiviStudent['firstname'],
                'last_name' => $suiviStudent['lastname'],
                'birthdate' => null,
                'email3wa' => $suiviStudent['email'],
                'id_google' => null,
                'id_moodle' => $suiviStudent['id_moodle'],
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
                'discr' => 'instructor',
                'phone' => $suiviStudent['phone'],
                'isReferent' => 0,
                'badges' => null,
            ];
        }
        return $students;
    }

    public function getSuiviLinksSessionStudent( $sessions )
    {
        $studentsBySession = $this->getDataFromSuivi( 'SELECT DISTINCT
                users.id as student_id,
                sessions.id as session_id
                FROM users
                LEFT JOIN link_students_daily ON link_students_daily.id_student = users.id
                LEFT JOIN daily ON daily.id = link_students_daily.id_daily
                LEFT JOIN sessions ON sessions.id = daily.id_session
                WHERE sessions.id <= 4' );
        $linksSessionStudent = [];
        foreach( $sessions as $session )
        {
            $param = [
                'id' => $session['id']
            ];
            $sessionEnd = $this->getDataFromSuivi( 'SELECT DISTINCT
                sessions.id as session_id,
                MAX(DATE) as session_end
                FROM users
                LEFT JOIN link_students_daily ON link_students_daily.id_student = users.id
                LEFT JOIN daily ON daily.id = link_students_daily.id_daily
                LEFT JOIN sessions ON sessions.id = daily.id_session
                WHERE sessions.id = :id GROUP BY sessions.id', $param );
            $now = new \DateTime();
            $endDate = new \DateTime( $sessionEnd[0]['session_end'] );

            foreach ( $studentsBySession as $studentBySession )
            {
                if( $studentBySession['session_id'] === $session['id'] )
                {
                    $linksSessionStudent[] = [
                        'session_id' => $session['id'],
                        'student_id' => $studentBySession['student_id'],
                        'is_enabled' => $now < $endDate ? 1 : 0,
                    ];
                }
            }
            return $linksSessionStudent;
        }
    }

    public function getSuiviLinksInstructorSessionModule()
    {
        $instructorsAndSessionByModule = $this->getDataFromSuivi('SELECT DISTINCT
                users.id as instructor_id,
                modules.id as module_id,
                sessions.id as session_id,
                modules.name as module_name
                FROM users
                LEFT JOIN daily ON daily.id_user = users.id
                LEFT JOIN modules ON modules.id = daily.id_module
                LEFT JOIN categories ON categories.id = modules.id_category
                LEFT JOIN sessions ON sessions.id = daily.id_session
                WHERE sessions.id <= 3');

        $instructorsAndSessionByModule = array_map(function($item){

            $explodedName = explode('.', $item['module_name']);
            $moduleName = $explodedName[0];

            return [
                'instructor_id' => $item['instructor_id'],
                'session_id' => $item['session_id'],
                'module_id' => $item['module_id'],
                'module_name' => $moduleName,
            ];
        }, $instructorsAndSessionByModule);

        $linksInstructorSessionModule = [];

        foreach ( $instructorsAndSessionByModule as $ism )
        {
            if( !in_array( $ism, $linksInstructorSessionModule ) )
            {
                $linksInstructorSessionModule[$ism['instructor_id'].$ism['session_id'].$ism['module_name']] = $ism;
            }
        }

        $linksInstructorSessionModule = array_map( function($ism) {
            foreach( $this->modules as $key => $module )
            {
                if( $module['name'] === $ism['module_name'] )
                {
                    return [
                        'instructor_id' => $ism['instructor_id'],
                        'session_id' => $ism['session_id'],
                        'module_id' => $key + 1,
                        'module_name' => $module['name']
                    ];
                }
            }
        }, $linksInstructorSessionModule);

        return $linksInstructorSessionModule;
    }

    public function getSuiviLinksSessionModule()
    {
        $linksSessionModule = [];

        foreach( $this->sessions as $session )
        {
            foreach( $this->modules as $key => $module )
            {
                if( $module['name'] === 'INTRO' )
                {
                    $startModuleForSession = $this->getDataFromSuivi('SELECT DISTINCT
                    MIN(date) as startdate
                    FROM daily
                    INNER JOIN modules ON modules.id = daily.id_module
                    WHERE id_session = :id AND modules.name LIKE :modulename',
                        [
                            'id' => $session['id'],
                            'modulename' => 'INTRO'
                        ])[0]['startdate'];

                    $endModuleForSession = $this->getDataFromSuivi( 'SELECT DISTINCT
                    MAX(date) as enddate
                    FROM daily
                    INNER JOIN modules ON modules.id = daily.id_module
                    WHERE id_session = :id AND modules.name LIKE :modulename',
                        [
                            'id' => $session['id'],
                            'modulename' => 'INTRO'
                        ])[0]['enddate'];

                    $module_name = $module['name'];
                }
                elseif( strpos( $module['name'], 'DATA' ) !== false  )
                {

                    $startModuleForSession = $this->getDataFromSuivi('SELECT DISTINCT
                    MIN(date) as startdate
                    FROM daily
                    INNER JOIN modules ON modules.id = daily.id_module
                    WHERE id_session = :id AND modules.name LIKE :modulename',
                        [
                            'id' => $session['id'],
                            'modulename' => 'DATA%'
                        ])[0]['startdate'];

                    $endModuleForSession = $this->getDataFromSuivi( 'SELECT DISTINCT
                    MAX(date) as enddate
                    FROM daily
                    INNER JOIN modules ON modules.id = daily.id_module
                    WHERE id_session = :id AND modules.name LIKE :modulename',
                        [
                            'id' => $session['id'],
                            'modulename' => 'DATA%'
                        ])[0]['enddate'];

                    $module_name = $module['name'];
                }
                else
                {
                    $startModuleForSession = $this->getDataFromSuivi('SELECT DISTINCT
                    MIN(date) as startdate
                    FROM daily
                    INNER JOIN modules ON modules.id = daily.id_module
                    WHERE id_session = :id AND modules.name LIKE :modulename AND modules.name <> "INTRO"',
                        [
                            'id' => $session['id'],
                            'modulename' => $module['name'] . '%'
                        ])[0]['startdate'];

                    $endModuleForSession = $this->getDataFromSuivi( 'SELECT DISTINCT
                    MAX(date) as enddate
                    FROM daily
                    INNER JOIN modules ON modules.id = daily.id_module
                    WHERE id_session = :id AND modules.name LIKE :modulename AND modules.name <> "INTRO"',
                        [
                            'id' => $session['id'],
                            'modulename' => $module['name'] . '%'
                        ])[0]['enddate'];

                    $module_name = $module['name'];
                }

                $linksSessionModule[] = [
                    'session_id' => $session['id'],
                    'module_id' => $key + 1,
                    'start_date' => $startModuleForSession,
                    'end_date' => $endModuleForSession,
                    'moduleName' => $module_name
                ];
            }
        }
        return $linksSessionModule;
    }
    //------------------------------------------------------------------------------------------------------------------

    public function generateSessions( $manager ) :void
    {
        foreach($this->sessions as $suiviSession)
        {
            $session = new Session();
            $session->setName( $suiviSession['name'] );
            $session->setSchoolYear( $suiviSession['school_year'] );
            $session->setCreatedAt( $suiviSession['created_at'] );
            $session->setUpdatedAt( $suiviSession['updated_at'] );
            $manager->persist($session);
        }
        $manager->flush();

        $this->sessions = $this->sessionRepository->findAll();
    }

    public function generateModules( $manager ) :void
    {
        foreach($this->modules as $suiviModule)
        {
            $module = new Module();
            $module->setTitle( $suiviModule['name'] );
            $module->setWeeks( $suiviModule['weeks'] );
            $module->setCreatedAt( $suiviModule['created_at'] );
            $module->setUpdatedAt( $suiviModule['updated_at'] );
            $manager->persist($module);
        }
        $manager->flush();

        $this->modules = $this->moduleRepository->findAll();
    }

    public function generateLinksSessionModule( $manager ) :void
    {
        foreach( $this->linksSessionModule as $suiviLinkSessionModule )
        {
            $moduleToLink = $this->moduleRepository->find($suiviLinkSessionModule['module_id']);
            $sessionToLink = $this->sessionRepository->find($suiviLinkSessionModule['session_id']);

            $linkSessionModule = new LinkSessionModule();
            $linkSessionModule->setModule( $moduleToLink );
            $linkSessionModule->setSession( $sessionToLink );
            $linkSessionModule->setStartDate( new \DateTime( $suiviLinkSessionModule['start_date'] ) );
            $linkSessionModule->setEndDate( new \DateTime( $suiviLinkSessionModule['end_date'] ) );
            $manager->persist($linkSessionModule);
        }
        $manager->flush();
        $this->linksSessionModule = $this->linksSessionModuleRepository->findAll();
    }

    public function generateInstructors( $manager ) :void
    {
        foreach($this->instructors as $suiviInstructors)
        {
            $instructor = new Instructor();
            $instructor->setFirstname($suiviInstructors['first_name']);
            $instructor->setLastname($suiviInstructors['last_name']);
            $instructor->setBirthDate( $suiviInstructors['birthdate'] );
            $instructor->setPhone( $suiviInstructors['phone'] );
            $instructor->setEmail( strtolower($suiviInstructors['email']) );
            $instructor->setMoodleId( $suiviInstructors['id_moodle'] );
            $instructor->setSuiviId( $suiviInstructors['id'] );
            $instructor->setIsReferent( $suiviInstructors['isReferent'] );
            $instructor->setRoles(['ROLE_INSTRUCTOR']);
            $manager->persist($instructor);
        }
        $manager->flush();

        $this->instructors = $this->instructorRepository->findAll();
    }

    public function generateStudents( $manager ) :void
    {
        foreach($this->students as $suiviStudent)
        {
            $student = new Student();
            $student->setFirstname( $suiviStudent['first_name'] );
            $student->setLastname( $suiviStudent['last_name'] );
            $student->setBirthDate( $suiviStudent['birthdate'] );
            $student->setBadges( $suiviStudent['badges'] );
            $student->setEmail(strtolower($suiviStudent['email']));
            $student->setMoodleId( $suiviStudent['id_moodle'] );
            $student->setSuiviId( $suiviStudent['id'] );
            $student->setRoles(['ROLE_STUDENT']);
            $manager->persist($student);

        }
        $manager->flush();

        $this->students = $this->studentRepository->findAll();
    }

    public function generateLinksSessionStudent( $manager )
    {
        foreach ($this->linksSessionStudent as $suiviLinkSessionStudent)
        {
            $sessionToLink = $this->sessionRepository->find( $suiviLinkSessionStudent['session_id'] );
            $studentToLink = $this->studentRepository->findBy( ['suiviId' => $suiviLinkSessionStudent['student_id'] ] )[0];

            $linkSessionStudent = new LinkSessionStudent();
            $linkSessionStudent->setSession( $sessionToLink );
            $linkSessionStudent->setStudent( $studentToLink );
            $linkSessionStudent->setIsEnabled( $suiviLinkSessionStudent['is_enabled'] );

            $manager->persist( $linkSessionStudent );
        }
        $manager->flush();

        $this->linksSessionStudent = $this->linksSessionStudentRepository->findAll();
    }

    public function generateLinkInstructorSessionModule( $manager )
    {
        dump($this->linksInstructorSessionModule);
        foreach ($this->linksInstructorSessionModule as $suiviLinkInstructorSessionModule)
        {
            $instructorToLink = $this->instructorRepository->findBy( ['suiviId' => $suiviLinkInstructorSessionModule['instructor_id'] ] );
            dump($instructorToLink);
            $sessionToLink = $this->sessionRepository->find( $suiviLinkInstructorSessionModule['session_id'] );
            $moduleToLink = $this->moduleRepository->find( $suiviLinkInstructorSessionModule['module_id'] );

            $linkInstructorSessionModule = new LinkInstructorSessionModule();
            $linkInstructorSessionModule->setInstructor( $instructorToLink[0] );
            $linkInstructorSessionModule->setSession( $sessionToLink );
            $linkInstructorSessionModule->setModule( $moduleToLink );
            $manager->persist( $linkInstructorSessionModule );
        }
        $manager->flush();

        $this->linksInstructorSessionModule = $this->linkInstructorSessionModuleRepository->findAll();
    }
    // -----------------------------------------------------------------------------------
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
                array_push($arrayAnswers, ["id" => $answer->getId(), "wording" => $answer->getWording(), "is_correct" => $answer->getIsCorrect()]);
            }

            $questionAnswer =
                [
                    [
                        "question"=>
                            [
                                "id"=> $randomQuestion->getId(),
                                "wording"=>$randomQuestion->getWording(),
                                "responce_type"=>$randomQuestion->getResponseType(),
                                "proposals"=>
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
        for( $i = 0; $i < 5; $i++ )
        {
            $dbQcmInstancesWithoutResult = $this->qcmInstanceRepository->AllQcmInstanceWithoutResult();
            $dbQcmInstance = $dbQcmInstancesWithoutResult[ array_rand( $dbQcmInstancesWithoutResult ) ];

            $result = new Result();
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
                        'wording'         => $proposal['wording'],
                        'isCorrectAnswer' => $proposal['isCorrectAnswer'],
                        'isStudentAnswer' => rand(0,1),
                    ];
                }
                $resultAnswers[] = [
                    'id'          => $questionAnswer['id'],
                    'wording'     => $questionAnswer['wording'],
                    'isMultiple'  => $questionAnswer['isMultiple'],
                    'proposals'   => $proposalDetails
                ];
            }

            $result->setAnswers($resultAnswers);

            $manager->persist($result);
            $manager->flush();
        }
    }
}
