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

namespace ClarusIt\HttpClient;

use Graviton\LinkHeaderParser\LinkHeader;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @implements \IteratorAggregate<array<array-key,array<array-key,mixed>>>
 */
final class PaginationIterator implements \IteratorAggregate
{
    public function __construct(
        private HttpClientInterface $client,
        private string $endpoint,
    ) {
    }

    public function getIterator(): \Traversable
    {
        $currentEndpoint = $this->endpoint;

        while ($currentEndpoint !== null) {
            try {
                // mempersiapkan request

                $response = $this->client->request('GET', $currentEndpoint);

                // melakukan request dan melakukan parse terhadap body response,
                // lalu mengembalikan sebagai array

                $output = $response->toArray();

                // loop ke array yang dihasilkan, lalu outputkan masing-masing item

                /** @var array<array-key,mixed> $item */
                foreach ($output as $item) {
                    // @phpstan-ignore-next-line
                    yield $item;
                }

                // mengambil header link dari response

                $headers = $response->getHeaders();
                $linkHeader = $headers['link'][0] ?? null;

                // jika tidak ada header link, maka tidak ada halaman berikutnya

                if ($linkHeader === null) {
                    $currentEndpoint = null;
                    continue;
                }

                // parse header link, lalu ambil link rel=next

                $parsedLink = LinkHeader::fromString($linkHeader);
                $next = $parsedLink->getRel('next');

                // jika tidak ada link rel=next, maka tidak ada halaman berikutnya

                if (!$next) {
                    $currentEndpoint = null;
                    continue;
                }

                // jika ada link rel=next, maka ambil URI-nya sebagai endpoint yang
                // akan di-request selanjutnya

                $currentEndpoint = $next->getUri();
            } catch (\Throwable $e) {
                // jika terjadi error, bungkus exceptionnya dengan exception
                // kita, dengan menyertakan informasi endpoint terakhir yang
                // menyebabkan error. dengan informasi tersebut, kita bisa
                // melanjutkan iterasi dari endpoint terakhir tersebut

                throw new PaginationException(
                    message: sprintf('Failed to fetch data from endpoint "%s"', $currentEndpoint),
                    previous: $e
                );
            }
        }
    }
}
