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

## Client Library

### Penggunaan

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

### Algoritma

Algoritma operasional HTTP client ini adalah sebagai berikut.

![Diagram proses](docs/proses.svg?raw=true "Title")

Catatan: Bagian pengecekan apakah token kedaluarsa sebenarnya boleh saja tidak
diimplementasikan. Konsekuensinya, library hanya dapat tahu token sudah
kedaluarsa setelah melakukan request ke server.

### Pembuatan Implementasi Dalam Bahasa Pemrograman Lain

Pada bahasa pemrograman lain seharusnya tidak sulit untuk mengimplementasikan
HTTP client ini. Untuk contoh, bisa melihat pada file
[ClarusHttpClient.php](./src/ClarusHttpClient.php) dan mengadaptasikan ke bahasa
pemrograman lain tersebut.

Untuk keperluan parsing JWT, bisa menggunakan library yang ada di bahasa
pemrograman yang digunakan. Daftar library bisa dilihat di
[jwt.io](https://jwt.io/libraries). Yang diperlukan untuk kasus ini adalah
library yang dapat melakukan `exp` check.

## Pagination

Ada endpoint yang memuat banyak data. Dalam kasus tersebut, biasanya akan
menggunakan sistem pagination. Satu halaman akan memberikan beberapa data,
beserta informasi halaman berikutnya. Dengan informasi tersebut, kita bisa
mengambil data selanjutnya sampai tidak ada data lagi.

Halaman berikut dapat diketahui melalui header `Link` dengan `rel=next`. Library
ini memberikan `PaginationIterator` yang dapat digunakan oleh client untuk
melakukan iterasi terhadap endpoint dengan sistem pagination.

### Penggunaan

Berikut contoh penggunaan `PaginationIterator`:

```php
$client = // client yang sudah diinisialisasi, lihat contoh di atas
$endpoint = '/api/foo/01902fd6-e555-47eb-4761-fc2e9e48b2b5';

$iterator = new PaginationIterator($client, $endpoint);

foreach ($iterator as $item) {
    // $item adalah data yang diterima dari endpoint berbentuk array
}
```

### Algoritma

Algoritma iterator pagination adalah sebagai berikut.

![Diagram proses](docs/pagination.svg?raw=true "Title")

### Pembuatan Implementasi Dalam Bahasa Pemrograman Lain

Untuk melakukan implementasi dalam bahasa pemrograman lain, diperlukan library
untuk melakukan parsing header link. Berikut adalah beberapa library yang dapat
digunakan untuk beberapa bahasa pemrograman populer:

* [parse-link-header](https://github.com/thlorenz/parse-link-header) (Javascript)
* [javax.ws.rs.core.Link](https://docs.oracle.com/javaee%2F7%2Fapi%2F%2F/javax/ws/rs/core/Link.html) (Java)
* [graviton/link-header-rel-parser](https://github.com/libgraviton/link-header-rel-parser) (PHP)
* [link-header-parser](https://github.com/jgarber623/link-header-parser-ruby) (Ruby)
* [linkheader-parser](https://pypi.org/project/linkheader-parser/) (Python)

## Lisensi

MIT
