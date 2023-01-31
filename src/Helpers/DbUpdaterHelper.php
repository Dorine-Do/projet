<?php
namespace App\Helpers;



use App\Entity\Main\LinkInstructorSessionModule;
use App\Entity\Main\LinkSessionModule;
use App\Entity\Main\LinkSessionStudent;
use App\Entity\Main\Module;
use App\Entity\Main\Session;
use App\Repository\LinkInstructorSessionModuleRepository;
use App\Repository\LinkSessionModuleRepository;
use App\Repository\LinkSessionStudentRepository;
use App\Repository\ModuleRepository;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

class DbUpdaterHelper
{
    private ManagerRegistry $doctrine;
    private SessionRepository $sessionRepository;
    private EntityManagerInterface $entityManager;
    private LinkSessionStudentRepository $linkSessionStudentRepository;
    private LinkSessionModuleRepository $linkSessionModuleRepository;
    private ModuleRepository $moduleRepository;
    private LinkInstructorSessionModuleRepository $linkInstructorSessionModuleRepository;


    public function __construct(
        ManagerRegistry $doctrine,
        SessionRepository $sessionRepository,
        EntityManagerInterface $entityManager,
        LinkSessionStudentRepository $linkSessionStudentRepository,
        LinkSessionModuleRepository $linkSessionModuleRepository,
        ModuleRepository $moduleRepository,
        LinkInstructorSessionModuleRepository $linkInstructorSessionModuleRepository
    )
    {
        $this->doctrine = $doctrine;
        $this->sessionRepository = $sessionRepository;
        $this->entityManager = $entityManager;
        $this->linkSessionStudentRepository = $linkSessionStudentRepository;
        $this->linkSessionModuleRepository = $linkSessionModuleRepository;
        $this->moduleRepository = $moduleRepository;
        $this->linkInstructorSessionModuleRepository = $linkInstructorSessionModuleRepository;
    }

