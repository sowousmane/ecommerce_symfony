<?php

namespace App\Service;
use Symfony\Component\HttpFoundation\RequestStack;

class AppService
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function setSession($value)
    {
        $session = $this->requestStack->getSession();
        $session->set('id_login', $value);
    }

    public function getSession()
    {
        $session = $this->requestStack->getSession();
        return $session->get('id_login');
    }

    public function isAdmin($role)
    {
        if($role == 'ROLE_ADMIN'){ return true; } return false;
    }
}