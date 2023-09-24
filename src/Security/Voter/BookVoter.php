<?php

namespace App\Security\Voter;

use App\Entity\Book;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BookVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::EDIT || $attribute === self::DELETE && $subject instanceof Book;

    }
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var  $user User */
        $user = $token->getUser();

        return $user->getBooks()->contains($subject);

        }



}
