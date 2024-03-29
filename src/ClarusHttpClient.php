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

use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\UnencryptedToken;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

final class ClarusHttpClient implements HttpClientInterface
{
    const TIME_COMPENSATION = 300;

    /**
     * API Key yang didapatkan dari aplikasi
     */
    private string $apiKey;

    /**
     * JWT token yang didapatkan melalui proses login di API
     */
    private ?string $token = null;

    /**
     * Instance HttpClient yang digunakan untuk melakukan request
     */
    private HttpClientInterface $client;

    /**
     * Parser untuk JWT token
     */
    private Parser $parser;

    /**
     * @param string $apiKey API Key yang didapat dari aplikasi
     * @param string $baseUrl Base URL, termasuk path /api/ di akhir, misalnya
     * https://example.com/api/
     * @param HttpClientInterface|null $client Jika menggunakan framework yang
     * sudah memiliki instance HttpClientInterface, bisa diisi di sini, jika
     * tidak ada juga tidak masalah.
     */
    public function __construct(
        string $apiKey,
        string $baseUrl,
        ?HttpClientInterface $client = null
    ) {
        $this->apiKey = $apiKey;
        $this->client = $client ?? HttpClient::create();
        $this->parser = new Parser(new JoseEncoder());

        $this->client = $this->client->withOptions(
            [
                'base_uri' => $baseUrl,
                'headers' => [
                    'user-agent' => 'ClarusHttpClient/0.5',
                ]
            ]
        );
    }

    /**
     * Melakukan request ke API. Jika mendapatkan response 401, maka akan
     * otomatis melakukan login dan mengulangi request.
     *
     * @param array<array-key,mixed> $options
     */
    public function request(
        string $method,
        string $url,
        array $options = []
    ): ResponseInterface {
        // jika belum memiliki token, lakukan login. jika login gagal, maka
        // method `login()` akan throw Exception, sehingga method ini pun akan
        // gagal dengan Exception tersebut.

        if ($this->token === null) {
            $this->login();
        }

        // jika token yang kita miliki sudah kedaluarsa atau sebentar lagi akan
        // kedaluarsa, maka kita lakukan login ulang.

        if ($this->isTokenExpired()) {
            $this->login();
        }

        // lakukan request ke API berdasarkan method, url, dan options yang
        // diberikan

        $response = $this->getAuthenticatedClient()->request($method, $url, $options);

        // jika status response adalah 401, maka token yang digunakan tidak
        // valid, maka kita melakukan login ulang, dan mengulangi request yang
        // sama.

        if (401 === $response->getStatusCode()) {
            $this->login();

            $response = $this->getAuthenticatedClient()->request($method, $url, $options);
        }

        return $response;
    }

    /**
     * HTTP client yang sudah dilengkapi dengan header Authorization berdasarkan
     * token yang sudah didapatkan
     */
    private function getAuthenticatedClient(): HttpClientInterface
    {
        if ($this->token === null) {
            throw new \LogicException('Token tidak tersedia, silakan lakukan login() terlebih dahulu');
        }

        return $this->client->withOptions(
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                ]
            ]
        );
    }

    /**
     * Melakukan login ke API untuk mendapatkan token. Token lalu disimpan
     * di property $token
     *
     * Jika login gagal, maka method ini akan throw Exception saat pemanggilan
     * `toArray()`
     */
    private function login(): void
    {
        $response = $this->client->request(
            'POST',
            'login',
            [
                'json' => [
                    'apikey' => $this->apiKey,
                ]
            ]
        );

        $data = $response->toArray();

        $token = $data['token'] ?? null;

        if (!is_string($token)) {
            throw new \RuntimeException('Token is not a string');
        }

        $this->token = $token;
    }

    /**
     * Menghitung apakah token yang kita miliki saat ini sudah kedaluarsa, atau
     * sebentar lagi kedaluarsa
     */
    private function isTokenExpired(): bool
    {
        // jika token tidak ada, maka dianggap sudah kedaluarsa

        if ($this->token === null || $this->token === '') {
            return true;
        }

        // parse token yang kita miliki

        $token = $this->parser->parse($this->token);

        // jika token yang kita miliki bukan instance dari UnencryptedToken,
        // maka dianggap sudah kedaluarsa

        if (!$token instanceof UnencryptedToken) {
            return true;
        }

        // ambil claims dari token

        $claims = $token->claims();

        // ambil nilai `exp` dari claims

        $exp = $claims->get('exp');

        // jika nilai `exp` tidak ada, atau bukan integer, maka dianggap sudah
        // kedaluarsa

        if (!is_int($exp)) {
            return true;
        }

        // jika waktu kedaluarsa dari token kurang dari waktu sekarang dikurangi
        // dengan TIME_COMPENSATION, maka dianggap sudah kedaluarsa. jadi jika
        // token expire pukul 10:00, maka token dianggap sudah kedaluarsa pukul
        // 09:55

        return $exp < time() - self::TIME_COMPENSATION;
    }

    /**
     * Tidak digunakan, silakan diabaikan saja
     */
    public function stream(
        ResponseInterface|iterable $responses,
        ?float $timeout = null
    ): ResponseStreamInterface {
        throw new \LogicException('Not implemented yet');
    }

    /**
     * Tidak digunakan, silakan diabaikan saja
     *
     * @param array<array-key,mixed> $options
     */
    public function withOptions(array $options): static
    {
        throw new \LogicException('Not implemented yet');
    }
}
