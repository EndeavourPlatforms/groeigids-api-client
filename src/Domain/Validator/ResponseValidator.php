<?php

namespace Endeavour\GroeigidsApiClient\Domain\Validator;

use Endeavour\GroeigidsApiClient\Domain\Exception\InvalidJsonSchemaException;
use Endeavour\GroeigidsApiClient\Domain\Exception\ObjectTypeNotSupportedException;
use Endeavour\GroeigidsApiClient\Domain\HttpClient\ResponseData\PageableObject;
use Endeavour\GroeigidsApiClient\Domain\HttpClient\ResponseData\PageArticle;
use Endeavour\GroeigidsApiClient\Domain\HttpClient\ResponseData\SortObject;
use Endeavour\GroeigidsApiClient\Domain\HttpClient\ResponseData\Themes;
use Endeavour\GroeigidsApiClient\Domain\Model\Article;
use Endeavour\GroeigidsApiClient\Domain\Model\ArticleChild;
use Opis\JsonSchema\Validator;

class ResponseValidator implements ResponseValidatorInterface
{
    public function __construct(protected Validator $validator)
    {
        $this->validator
            ->resolver()
            ->registerFile('http://example.com/article.json', __DIR__ . '/../Schema/ArticleSchema.json')
            ->registerFile('http://example.com/childarticle.json', __DIR__ . '/../Schema/ChildArticleSchema.json')
            ->registerFile('http://example.com/pageableobject.json', __DIR__ . '/../Schema/PageableObjectSchema.json')
            ->registerFile('http://example.com/pagearticle.json', __DIR__ . '/../Schema/PageArticleSchema.json')
            ->registerFile('http://example.com/sortobject.json', __DIR__ . '/../Schema/SortObjectSchema.json')
            ->registerFile('http://example.com/themes.json', __DIR__ . '/../Schema/ThemesSchema.json')
        ;
    }

    /**
     * @param class-string $classString
     * @throws InvalidJsonSchemaException
     */
    public function validateData(string $classString, string $data): void
    {
        $json = json_decode($data);
        $schemaName = $this->getSchemaNameByClassType($classString);

        $result = $this->validator->validate($json, $schemaName);

        if (! $result->isValid()) {
            throw new InvalidJsonSchemaException(sprintf("Data does not match schema: %s", $classString));
        }
    }

    /**
     * @param class-string $classString
     */
    protected function getSchemaNameByClassType(string $classString): string
    {
        return match ($classString) {
            Article::class => 'http://example.com/article.json',
            ArticleChild::class => 'http://example.com/childarticle.json',
            PageableObject::class => 'http://example.com/pageableobject.json',
            PageArticle::class => 'http://example.com/pagearticle.json',
            SortObject::class => 'http://example.com/sortobject.json',
            Themes::class => 'http://example.com/themes.json',
            default => throw new ObjectTypeNotSupportedException('Invalid class type provided to build schema'),
        };
    }
}