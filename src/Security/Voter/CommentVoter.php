<?php

namespace App\Security\Voter;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CommentVoter extends Voter
{
    public const EDIT = 'COMMENT_EDIT';
    public const CREATE = 'COMMENT_CREATE';
    public const DELETE = 'COMMENT_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute === self::CREATE) {
            return true; // comment creation doesn't demand Comment object
        }

        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Comment;
    }

    /**
     * @param string $attribute
     * @param Comment|null $subject
     * @param TokenInterface $token
     * @return bool
     *
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        return match ($attribute) {
            self::EDIT, self::DELETE => $subject instanceof Comment
                && $user->getIsVerified() === true
                && $user === $subject->getCommentedBy(),
            self::CREATE => $user->getIsVerified() === true,
            default => false,
        };
    }
}
