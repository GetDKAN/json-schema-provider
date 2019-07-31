<?php

namespace JsonSchemaProviderTest;

use Contracts\RetrieverInterface;

class TestSchemaRetriever implements RetrieverInterface
{
    public function retrieve(string $id): ?string
    {
        $file_path = __DIR__ . "/data/{$id}.json";
        if (file_exists($file_path)) {
            return file_get_contents($file_path);
        } else {
            throw new \Exception("Requested schema {$id} does not exist.");
        }
    }
}
