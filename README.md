# Groeigids API client
The Groeigids API Client is a PHP library designed to interact with the Elkander Groeigids API,
making it easy to fetch and manage articles and themes.
Swagger documentation of the API can be found [here](https://groeigids-api.elkander.nl/swagger-ui/index.html).

## Features
- Fetch articles and themes from the Groeigids API
- JSON schema validation for API responses
- Support for query parameters and sorting
- Custom exceptions for error handling

## Requirements
- PHP 8.1+
- Composer
- A valid API key for the Groeigids API
- A PSR-18 compatible HTTP client

## Installation
To install the Groeigids API Client, use Composer:
```bash
composer require endeavour/groeigids-api-client
```

## Configuration
To run integration tests, a .env file should be created in the root of the project with the following content:
```dotenv
GROEIGIDS_API_KEY=your-api-key-here
```

## Usage
To use the Groeigids API Client, create an instance of the `GroeigidsApiClient` class:
```php
use Endeavour\GroeigidsApiClient\Infrastructure\HttpClient\GroeigidsClient;

$client = new GroeigidsClient(getenv('GROEIGIDS_API_KEY'));
```
Now fetching articles and themes is as simple as calling the `fetchArticles` and `fetchThemeArticles` methods:
```php
$articles = $client->fetchArticles();
$themes = $client->fetchThemeArticles();
```
The `fetchArticles` and `fetchThemeArticles` methods accept an optional array of query parameters:
```php
$articles = $client->fetchArticles(page: 1, size: 10);
$themes = $client->fetchThemeArticles(page: 2, size: 50);
```
The `fetchArticles` and `fetchThemeArticles` methods also accept an optional TypedArray object containing sorting parameters:
```php
$sortParameters = new TypedArray(SortParameter::class, [
    new SortParameter('title', 'asc'),
    new SortParameter('order', 'desc')
]);
$articles = $client->fetchArticles(sort: $sortParameters);
```
Other functions are available to fetch articles by ID, by breadcrumb or those modified after a certain date:
```php
$articleById = $client->fetchArticle(1);
$articleByBreadcrumb = $client->fetchArticleByBreadcrumb('article-breadcrumb');
$articlesAfterDate = $client->fetchModfifiedArticlesAfterDate(new DateTime('2020-01-01'))
```

## Testing
To run the unit tests, use PHPUnit (make sure a .env file is present in the root of the project):
```bash
make up
make test
```

## Contributing
Contributions are welcome!

## Licence
This project is licensed under the MIT License.
