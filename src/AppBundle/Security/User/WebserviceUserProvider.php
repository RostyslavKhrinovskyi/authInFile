<?php

namespace AppBundle\Security\User;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class WebserviceUserProvider implements UserProviderInterface
{
    /**
     * @var Container
     */
    private $finder;

    /**
     * WebserviceUserProvider constructor.
     * @param Container $container
     */
    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * @param string $username
     * @return WebserviceUser
     */
    public function loadUserByUsername($username)
    {
        $userData = null;

        foreach ($this->finder as $file) {
            $userData = json_decode($file->getContents(), true);
        }

        if ($userData && isset($userData['users'])) {

            $findUser = $this->findUserInArray($userData['users'], $username);

            if ($findUser) {
                return new WebserviceUser($username, $findUser['password'], false, ['ROLE_USER']);
            }
        }

        throw new UsernameNotFoundException(
            sprintf('Username "%s" does not exist.', $username)
        );
    }

    /**
     * @param UserInterface $user
     * @return WebserviceUser
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof WebserviceUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'AppBundle\Security\User\WebserviceUser';
    }

    /**
     * @param $users
     * @param $username
     * @return array
     */
    private function findUserInArray($users, $username)
    {
        $userInfo = false;

        foreach ($users as $user) {
            if ($user['username'] === $username) {
                $userInfo = $user;
            }
        }

        return $userInfo;
    }
}