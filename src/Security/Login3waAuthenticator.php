<?php
namespace App\Security;

use App\Entity\Main\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use phpDocumentor\Reflection\Types\Object_;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        ManagerRegistry $managerRegistry,
        UserRepository $userRepo,
        ManagerRegistry $doctrine
    )
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->managerRegistry = $managerRegistry;
        $this->userRepo = $userRepo;
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'app_connection';
    }

    public function authenticate(Request $request): Passport
    {
        if ( !$_COOKIE['cookie'] ) {
            header('Location: https://login.3wa.io');
            exit();
            // The cookie was empty, authentication fails
            // Code 401 "Unauthorized"
            // throw new CustomUserMessageAuthenticationException('Utilisateur non connecté');
        }



        if(  )
        {

        }

        // recuperation des infos du user dans admin_login
        $admin_login_user = [];
        // recuperation de cet utilisateur dans la db youup
        if( strpos( $admin_login_user['email'],'3wa.io') ) {
            $emailType = 'email3wa';
        }
        else
        {
            $emailType = 'email';
        }

        $userYouUp = $this->userRepo->findOneBy([
            'firstname' => $admin_login_user['firstname'],
            'lastname' => $admin_login_user['lastname'],
            $emailType => $admin_login_user['email']
        ]);

        if( !$userYouUp )
        {
            // find user in dbsuivi to set youup user fields
            $suiviUser = [];
            if( $suiviUser[''] )
            {

            }
            $user = new User();
            $user->setFirstName();
            $user->setLastName();
            $user->setMoodleId();
            $user->setDiscr();
        }

        return new SelfValidatingPassport(new UserBadge($cookieYouUp, $user));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
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

    protected function rawSqlRequestToExtDb( $sql, $params = [], $extDb = 'dbsuivi' ) {
        $conn = $this->doctrine->getConnection($extDb);
        return $conn
            ->prepare($sql)
            ->executeQuery($params)
            ->fetchAll();
    }
}