<?php
namespace App\Helpers;



use App\Entity\Main\LinkSessionStudent;
use App\Entity\Main\Session;
use App\Repository\LinkSessionStudentRepository;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Google\Service\Docs\Link;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class DbUpdaterHelper
{
    private ManagerRegistry $doctrine;
    private SessionRepository $sessionRepository;
    private EntityManagerInterface $entityManager;
    private LinkSessionStudentRepository $linkSessionStudentRepository;

    public function __construct(
        ManagerRegistry $doctrine,
        SessionRepository $sessionRepository,
        EntityManagerInterface $entityManager,
        LinkSessionStudentRepository $linkSessionStudentRepository
    )
    {
        $this->doctrine = $doctrine;
        $this->sessionRepository = $sessionRepository;
        $this->entityManager = $entityManager;
        $this->linkSessionStudentRepository = $linkSessionStudentRepository;
    }

    public function updateUserSession( $user )
    {
        $now = new \DateTime();
        if( $this->isUserStudent($user) )
        {
            $studentSuiviSessions = $this->getSuiviStudentSessions( $user->getEmail() );
            foreach ( $studentSuiviSessions as $studentSuiviSession )
            {
                $youUpEquivSession = $this->sessionRepository->findOneBy( [ 'name' => $studentSuiviSession['name'] ] );

                if( !$youUpEquivSession )
                {
                    $startDate = new \DateTime( $studentSuiviSession['startDate'] );
                    $newSession = new Session();
                    $newSession->setName( $studentSuiviSession['name'] );
                    $newSession->setSchoolYear( $startDate->format('Y') );

                    $this->entityManager->persist( $newSession );

                    $newLinkSessionStudent = new LinkSessionStudent();
                    $newLinkSessionStudent->setSession( $newSession );
                    $newLinkSessionStudent->setStudent( $user );
                    $newLinkSessionStudent->setIsEnabled(
                        $studentSuiviSession['startDate'] < $now && $studentSuiviSession['endDate'] > $now ? true : false
                    );

                    $this->entityManager->persist( $newLinkSessionStudent );

                    $newSession->addLinkSessionStudent( $newLinkSessionStudent );
                    $user->addLinkSessionStudent( $newLinkSessionStudent );

                    $this->entityManager->persist( $newSession );
                    $this->entityManager->persist( $user );
                    $this->entityManager->flush();
                }
            }

            $linksStudentSession = $this->linkSessionStudentRepository->findBy( [ 'student' => $user ] );

            foreach ( $linksStudentSession as $linkStudentSession ) {

                $suiviSession = array_filter( $studentSuiviSessions, function($studentSuiviSession) use ($linkStudentSession) {
                    return $studentSuiviSession['name'] === $linkStudentSession->getSession()->getName();
                });

                if( $suiviSession )
                {
                    if( $suiviSession['startDate'] < $now && $suiviSession['endDate'] > $now && !$linkStudentSession->isEnabled() )
                    {
                        $linkStudentSession->setIsEnabled( true );
                    }
                    elseif( $suiviSession['startDate'] > $now || $suiviSession['endDate'] < $now && $linkStudentSession->isEnabled() )
                    {
                        $linkStudentSession->setIsEnabled( false );
                    }
                    $this->entityManager->persist($linkStudentSession);
                }
                else
                {
                    $this->entityManager->remove($linkStudentSession);
                }
                $this->entityManager->flush();
            }
        }

        if( $this->isUserInstructor( $user ) )
        {
            // comparer les sessions dbSuivi VS dbYouUp
                // ajouter celles qui manquent


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
            WHERE users.email = ?,
            GROUP BY name
        ";
        return $this->rawSqlRequestToExtDb( $sql, ['email' => $studentEmail] );
    }

    public function getSuiviInstructorSessions( string $studentEmail )
    {
        $sql = "
            SELECT DISTINCT sessions.name as sessionName, modules.name as moduleName
            FROM daily
            LEFT JOIN sessions
            ON daily.id_session = sessions.id
            LEFT JOIN users
            ON users.id = daily.id_user
            LEFT JOIN modules
            ON modules.id = daily.id_module
            WHERE users.email = ? AND daily.date >= DATE( NOW() - INTERVAL 1 MONTH )
        ";
        return $this->rawSqlRequestToExtDb( $sql, ['email' => $studentEmail] );
    }

    public function updateSessions()
    {

    }

    public function updateLinksSessionsModules()
    {

    }

    public function getSuiviModules()
    {
        $sql = "
            SELECT DISTINCT
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
            GROUP BY modules.id
        ";
        $sessions = $this->sessionRepository->findAll();
        $moduleByName = [];
        foreach ($sessions as $session)
        {
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

            $suiviModules = $this->rawSqlRequestToExtDb($modulesSql, [ 'id' => $session->getId() ]);
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
                'title' => $name,
                'weeks' => $weeks,
            ];
        }
        return $modules;
    }

    public function getSuiviModulesBySession()
    {

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