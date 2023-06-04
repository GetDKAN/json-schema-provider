<?php

namespace JsonSchemaProviderTest;

use PHPUnit\Framework\TestCase;
use Swaggest\JsonSchema\Schema;
use JsonSchemaProvider\Provider;

class ProviderTest extends TestCase
{

    public function testBasJsonSchema(): void
    {
        $provider = new Provider(new TestSchemaRetriever());

        $this->expectExceptionMessage("The requested schema is not valid JSON");

        $provider->retrieve("badjson");
    }

    public function testNotObjectSchema(): void
    {
        $provider = new Provider(new TestSchemaRetriever());

        $this->expectExceptionMessage(
            'The requested schemas is not an object with a reference to a valid ' .
            'meta-schema (ex. { "$schema": "http://json-schema.org/draft-07/schema#" })'
        );

        $provider->retrieve("string");
    }

    public function testNoMetaSchemaSchema(): void
    {
        $provider = new Provider(new TestSchemaRetriever());

        $this->expectExceptionMessage(
            'The requested schemas is not an object with a reference to a valid ' .
            'meta-schema (ex. { "$schema": "http://json-schema.org/draft-07/schema#" })'
        );

        $provider->retrieve("nometa");
    }

    public function testBadMetaSchemaSchema(): void
    {
        $provider = new Provider(new TestSchemaRetriever());

        $this->expectExceptionMessage(
            'The requested schemas is not an object with a reference to a valid ' .
            'meta-schema (ex. { "$schema": "http://json-schema.org/draft-07/schema#" })'
        );

        $provider->retrieve("badmeta");
    }

    public function testInvalidAgainstMetaSchema(): void
    {
        $provider = new Provider(new TestSchemaRetriever());

        $this->expectExceptionMessage('The requested schema is not valid');

        $provider->retrieve("invalidagainstmeta");
    }

    public function testValidSchema(): void
    {
        $provider = new Provider(new TestSchemaRetriever());

        $schema = <<<'JSON'
{
  "$schema": "http://json-schema.org/draft-07/schema#",
  "type": "object",
  "properties": {}
}
JSON;


        $this->assertEquals($schema, $provider->retrieve("valid"));
    }
}
