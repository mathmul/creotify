<?php

declare(strict_types=1);

namespace App\Repository\Contract;

use App\Entity\Customer;

/**
 * @method void remove(object $object, bool $flush = true)
 */
interface OrderRepositoryInterface extends RepositoryInterface
{
    public function customerHasArticle(Customer $customer, int $articleId): bool;

    public function customerHasSubscription(Customer $customer): bool;
}
