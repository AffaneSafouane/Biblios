<?php 

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserCreatorVoter extends Voter {
    
    /**
     * @innheritDoc
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return 'user.email' === $attribute && $subject instanceof User;
    }

    /**
     * @innheritDoc
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var User $subject */
        return $user === $subject->getEmail();
    }
}