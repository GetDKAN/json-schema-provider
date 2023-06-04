<?php

namespace JsonSchemaProvider;

use Contracts\RetrieverInterface;
use JsonSchema\Validator;

class Provider implements RetrieverInterface
{

    private RetrieverInterface $schemaStorage;

    public function __construct(RetrieverInterface $schema_storage)
    {
        $this->schemaStorage = $schema_storage;
    }

    public function retrieve(string $id): ?string
    {
        try {
            $schema_string = $this->schemaStorage->retrieve($id);
        } catch (\Exception $e) {
            throw new \Exception("A schema {$id} does not exist.");
        }

        $schema = json_decode($schema_string);
        if (!$schema) {
            throw new \Exception("The requested schema is not valid JSON");
        }


        if (!$this->schemaIsObjectAndContainsMetaSchema($schema)) {
            throw new \Exception(
                'The requested schemas is not an object with a reference to ' .
                'a valid meta-schema (ex. { "$schema": "http://json-schema.org/draft-07/schema#" })'
            );
        }

        if (!$this->validSchema($schema)) {
            throw new \Exception("The requested schema is not valid" . PHP_EOL . $this->getSchemaErrors($schema));
        }

        return $schema_string;
    }

    private function schemaIsObjectAndContainsMetaSchema($schema): bool
    {
        return (
            is_object($schema)
            && isset($schema->{'$schema'})
            && substr_count($schema->{'$schema'}, "http://json-schema.org/draft-") > 0
        );
    }

    private function validateSchema($schema): Validator
    {
        $meta_schema_url = $schema->{'$schema'};

        $validator = new Validator();
        $validator->validate($schema, (object)['$ref' => $meta_schema_url]);

        return $validator;
    }

    private function validSchema($schema)
    {
        $validator = $this->validateSchema($schema);
        return $validator->isValid();
    }

    private function getSchemaErrors($schema)
    {
        $validator = $this->validateSchema($schema);
        $errors = "";
        foreach ($validator->getErrors() as $error) {
            $errors .= sprintf("[%s] %s\n", $error['property'], $error['message']) . PHP_EOL;
        }
        return $errors;
    }
}
