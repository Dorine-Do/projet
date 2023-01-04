<?php
namespace App\Security;

use App\Entity\Main\Admin;
use App\Entity\Main\Instructor;
use App\Entity\Main\Student;
use App\Entity\Main\User;
use App\Helpers\DbUpdaterHelper;
use App\Repository\CookieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use phpDocumentor\Reflection\Types\Object_;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class Login3waAuthenticator extends AbstractAuthenticator
{
    private ClientRegistry $clientRegistry;
    private EntityManagerInterface $entityManager;
    private RouterInterface $router;
    private ManagerRegistry $managerRegistry;
    private UserRepository $userRepo;
    private ManagerRegistry $doctrine;
    private CookieRepository $cookieRepo;
    private SessionInterface $session;
    private DbUpdaterHelper $dbUpdaterHelper;

    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        ManagerRegistry $managerRegistry,
        UserRepository $userRepo,
        ManagerRegistry $doctrine,
        CookieRepository $cookieRepo,
        DbUpdaterHelper $dbUpdaterHelper
    )
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->managerRegistry = $managerRegistry;
        $this->userRepo = $userRepo;
        $this->doctrine = $doctrine;
        $this->cookieRepo = $cookieRepo;
        $this->dbUpdaterHelper = $dbUpdaterHelper;
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'app_connection';
    }

    public function authenticate(Request $request): Passport
    {
        $cookieString = $this->generateCookieString();

        return new SelfValidatingPassport(new UserBadge($cookieString, function() use ($request, $cookieString) {

//            // TODO delete in production -------------------------------------------------------------------------------
            $stringBeginning = explode('\\',$request->server->get('PUBLIC'));
            if( $stringBeginning[0] === 'C:' ) {
                return $this->userRepo->find(121);
            }
//            // TODO end delete in production ---------------------------------------------------------------------------

            // if user isn't logged in 3wa.io ( cookie isn't set )
            if ( !isset($_COOKIE['cookie']) ) {
                header('Location: https://login.3wa.io/youup');
                exit();
            }

            // get user by cookie in dblogin
            $sqlReqDblogin = "
                SELECT
                users.firstname, users.lastname, users.username, users.email, users.access, cookies.cookie
                FROM cookies
                LEFT JOIN users
                ON users.id = cookies.id_user
                WHERE cookies.cookie = :cookie
                ";

            $dbLoginUser = $this->rawSqlRequestToExtDb( $sqlReqDblogin, [ 'cookie' => $_COOKIE['cookie'] ], 'dblogin' )[0];

            // if userLogin doesn't exist in youUp db
            if( !$this->userRepo->findOneBy( [ 'email' => $dbLoginUser['email'] ] ) )
            {
                // get user data from dbsuivi
                $sqlReqDbsuivi = "SELECT firstname, lastname, email, access, phone, id_moodle, id FROM users WHERE email = :email";

                $dbSuiviUser = $this->rawSqlRequestToExtDb( $sqlReqDbsuivi, [ 'email' => $dbLoginUser['email'] ], 'dbsuivi' )[0];

                // check access type
                switch( $dbSuiviUser['access'] )
                {
                    case 'teacher':
                        $newUser = new Instructor();
                        $newUser->setFirstName( $dbSuiviUser['firstname'] );
                        $newUser->setLastName( $dbSuiviUser['lastname'] );
                        $newUser->setEmail($dbSuiviUser['email']);
                        $newUser->setPhone( $dbSuiviUser['phone'] ?: null );
                        $newUser->setMoodleId( $dbSuiviUser['id_moodle'] );
                        $newUser->setSuiviId( $dbSuiviUser['id'] );
                        $newUser->setRoles( ['ROLE_INSTRUCTOR'] );
                        break;
                    case 'admin':
                        $newUser = new Admin();
                        $newUser->setFirstName( $dbSuiviUser['firstname'] );
                        $newUser->setLastName( $dbSuiviUser['lastname'] );
                        $newUser->setEmail($dbSuiviUser['email']);
                        $newUser->setMoodleId( $dbSuiviUser['id_moodle'] );
                        $newUser->setSuiviId( $dbSuiviUser['id'] );
                        $newUser->setRoles( ['ROLE_ADMIN'] );
                        break;
                    default:
                        $newUser = new Student();
                        $newUser->setFirstName( $dbSuiviUser['firstname'] );
                        $newUser->setLastName( $dbSuiviUser['lastname'] );
                        $newUser->setEmail($dbSuiviUser['email']);
                        $newUser->setMoodleId( $dbSuiviUser['id_moodle'] );
                        $newUser->setSuiviId( $dbSuiviUser['id'] );
                        $newUser->setRoles( ['ROLE_STUDENT'] );
                        break;
                }
                $this->entityManager->persist($newUser);
                $this->entityManager->flush();

                $user = $this->userRepo->find( $newUser->getId() );
            }
            else
            {
                $user = $this->userRepo->findOneBy( [ 'email' => $dbLoginUser['email'] ] );
            }

//            $sessionStorage = new NativeSessionStorage([], new NativeFileSessionHandler());
//            $session = new Session($sessionStorage);

            /********************************************
            UPDATING DB FROM DBSUIVI
            *********************************************/

            $this->dbUpdaterHelper->updateUserSession( $user );


                // Recuperer les formateurs inscris sur chaque module de cette session
                // pour chaque formateurs de la session
                    // s'il n'est pas ou plus inscrit au module de cette session
                        // le supprimer de link-instructor-session-module
                    // s'il doit y etre nais n'y est pas
                        // ajout du link-instructor-session-module



            return $user;
        }));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetUrl = $this->router->generate('app_check_dashboard');
        return new RedirectResponse($targetUrl);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        // TODO Check this
        return new RedirectResponse(
            '/connect/', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    protected function rawSqlRequestToExtDb( $sql, $params = [], $extDb = 'dbsuivi' ) {
        $conn = $this->doctrine->getConnection($extDb);
        return $conn
            ->prepare($sql)
            ->executeQuery($params)
            ->fetchAll();
    }

    protected function generateCookieString( $length = 32 )
    {
        $characters = str_split( '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
        $cookieString = '';
        for( $n = 0; $n < $length; $n++ )
        {
            $cookieString .= $characters[array_rand($characters)];
        }
        return $cookieString;
    }
}
