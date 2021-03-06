<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Schema\Tests\Parser;

use PSX\Http;
use PSX\Schema\Parser\JsonSchema;
use PSX\Schema\SchemaInterface;

/**
 * JsonSchemaTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class JsonSchemaTest extends ParserTestCase
{
    public function testParse()
    {
        $schema = JsonSchema::fromFile(__DIR__ . '/JsonSchema/test_schema.json');

        $this->assertSchema($this->getSchema(), $schema);
    }

    /**
     * The offical json schema is recursive so we check whether we can parse it
     * without a problem
     */
    public function testParseRecursion()
    {
        $schema   = JsonSchema::fromFile(__DIR__ . '/JsonSchema/schema.json');
        $property = $schema->getDefinition();

        $this->assertInstanceOf('PSX\Schema\PropertyInterface', $property);
    }

    public function testParseSwagger()
    {
        $schema   = JsonSchema::fromFile(__DIR__ . '/JsonSchema/swagger.json');
        $property = $schema->getDefinition();

        $this->assertInstanceOf('PSX\Schema\PropertyInterface', $property);
    }

    public function testParseExternalResource()
    {
        $handler  = Http\Handler\Mock::getByXmlDefinition(__DIR__ . '/JsonSchema/http_mock.xml');
        $http     = new Http\Client($handler);
        $resolver = JsonSchema\RefResolver::createDefault($http);

        $parser   = new JsonSchema(__DIR__ . '/JsonSchema', $resolver);
        $schema   = $parser->parse(file_get_contents(__DIR__ . '/JsonSchema/test_schema_external.json'));
        $property = $schema->getDefinition();

        $this->assertInstanceOf('PSX\Schema\PropertyInterface', $property);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^Could not load json schema (.*)$/
     */
    public function testParseInvalidFile()
    {
        JsonSchema::fromFile(__DIR__ . '/JsonSchema/foo.json');
    }

    public function testParseInvalidVersion()
    {
        $schema = JsonSchema::fromFile(__DIR__ . '/JsonSchema/wrong_version_schema.json');

        $this->assertInstanceOf(SchemaInterface::class, $schema);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^Could not load external schema (.*)$/
     */
    public function testParseInvalidFileRef()
    {
        JsonSchema::fromFile(__DIR__ . '/JsonSchema/invalid_file_ref_schema.json');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Could not load external schema http://localhost/foo/bar#/definitions/bar received 404
     */
    public function testParseInvalidHttpRef()
    {
        $handler  = Http\Handler\Mock::getByXmlDefinition(__DIR__ . '/JsonSchema/http_mock.xml');
        $http     = new Http\Client($handler);
        $resolver = JsonSchema\RefResolver::createDefault($http);

        $parser   = new JsonSchema(__DIR__, $resolver);
        $parser->parse(file_get_contents(__DIR__ . '/JsonSchema/invalid_http_ref_schema.json'));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unknown protocol scheme foo
     */
    public function testParseInvalidSchemaRef()
    {
        JsonSchema::fromFile(__DIR__ . '/JsonSchema/unknown_protocol_ref_schema.json');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Could not resolve pointer /definitions/bar
     */
    public function testParseInvalidDocumentRef()
    {
        JsonSchema::fromFile(__DIR__ . '/JsonSchema/invalid_document_ref_schema.json');
    }

    public function testRecursiveSchema()
    {
        $schema   = JsonSchema::fromFile(__DIR__ . '/JsonSchema/recursive_schema.json');
        $property = $schema->getDefinition();

        $this->assertInstanceOf('PSX\Schema\PropertyInterface', $property);
        $this->assertInstanceOf('PSX\Schema\PropertyInterface', $property->getProperty('entry')->getItems());
    }
}
