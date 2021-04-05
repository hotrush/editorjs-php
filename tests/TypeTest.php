<?php

use EditorJS\EditorJS;
use EditorJS\EditorJSException;

/**
 * Class TypeTest
 *
 * Check basic types: integer, boolean
 */
class TypeTest extends TestCase
{
    const CONFIGURATION_FILE = TESTS_DIR . "/samples/type-test-config.json";
    const CONFIGURATION_FILE_REQUIRED = TESTS_DIR . "/samples/type-test-config-required.json";

    /**
     * Sample configuration
     */
    public $configuration;

    /**
     * Setup configuration
     */
    public function setUp(): void
    {
        $this->configuration = json_decode(file_get_contents(TypeTest::CONFIGURATION_FILE), true);
    }

    public function testBooleanFailed()
    {
        $callable_not_bool = function () {
            new EditorJS(json_decode('{"blocks":[{"type":"test","data":{"bool_test":"not boolean"}}]}', true), $this->configuration);
        };

        $this->assertException($callable_not_bool, EditorJSException::class, null, 'Option \'bool_test\' with value `not boolean` must be boolean');
    }

    public function testBooleanValid()
    {
        new EditorJS(json_decode('{"blocks":[{"type":"test","data":{"bool_test":true}}]}', true), $this->configuration);
        $this->assertTrue(true);
    }

    public function testIntegerValid()
    {
        new EditorJS(json_decode('{"blocks":[{"type":"test","data":{"int_test": 5}}]}', true), $this->configuration);
        $this->assertTrue(true);
    }

    public function testIntegerFailed()
    {
        $callable = function () {
            new EditorJS(json_decode('{"blocks":[{"type":"test","data":{"int_test": "not integer"}}]}', true), $this->configuration);
        };

        $this->assertException($callable, EditorJSException::class, null, 'Option \'int_test\' with value `not integer` must be integer');
    }

    public function testStringValid()
    {
        new EditorJS(json_decode('{"blocks":[{"type":"test","data":{"string_test": "string"}}]}', true), $this->configuration);
        $this->assertTrue(true);
    }

    public function testStringFailed()
    {
        $callable = function () {
            new EditorJS(json_decode('{"blocks":[{"type":"test","data":{"string_test": 17}}]}', true), $this->configuration);
        };

        $this->assertException($callable, EditorJSException::class, null, 'Option \'string_test\' with value `17` must be string');
    }

    public function testAllowedNullNotRequired()
    {
        new EditorJS(json_decode('{"blocks":[{"type":"test","data":{"int_test": null}}]}', true), $this->configuration);
        $this->assertTrue(true);
    }

    public function testDisallowedNullNotRequired()
    {
        $callable = function () {
            new EditorJS(json_decode('{"blocks":[{"type":"test","data":{"string_test": null}}]}', true), $this->configuration);
        };

        $this->assertException($callable, EditorJSException::class, null, 'string_test\' with value `` must be string');
    }

    public function testNullRequired()
    {
        new EditorJS(json_decode('{"blocks":[{"type":"test","data":{"string_test": "qwe"}}]}', true), json_decode(file_get_contents(TypeTest::CONFIGURATION_FILE_REQUIRED), true));

        $callable = function () {
            new EditorJS(json_decode('{"blocks":[{"type":"test","data":{"string_test": null}}]}', true), json_decode(file_get_contents(TypeTest::CONFIGURATION_FILE_REQUIRED), true));
        };
        $this->assertException($callable, EditorJSException::class, null, 'Not found required param `string_test`');
    }
}