    public function updateUserSession( $user )
    {
        $now = new \DateTime();
//        dump($this->isUserStudent($user));
        if( $this->isUserStudent($user) )
        {
            try {
                $studentSuiviSessions = $this->getSuiviStudentSessions($user->getEmail());
                dump('$studentSuiviSessions');
                dump($studentSuiviSessions);
            }
            catch (\Error $e)
            {
                dd($e);
            }
                // Pour chaque session du student ( de la db de suivi )
                foreach ( $studentSuiviSessions as $studentSuiviSession )
                {
                    try {
                        $youUpEquivSession = $this->sessionRepository->findOneBy( [ 'name' => $studentSuiviSession['name'] ] );
                        dump('$youUpEquivSession');
                        dump($youUpEquivSession);
                    }
                    catch (\Error $e)
                    {
                        dd($e);
                    }
                    // si son equivalent n'existe pas dans la db youup
                    if( !$youUpEquivSession )
                    {
                        try {
                            // on le créer
                            $startDate = new \DateTime( $studentSuiviSession['startDate'] );
                            $newSession = new Session();
                            $newSession->setName( $studentSuiviSession['name'] );
                            $newSession->setSchoolYear( $startDate->format('Y') );

                            $this->entityManager->persist( $newSession );
                            $this->entityManager->flush();

                            // on créer le linkSessionStudent
                            $newLinkSessionStudent = new LinkSessionStudent();
                            $newLinkSessionStudent->setSession( $newSession );
                            $newLinkSessionStudent->setStudent( $user );
                            $newLinkSessionStudent->setIsEnabled(
                                $studentSuiviSession['startDate'] < $now && $studentSuiviSession['endDate'] > $now
                            );

                            $this->entityManager->persist( $newLinkSessionStudent );
                            $this->entityManager->flush();

                            $newSession->addLinkSessionStudent( $newLinkSessionStudent );
                            $user->addLinkSessionStudent( $newLinkSessionStudent );

                            $this->entityManager->persist( $newSession );
                            $this->entityManager->persist( $user );
                            $this->entityManager->flush();
                        }
                        catch(\Error $e)
                        {
                            dd($e);
                        }

                    }
                    else
                    {
                        try {
                            $linkSessionStudent = $this->linkSessionStudentRepository->findOneBy( [ 'session' => $youUpEquivSession, 'student' => $user] );
                            dump('$linkSessionStudent');
                            dump($linkSessionStudent);
                        }catch (\Error $e)
                        {
                            dd($e);
                        }

                        if ( !$linkSessionStudent )
                        {
                            try {
                                $newLinkSessionStudent = new LinkSessionStudent();
                                $newLinkSessionStudent->setSession( $youUpEquivSession );
                                $newLinkSessionStudent->setStudent( $user );
                                $newLinkSessionStudent->setIsEnabled(
                                    $studentSuiviSession['startDate'] < $now && $studentSuiviSession['endDate'] > $now
                                );

                                $youUpEquivSession->addLinkSessionStudent( $newLinkSessionStudent );
                                $user->addLinkSessionStudent( $newLinkSessionStudent );

                                $this->entityManager->persist( $youUpEquivSession );
                                $this->entityManager->persist( $user );
                                $this->entityManager->flush();
                                dump('flush $newLinkSessionStudent');
                            }catch (\Error $e)
                            {
                                dd($e);
                            }

                        }
                    }
                }

            try {
                $linksStudentSession = $this->linkSessionStudentRepository->findBy( [ 'student' => $user ] );
                dump('$linksStudentSession');
                dump($linksStudentSession);
            }catch (\Error $e)
            {
                dd($e);
            }

                // pour chaque linkSessionStudent (db youup)
                foreach ( $linksStudentSession as $linkStudentSession ) {

                    try {
                        // on recupere la session en rapport avec le linkSessionStudent
                        $suiviSession = array_filter( $studentSuiviSessions, function($studentSuiviSession) use ($linkStudentSession) {
                            return strtoupper($studentSuiviSession['name']) === strtoupper($linkStudentSession->getSession()->getName());
                        });
                        dump('$suiviSession');
                        dump($suiviSession);
                    }catch (\Error $e)
                    {
                        dd($e);
                    }

                    // Si la session existe dans les deux db ( suivi et youup )
                    if( $suiviSession && count($suiviSession) > 0 )
                    {

                        try {
                            $suiviSession = end($suiviSession);
                            $startDate = new \DateTime($suiviSession['startDate']);
                            $endDate = new \DateTime($suiviSession['endDate']);
                            // si la session de la db de suivi est toujours en cours (commencé, inachevée)
                            if( $startDate < $now && $endDate > $now && !$linkStudentSession->isEnabled() )
                            {
                                $linkStudentSession->setIsEnabled( true );
                            }
                            elseif( $startDate > $now || $endDate < $now && $linkStudentSession->isEnabled() )
                            {
                                $linkStudentSession->setIsEnabled( false );
                            }
                            $this->entityManager->persist($linkStudentSession);
                        }catch (\Error $e)
                        {
                            dd($e);
                        }

                    }
                    else
                    {
                            // la session existe dans youup mais n'existe plus dans la db de suivi
                            $sessionToUnlink = $linkStudentSession->getSession();
                            dump('$sessionToUnlink');
                            dump($sessionToUnlink);
                        try {
                            $sessionToUnlink->removeLinkSessionStudent($linkStudentSession);
                            dump('removeLinkSessionStudent');
                        }catch (\Error $e)
                        {
                            dd($e);
                        }
                        $user->removeLinkSessionStudent($linkStudentSession);
                        dump('removeLinkSessionStudent');

                        $this->entityManager->persist($sessionToUnlink);
                        dump('persist $sessionToUnlink');

                        $this->entityManager->persist($user);
                        dump('persist $user');

                        $this->entityManager->remove($linkStudentSession);
                        dump('remove $linkStudentSession');

                        $this->entityManager->flush();
                        dump('flush');


                    }
                    $this->entityManager->flush();
                }

                // pour chaque linkSessionStudent de la db youup
                foreach( $linksStudentSession as $linkStudentSession )
                {
                    try {
                        $session = $linkStudentSession->getSession();
                        dump('$session');
                        dump($session);
                        $suiviSessionModules = $this->getSuiviSessionModules( $session->getName() );
                        dump('$suiviSessionModules');
                        dump($suiviSessionModules);
                        $linksSessionModule = $this->linkSessionModuleRepository->findBy( [ 'session' => $session ] );
                        dump('$linksSessionModule');
                        dump($linksSessionModule);
                        $modules = array_map( function($linkSessionModule) {
                            return $linkSessionModule->getModule();
                        }, $linksSessionModule);
                        dump('$modules');
                        dump($modules);
                    }catch (\Error $e)
                    {
                        dd($e);
                    }


                    // Pour chaque module lié à cette session dans youup
                    foreach( $modules as $module )
                    {
                        try {
                            $suiviModule = array_filter( $suiviSessionModules, function( $suiviSessionModule ) use ( $module ) {
                                return $suiviSessionModule['title'] === $module->getTitle();
                            });
                        }catch (\Error $e)
                        {
                            dd($e);
                        }


                        if( count($suiviModule) > 0 )
                        {
                            try {
                                $linkSessionModuleToRemove = $this->linkSessionModuleRepository->findOneBy( [
                                    'session' => $linkStudentSession->getSession(),
                                    'module' => $module
                                ]);
                                $sessionToUnlink = $linkStudentSession->getSession();

                                $sessionToUnlink->removeLinksSessionModule($linkSessionModuleToRemove);
                                $module->removeLinksSessionModule($linkSessionModuleToRemove);

                                $this->entityManager->persist($sessionToUnlink);
                                $this->entityManager->persist($module);

                                $this->entityManager->remove( $linkSessionModuleToRemove );
                                $this->entityManager->flush();
                            }catch (\Error $e)
                            {
                                dd($e);
                            }

                        }
                        else
                        {
                            array_splice(
                                $suiviSessionModules,
                                array_search( $suiviModule , $suiviSessionModules ),
                                1
                            );
                        }
                    }

                    foreach($suiviSessionModules as $suiviSessionModule)
                    {
                        try {
                            $module = $this->moduleRepository->findOneBy( [ 'title' => $suiviSessionModule['title'] ] );
                        }catch (\Error $e)
                        {
                            dd($e);
                        }

                        if( !$module )
                        {
                            try {
                                $newModule = new Module();
                                $newModule->setTitle( $suiviSessionModule['title'] );
                                $newModule->setWeeks( $suiviSessionModule['weeks'] );
                                $this->entityManager->persist( $newModule );

                                $module = $newModule;
                            }catch (\Error $e)
                            {
                                dd($e);
                            }

                        }

                        if( !$this->linkSessionModuleRepository->findOneBy( [ 'session' => $session, 'module' => $module ] ) )
                        {
                            try {
                                $newLinkSessionModule = new LinkSessionModule();
                                $newLinkSessionModule->setSession( $session );
                                $newLinkSessionModule->setModule( $module );
                                $newLinkSessionModule->setStartDate( new \DateTime( $suiviSessionModule['startDate'] ) );
                                $newLinkSessionModule->setEndDate( new \DateTime( $suiviSessionModule['endDate'] ) );

                                $this->entityManager->persist( $newLinkSessionModule );
                                $this->entityManager->flush();

                                $module->addLinksSessionModule( $newLinkSessionModule );
                                $session->addLinksSessionModule( $newLinkSessionModule );

                                $this->entityManager->persist( $module );
                                $this->entityManager->persist( $session );
                                $this->entityManager->flush();
                            }catch (\Error $e)
                            {
                                dd($e);
                            }
                        }
                    }
                }
        }

        if( $this->isUserInstructor( $user ) )
        {
            $instructorSuiviSessions = $this->getSuiviInstructorSessions( $user->getEmail() );
//            dump($instructorSuiviSessions);
            foreach( $instructorSuiviSessions as $instructorSuiviSession )
            {
                // Sessions
                $session = $this->sessionRepository->findOneBy( [ 'name' => $instructorSuiviSession['sessionName'] ] );
//                dump($session);
                if( !$session ) {
                    $startDate = new \DateTime($instructorSuiviSession['startDate']);
                    $newSession = new Session();
                    $newSession->setName($instructorSuiviSession['sessionName']);
                    $newSession->setSchoolYear($startDate->format('Y'));

                    $this->entityManager->persist($newSession);
                    $this->entityManager->flush();

                    $session = $newSession;
                }

                $instructorSuiviSessionModules = $this->getInstructorSuiviSessionModules( $instructorSuiviSession['sessionName'], $user->getEmail() );
//                dump('$instructorSuiviSessionModules');
//                dump($instructorSuiviSessionModules);

                foreach( $instructorSuiviSessionModules as $instructorSuiviSessionModule)
                {
                    // Modules
                    $youupEquivModule = $this->moduleRepository->findOneBy( [ 'title' => $instructorSuiviSessionModule['title'] ] );
//                    dump('$youupEquivModule');
//                    dump($youupEquivModule);

                    if( !$youupEquivModule )
                    {
                        $newModule = new Module();
                        $newModule->setTitle( $instructorSuiviSessionModule['title'] );
                        $newModule->setWeeks( $instructorSuiviSessionModule['weeks'] );

                        $this->entityManager->persist($newModule);
                        $this->entityManager->flush();

                        $youupEquivModule = $newModule;
                    }

                    $youupLinkSessionModule = $this->linkSessionModuleRepository->findOneBy([
                        'session' => $session,
                        'module' => $youupEquivModule
                    ]);
//                    dump('$youupLinkSessionModule');
//                    dump($youupLinkSessionModule);

                    // LinkSessionModule
                    if( !$youupLinkSessionModule )
                    {
                        $newLinkSessionModule = new LinkSessionModule();
                        $newLinkSessionModule->setSession( $session );
                        $newLinkSessionModule->setModule( $youupEquivModule );
                        $newLinkSessionModule->setStartDate( new \DateTime($instructorSuiviSessionModule['startDate']) );
                        $newLinkSessionModule->setEndDate( new \DateTime($instructorSuiviSessionModule['endDate']) );

                        $this->entityManager->persist($newLinkSessionModule);
                        $this->entityManager->flush();

                        $session->addLinksSessionModule( $newLinkSessionModule );
                        $youupEquivModule->addLinksSessionModule( $newLinkSessionModule );

                        $this->entityManager->persist($session);
                        $this->entityManager->persist($youupEquivModule);
                        $this->entityManager->flush();
                    }


                    $youupLinkInstructorSessionModule = $this->linkInstructorSessionModuleRepository->findOneBy([
                        'instructor' => $user,
                        'session' => $session,
                        'module' => $youupEquivModule
                    ]);

//                    dump('$youupLinkInstructorSessionModule');
//                    dump($youupLinkInstructorSessionModule);

                    //linkInstructorSessionModule
                    if( !$youupLinkInstructorSessionModule )
                    {
                        $newLinkInstructorSessionModule = new LinkInstructorSessionModule();
                        $newLinkInstructorSessionModule->setInstructor( $user );
                        $newLinkInstructorSessionModule->setSession( $session );
                        $newLinkInstructorSessionModule->setModule( $youupEquivModule );

//                        dump('$newLinkInstructorSessionModule');
//                        dump($newLinkInstructorSessionModule);

                        try {
                            $this->entityManager->persist($newLinkInstructorSessionModule);
//                            dump('persist');

                            $this->entityManager->flush();
//                            dump('flush');
                        }catch (\Error $e)
                        {
                            dd($e);
                        }


                        $youupEquivModule->addLinksInstructorSessionModule( $newLinkInstructorSessionModule );
//                        dump('$youupEquivModule->$newLinkInstructorSessionModule');
                        $session->addLinksInstructorSessionModule( $newLinkInstructorSessionModule );
//                        dump('$session->$newLinkInstructorSessionModule');
                        $user->addLinksInstructorSessionModule( $newLinkInstructorSessionModule );
//                        dump('$user->$newLinkInstructorSessionModule');


                        $this->entityManager->persist($youupEquivModule);
                        $this->entityManager->persist($session);
                        $this->entityManager->persist($user);
//                        dump('before flush');
                        $this->entityManager->flush();
//                        dump('after flush');

                    }
                }

                // -----------------------------------------------------------------------------------------------------
                $youupLinksInstructorSessionModule = $this->linkInstructorSessionModuleRepository->findBy([
                    'session' => $session,
                    'instructor' => $user
                ]);
//                dump('$youupLinksInstructorSessionModule');
//                dump($youupLinksInstructorSessionModule);

                foreach( $youupLinksInstructorSessionModule as $youupLinkInstructorSessionModule )
                {
                    $keep = array_filter( $instructorSuiviSessionModules, function($instructorSuiviSessionModule) use ($youupLinkInstructorSessionModule) {

                        if(
                            strtoupper($instructorSuiviSessionModule['sessionName'])
                            ===
                            strtoupper($youupLinkInstructorSessionModule->getSession()->getName())
                            &&
                            strtoupper($instructorSuiviSessionModule['title'])
                            ===
                            strtoupper($youupLinkInstructorSessionModule->getModule()->getTitle())
                        )
                        {
                            return $instructorSuiviSessionModule;

                        };
                    });
//                    dump('$keep');
//                    dump($keep);

                    if( count($keep) === 0 )
                    {
                        $this->entityManager->remove($youupLinkInstructorSessionModule);
//                        dump('remove');
                        $this->entityManager->flush();
//                        dump('remove flush');
                    }
                }
            }
//            dd('stop for');
        }
    }

