<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Schema\Tests\Validation;

use PSX\Schema\Validation\Field;
use PSX\Schema\Validation\Validator;
use PSX\Validate\Filter;

/**
 * ValidatorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testValidate()
    {
        $validator = $this->getValidator();
        $validator->validate('/id', 2);
    }

    /**
     * @expectedException \PSX\Schema\ValidationException
     */
    public function testValidateInvalid()
    {
        $validator = $this->getValidator();
        $validator->validate('/id', 4);
    }

    public function testValidateUnknown()
    {
        $validator = $this->getValidator();
        $validator->validate('/foo', 4);
    }

    public function testGetFields()
    {
        $fields    = [new Field('id', [new Filter\Length(1, 2)])];
        $validator = new Validator($fields);

        $this->assertEquals($fields, $validator->getFields());
    }

    protected function getValidator()
    {
        $fields = [
            new Field('id', [new Filter\Length(1, 2)]),
            new Field('title', [new Filter\Length(1, 2)]),
            new Field('author/name', [new Filter\Length(1, 2)]),
        ];

        return new Validator($fields);
    }
}