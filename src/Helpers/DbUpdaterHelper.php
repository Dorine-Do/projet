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
        if( $this->isUserStudent($user) )
        {
            $studentSuiviSessions = $this->getSuiviStudentSessions( $user->getEmail() );

            // Pour chaque session du student ( de la db de suivi )
            foreach ( $studentSuiviSessions as $studentSuiviSession )
            {
                $youUpEquivSession = $this->sessionRepository->findOneBy( [ 'name' => $studentSuiviSession['name'] ] );
                // si son equivalent n'existe pas dans la db youup
                if( !$youUpEquivSession )
                {
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
                else
                {
                    $linkSessionStudent = $this->linkSessionStudentRepository->findOneBy( [ 'session' => $youUpEquivSession, 'student' => $user] );
                    if ( !$linkSessionStudent )
                    {
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
                    }
                }
            }

            $linksStudentSession = $this->linkSessionStudentRepository->findBy( [ 'student' => $user ] );
            // pour chaque linkSessionStudent (db youup)
            foreach ( $linksStudentSession as $linkStudentSession ) {
                // on recupere la session en rapport avec le linkSessionStudent
                $suiviSession = array_filter( $studentSuiviSessions, function($studentSuiviSession) use ($linkStudentSession) {
                    return $studentSuiviSession['name'] === $linkStudentSession->getSession()->getName();
                });

                // Si la session existe dans les deux db ( suivi et youup )
                if( $suiviSession && count($suiviSession) > 0 )
                {
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
                }
                else
                {
                    // la session existe dans youup mais n'existe plus dans la db de suivi
                    $sessionToUnlink = $linkStudentSession->getSession();
                    $sessionToUnlink->removeLinkSessionStudent($linkStudentSession);
                    $user->removeLinkSessionStudent($linkStudentSession);
                    $this->entityManager->persist($sessionToUnlink);
                    $this->entityManager->persist($user);
                    $this->entityManager->remove($linkStudentSession);
                }
                $this->entityManager->flush();
            }

            // pour chaque linkSessionStudent de la db youup
            foreach( $linksStudentSession as $linkStudentSession )
            {
                $session = $linkStudentSession->getSession();
                $suiviSessionModules = $this->getSuiviSessionModules( $session->getName() );
                $linksSessionModule = $this->linkSessionModuleRepository->findBy( [ 'session' => $session ] );
                $modules = array_map( function($linkSessionModule) {
                    return $linkSessionModule->getModule();
                }, $linksSessionModule);

                // Pour chaque module lié à cette session dans youup
                foreach( $modules as $module )
                {
                    $suiviModule = array_filter( $suiviSessionModules, function( $suiviSessionModule ) use ( $module ) {
                        return $suiviSessionModule['title'] === $module->getTitle();
                    });

                    if( count($suiviModule) > 0 )
                    {
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
                    $module = $this->moduleRepository->findOneBy( [ 'title' => $suiviSessionModule['title'] ] );

                    if( !$module )
                    {
                        $newModule = new Module();
                        $newModule->setTitle( $suiviSessionModule['title'] );
                        $newModule->setWeeks( $suiviSessionModule['weeks'] );
                        $this->entityManager->persist( $newModule );

                        $module = $newModule;
                    }

                    if( !$this->linkSessionModuleRepository->findOneBy( [ 'session' => $session, 'module' => $module ] ) )
                    {
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
                    }
                }
            }
        }

        if( $this->isUserInstructor( $user ) )
        {
            $instructorSuiviSessions = $this->getSuiviInstructorSessions( $user->getEmail() );

            foreach( $instructorSuiviSessions as $instructorSuiviSession )
            {

                $session = $this->sessionRepository->findOneBy( [ 'name' => $instructorSuiviSession['sessionName'] ] );

                if( !$session ) {
                    $startDate = new \DateTime($instructorSuiviSession['startDate']);
                    $newSession = new Session();
                    $newSession->setName($instructorSuiviSession['sessionName']);
                    $newSession->setSchoolYear($startDate->format('Y'));

                    $this->entityManager->persist($newSession);
                    $this->entityManager->flush();

                    $session = $newSession;
                }

                $instructorSuiviSessionModules = $this->getInstructorSuiviSessionModules( $session->getName(), $user->getEmail() );

                foreach( $instructorSuiviSessionModules as $instructorSuiviSessionModule)
                {
                    $youupEquivModule = $this->moduleRepository->findOneBy( [ 'title' => $instructorSuiviSessionModule['title'] ] );

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

                    if( !$youupLinkInstructorSessionModule )
                    {
                        $newLinkInstructorSessionModule = new LinkInstructorSessionModule();
                        $newLinkInstructorSessionModule->setInstructor( $user );
                        $newLinkInstructorSessionModule->setSession( $session );
                        $newLinkInstructorSessionModule->setModule( $youupEquivModule );

                        $this->entityManager->persist($newLinkInstructorSessionModule);
                        $this->entityManager->flush();

                        $youupEquivModule->addLinksInstructorSessionModule( $newLinkInstructorSessionModule );
                        $session->addLinksInstructorSessionModule( $newLinkInstructorSessionModule );
                        $user->addLinksInstructorSessionModule( $newLinkInstructorSessionModule );

                        $this->entityManager->persist($youupEquivModule);
                        $this->entityManager->persist($session);
                        $this->entityManager->persist($user);
                        $this->entityManager->flush();
                    }
                }

                // -----------------------------------------------------------------------------------------------------
                $youupLinksInstructorSessionModule = $this->linkInstructorSessionModuleRepository->findBy([
                    'session' => $session,
                    'instructor' => $user
                ]);

                foreach( $youupLinksInstructorSessionModule as $youupLinkInstructorSessionModule )
                {
                    $keep = array_filter( $instructorSuiviSessionModules, function($instructorSuiviSessionModule) use ($youupLinkInstructorSessionModule) {
                        if(
                            $instructorSuiviSessionModule['sessionName'] === $youupLinkInstructorSessionModule->getSession()->getName()
                            &&
                            $instructorSuiviSessionModule['title'] === $youupLinkInstructorSessionModule->getModule()->getTitle()
                        )
                        {
                            return $instructorSuiviSessionModule;

                        };
                    });
                    if( count($keep) === 0 )
                    {
                        $this->entityManager->remove($youupLinkInstructorSessionModule);
                        $this->entityManager->flush();
                    }
                }
            }
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
            WHERE sessions.name = ? AND users.email = ?
            GROUP BY modules.name";

        $suiviModules = $this->rawSqlRequestToExtDb($modulesSql, [ $sessionName, $userEmail ]);
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

        $modulesSql = "SELECT DISTINCT
            sessions.name as session_name,
            modules.name as module_name,
            MIN(date) as start_date,
            MAX(date) as end_date,
            COUNT(modules.id) as duration
            FROM modules
            LEFT JOIN daily ON daily.id_module = modules.id
            LEFT JOIN sessions ON sessions.id = daily.id_session
            WHERE sessions.name = ?
            GROUP BY modules.name";

        $suiviModules = $this->rawSqlRequestToExtDb($modulesSql, [ $sessionName ]);
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