    public function getSuiviStudentSessions( string $studentEmail )
    {
        $sql = "
            SELECT sessions.name as name, MIN(daily.date) as startDate, MAX(daily.date) as endDate
            FROM daily
            LEFT JOIN link_students_daily
            ON daily.id = link_students_daily.id_daily
            LEFT JOIN sessions
            ON daily.id_session = sessions.id
            LEFT JOIN users
            ON users.id = link_students_daily.id_student
            WHERE users.email = ?
            AND daily.date >= NOW() - INTERVAL 30 DAY
            GROUP BY sessions.name
        ";
        return $this->rawSqlRequestToExtDb( $sql, [$studentEmail] );
    }

    public function getSuiviInstructorSessions( string $instructorEmail )
    {
        $sql = "
            SELECT DISTINCT sessions.name as sessionName, MIN(daily.date) as startDate, MAX(daily.date) as endDate
            FROM daily
            LEFT JOIN sessions
            ON daily.id_session = sessions.id
            LEFT JOIN users
            ON users.id = daily.id_user
            LEFT JOIN modules
            ON modules.id = daily.id_module
            WHERE users.email = ?
            AND daily.date >= NOW() - INTERVAL 30 DAY
            GROUP BY sessions.name
        ";
        return $this->rawSqlRequestToExtDb( $sql, [$instructorEmail] );
    }

