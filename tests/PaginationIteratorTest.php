<?php

declare(strict_types=1);

/*
 * This file is part of clarus-it/http-client package.
 *
 * (c) PT Clarus Innovace Teknologi <https://clarus-it.co.id>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace ClarusIt\HttpClient\Tests;

use ClarusIt\HttpClient\ClarusHttpClient;
use ClarusIt\HttpClient\PaginationIterator;
use PHPUnit\Framework\TestCase;

final class PaginationIteratorTest extends TestCase
{
    // public function testPaginationIterator(): void
    // {
    //     $uri = 'https://app.dev.localhost:13000/api/';
    //     $apiKey = '3f71b88bdcb5af7094108c8467fd7e23ba706cba64829212395ba071835efc6e';
    //     $client = new ClarusHttpClient($apiKey, $uri);

    //     $endpoint = '/api/insurance/placing-batches/01902fd6-e555-47eb-4761-fc2e9e48b2b5/insurables';

    //     $iterator = new PaginationIterator($client, $endpoint);


    //     foreach ($iterator as $item) {
    //         /**
    //          * @psalm-suppress RedundantConditionGivenDocblockType
    //          * @phpstan-ignore-next-line
    //          */
    //         $this->assertIsArray($item);

    //         /**
    //          * @psalm-suppress UndefinedFunction
    //          * @phpstan-ignore-next-line
    //          */
    //         dump($item);
    //     }
    // }
}
