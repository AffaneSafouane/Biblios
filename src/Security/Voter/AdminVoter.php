<?php

namespace App\Security\Voter;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Editor;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AdminVoter extends Voter
{
    public const CREATE = 'CREATE';
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute == self::CREATE) {
            return true;
        }

        return in_array($attribute, [self::EDIT, self::DELETE])
            && ($subject instanceof Author || $subject instanceof Editor || $subject instanceof Book);
    }

    /**
     * @param string $attribute
     * @param Author|Book|Editor|null $subject
     * @param TokenInterface $token
     * @return bool
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
            self::EDIT, self::DELETE => ($subject instanceof Author || $subject instanceof Editor || $subject instanceof Book)
                && $this->security->isGranted('ROLE_EDITION_DE_LIVRE')
                && ($user->getIsVerified() === true),
            self::CREATE => $this->security->isGranted('ROLE_AJOUT_DE_LIVRE')
                && ($user->getIsVerified()) === true,
            default => false,
        };
    }
}
