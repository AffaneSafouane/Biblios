<?php

namespace App\Factory;

use App\Entity\Comment;
use App\Enum\CommentStatus;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Comment>
 */
final class CommentFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Comment::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        $status = self::faker()->randomElement(CommentStatus::cases());

        return [
            'book' => BookFactory::random(),
            'content' => self::faker()->text(),
            'review' => self::faker()->randomFloat(2, 0, 5),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'publishedAt' => $status === CommentStatus::Published
                ? \DateTimeImmutable::createFromMutable(self::faker()->dateTime())
                : null,
            'commentedBy' => UserFactory::random(),
            'status' => $status,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Comment $comment): void {})
        ;
    }
}
