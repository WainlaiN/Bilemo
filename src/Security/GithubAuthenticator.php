<?php


namespace App\Security;


use App\Repository\ClientRepository;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GithubAuthenticator extends SocialAuthenticator
{

    private $router;

    private $clientRegistry;

    private $clientRepository;

    public function __construct(
        RouterInterface $router,
        ClientRegistry $clientRegistry,
        ClientRepository $clientRepository
    ) {
        $this->router = $router;
        $this->clientRegistry = $clientRegistry;
        $this->clientRepository = $clientRepository;

    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->router->generate('app_login'));
    }

    public function supports(Request $request)
    {
        return 'oauth_check' === $request->attributes->get('_route') && $request->get('service') === 'github';

    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->clientRegistry->getClient('github'));
    }

    /**
     * @param AccessToken $credentials
     * @param UserProviderInterface $userProvider
     * @return \Symfony\Component\Security\Core\User\UserInterface|void|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var GithubResourceOwner $GithubUser */
        $githubUser = $this->clientRegistry->getClient('github')->fetchUserFromToken($credentials);

        return $this->clientRepository->findOrCreateFromGithubOauth($githubUser);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // TODO: Implement onAuthenticationFailure() method.
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return new RedirectResponse('/');
    }
}