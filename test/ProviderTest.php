<?php

namespace JsonSchemaProviderTest;

use Swaggest\JsonSchema\Schema;
use JsonSchemaProvider\Provider;

class ProviderTest extends \PHPUnit\Framework\TestCase
{

    public function testBasJsonSchema()
    {
        $provider = new Provider(new TestSchemaRetriever());

        $this->expectExceptionMessage("The requested schema is not valid JSON");

        $provider->retrieve("badjson");
    }

    public function testNotObjectSchema()
    {
        $provider = new \JsonSchemaProvider\Provider(new TestSchemaRetriever());

        $this->expectExceptionMessage(
            'The requested schemas is not an object with a reference to a valid ' .
            'meta-schema (ex. { "$schema": "http://json-schema.org/draft-07/schema#" })'
        );

        $provider->retrieve("string");
    }

    public function testNoMetaSchemaSchema()
    {
        $provider = new \JsonSchemaProvider\Provider(new TestSchemaRetriever());

        $this->expectExceptionMessage(
            'The requested schemas is not an object with a reference to a valid ' .
            'meta-schema (ex. { "$schema": "http://json-schema.org/draft-07/schema#" })'
        );

        $provider->retrieve("nometa");
    }

    public function testBadMetaSchemaSchema()
    {
        $provider = new \JsonSchemaProvider\Provider(new TestSchemaRetriever());

        $this->expectExceptionMessage(
            'The requested schemas is not an object with a reference to a valid ' .
            'meta-schema (ex. { "$schema": "http://json-schema.org/draft-07/schema#" })'
        );

        $provider->retrieve("badmeta");
    }

    public function testInvalidAgainstMetaSchema()
    {
        $provider = new \JsonSchemaProvider\Provider(new TestSchemaRetriever());

        $this->expectExceptionMessage('The requested schema is not valid');

        $provider->retrieve("invalidagainstmeta");
    }

    public function testValidSchema()
    {
        $provider = new \JsonSchemaProvider\Provider(new TestSchemaRetriever());

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
