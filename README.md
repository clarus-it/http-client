# clarus-it/http-client

Contoh HTTP client untuk keperluan mengakses API ke aplikasi-aplikasi buatan
Clarus IT.

## Contoh Penggunaan

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
// tokennya sudah kadaluwarsa
$response = $client->request('GET', 'ping');

// mendapatkan hasilnya sebagai array
$result = $response->toArray();
```

`ClarusHttpClient` mengimplementasikan Symfony `HttpClientInterface`, jadi bisa
digunakan dengan cara yang sama dengan Symfony HttpClient.

## Algoritma

![Diagram proses](docs/proses.png?raw=true "Title")

## Bahasa Pemrograman Lain

Pada bahasa pemrograman lain seharusnya tidak sulit untuk mengimplementasikan
HTTP client ini. Untuk contoh, bisa melihat pada file
[ClarusHttpClient.php](./src/ClarusHttpClient.php) dan mengadaptasikan ke bahasa
pemrograman lain tersebut.

## License

MIT
