<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class LoginAuthenticator extends AbstractGuardAuthenticator
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->passwordEncoder = $userPasswordEncoder;
    }

    public function supports(Request $request)
    {
        return $request->get('_route') === 'api_login' && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        return ['email'=>$request->request->get('email'),
            'password'=>$request->request->get('password')];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        //var_dump($credentials); die;
        return $userProvider->loadUserByUsername($credentials['email']);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        //var_dump($user);die;
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse(['error'=>$exception->getMessageKey()], 400);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new JsonResponse(['result'=>true]);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse(['error'=>'Access Denied']);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
