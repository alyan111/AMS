<?php

declare(strict_types=1);

/*
 * This file is part of the WebPush library.
 *
 * (c) Louis Lagrange <lagrange.louis@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Minishlink\WebPush;

/**
 * @author Sergii Bondarenko <sb@firstvector.org>
 */
interface SubscriptionInterface
{
    public function getEndpoint(): string;

    public function getPublicKey(): ?string;

    public function getAuthToken(): ?string;

    public function getContentEncoding(): ?string;
}