    public function getInstructorSuiviSessionModules( $sessionName, $userEmail )
    {

        $modulesSql = "SELECT DISTINCT
            sessions.name as session_name,
            modules.name as module_name,
            MIN(date) as start_date,
            MAX(date) as end_date,
            COUNT(modules.id) as duration
            FROM modules
            LEFT JOIN daily ON daily.id_module = modules.id
            LEFT JOIN sessions ON sessions.id = daily.id_session
            LEFT JOIN users ON users.id = daily.id_user
            WHERE LOWER(sessions.name) = ? AND users.email = ?
            AND daily.date >= NOW() - INTERVAL 30 DAY
            GROUP BY modules.name";

        $suiviModules = $this->rawSqlRequestToExtDb($modulesSql, [ strtolower($sessionName), $userEmail ]);
        $moduleByName = [];
        foreach($suiviModules as $suiviModule)
        {
            $explodedModuleName = explode('.', $suiviModule['module_name']);
            $moduleName = $explodedModuleName[0];
            if( !array_key_exists($moduleName, $moduleByName) )
            {
                $moduleByName[$moduleName] = [];
            }
            $moduleByName[$moduleName] = $suiviModule;
        }

        $modules = [];
        foreach( $moduleByName as $name => $module )
        {

            $weeks = ceil( $module['duration'] / 5 );

            $modules[] = [
                'sessionName' => $sessionName,
                'title' => $name,
                'weeks' => $weeks,
                'startDate' => $module['start_date'],
                'endDate' => $module['end_date'],
            ];
        }
        return $modules;
    }

