<?php

namespace AppBundle\Security\User;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class WebserviceUserProvider implements UserProviderInterface
{

    public function loadUserByUsername($username)
    {
        $finder = new Finder();

        $finder
            ->in(__DIR__ . '/../User/Data')
            ->files()
            ->name('users.txt');

        foreach ($finder as $file) {
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

    public function findUserInArray($users, $username)
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