# clarus-it/http-client

Library HTTP client dalam bahasa pemrograman PHP untuk keperluan mengakses API
ke aplikasi-aplikasi buatan Clarus IT. Library ini berlaku sebagai reference
implementation dan contoh bagi yang perlu membuat implementasi untuk bahasa
pemrograman lain.

## Instalasi

Untuk menginstall library ini, bisa menggunakan composer:

```bash
composer require clarus-it/http-client
```

## Penggunaan

Berikut contoh penggunaan `ClarusHttpClient`:

```php
// baseUri adalah base dari endpoint API, termasuk akhiran /api/
$baseUri = 'https://example.com/api/';

// apiKey adalah api key yang digenerate oleh aplikasi
$apiKey = '4e85a111bf36d87fea86f6452f5084a1bb7820b9605a18b217e3950eb5ea12c1';

// instantiasi objek ClarusHttpClient dengan parameter apiKey dan baseUri
$client = new ClarusHttpClient($apiKey, $baseUri);

// melakukan request GET ke https://example.com/api/ping
// client akan secara otomatis melakukan login apabila belum login, atau jika
// tokennya sudah kedaluarsa
$response = $client->request('GET', 'ping');

// mendapatkan hasilnya sebagai array
$result = $response->toArray();
```

`ClarusHttpClient` mengimplementasikan Symfony `HttpClientInterface`, jadi bisa
digunakan dengan cara yang sama dengan Symfony HttpClient.

## Algoritma

Algoritma operasional HTTP client ini adalah sebagai berikut.

![Diagram proses](docs/proses.svg?raw=true "Title")

Catatan: Bagian pengecekan apakah token kedaluarsa sebenarnya boleh saja tidak
diimplementasikan. Konsekuensinya, library hanya dapat tahu token sudah
kedaluarsa setelah melakukan request ke server.

## Pembuatan Implementasi Dalam Bahasa Pemrograman Lain

Pada bahasa pemrograman lain seharusnya tidak sulit untuk mengimplementasikan
HTTP client ini. Untuk contoh, bisa melihat pada file
[ClarusHttpClient.php](./src/ClarusHttpClient.php) dan mengadaptasikan ke bahasa
pemrograman lain tersebut.

Untuk keperluan parsing JWT, bisa menggunakan library yang ada di bahasa
pemrograman yang digunakan. Daftar library bisa dilihat di
[jwt.io](https://jwt.io/libraries). Yang diperlukan untuk kasus ini adalah
library yang dapat melakukan `exp` check.

## Lisensi

MIT