    public function getSuiviSessionModules( $sessionName )
    {

        $lowerSessionName = strtolower($sessionName);
        $upperSessionName = strtoupper($sessionName);

        $modulesSql = "SELECT DISTINCT
            sessions.name as session_name,
            modules.name as module_name,
            MIN(date) as start_date,
            MAX(date) as end_date,
            COUNT(modules.id) as duration
            FROM modules
            LEFT JOIN daily ON daily.id_module = modules.id
            LEFT JOIN sessions ON sessions.id = daily.id_session
            WHERE sessions.name = ? OR sessions.name = ? OR sessions.name = ? 
            AND daily.date >= NOW() - INTERVAL 1 YEAR
            GROUP BY modules.name";

        $suiviModules = $this->rawSqlRequestToExtDb($modulesSql, [ $lowerSessionName, $upperSessionName, $sessionName]);

        $moduleByName = [];
        dump('$suiviModules');
        dump($suiviModules);

        foreach($suiviModules as $suiviModule)
        {
            $explodedModuleName = explode('.', $suiviModule['module_name']);
            $moduleName = $explodedModuleName[0];
            if( !array_key_exists($moduleName, $moduleByName) )
            {
                $moduleByName[$moduleName] = [];
            }
            $moduleByName[$moduleName] = $suiviModule;
        }

        $modules = [];
        foreach( $moduleByName as $name => $module )
        {
            $weeks = ceil( $module['duration'] / 5 );
            $modules[] = [
                'title' => $name,
                'weeks' => $weeks,
                'startDate' => $module['start_date'],
                'endDate' => $module['end_date'],
            ];
        }
        return $modules;
    }

    public function isUserStudent( $user )
    {
        return in_array('ROLE_STUDENT', $user->getRoles());
    }

    public function isUserInstructor( $user )
    {
        return in_array('ROLE_INSTRUCTOR', $user->getRoles());
    }

    protected function rawSqlRequestToExtDb( $sql, $params = [], $extDb = 'dbsuivi' ) {
        $conn = $this->doctrine->getConnection($extDb);
        return $conn
            ->prepare($sql)
            ->executeQuery($params)
            ->fetchAll();
    }
}