<?php

use EditorJS\EditorJS;
use EditorJS\EditorJSException;

/**
 * Class SyntaxSugarTest
 *
 * Check simplified configuration structure
 */
class SyntaxSugarTest extends TestCase
{
    const CONFIGURATION_FILE = TESTS_DIR . "/samples/syntax-sugar.json";

    /**
     * Sample configuration
     */
    public $configuration;

    /**
     * Setup configuration
     */
    public function setUp(): void
    {
        $this->configuration = json_decode(file_get_contents(SyntaxSugarTest::CONFIGURATION_FILE), true);
    }

    public function testShortTypeField()
    {
        $data = json_decode('{"blocks":[{"type":"header","data":{"text":"CodeX <b>Editor</b>", "level": 2}}]}', true);

        $editor = new EditorJS($data, $this->configuration);
        $result = $editor->getBlocks();

        $this->assertEquals('CodeX Editor', $result[0]['data']['text']);
        $this->assertEquals(2, $result[0]['data']['level']);
    }

    public function testShortTypeFieldCanBeOnly()
    {
        $callable = function () {
            new EditorJS(
                json_decode('{"blocks":[{"type":"header","data":{"text":"CodeX <b>Editor</b>", "level": 5}}]}', true),
                $this->configuration
            );
        };

        $this->assertException($callable, EditorJSException::class, null, 'Option \'level\' with value `5` has invalid value. Check canBeOnly param.');
    }

    public function testShortIntValid()
    {
        new EditorJS(json_decode('{"blocks":[{"type":"subtitle","data":{"text": "string", "level": 1337}}]}', true), $this->configuration);
        $this->assertTrue(true);
    }

    public function testShortIntNotValid()
    {
        $callable = function () {
            new EditorJS(json_decode('{"blocks":[{"type":"subtitle","data":{"text": "test", "level": "string"}}]}', true), $this->configuration);
        };

        $this->assertException($callable, EditorJSException::class, null, 'Option \'level\' with value `string` must be integer');
    }

    public function testInvalidType()
    {
        $callable = function () {
            $invalid_configuration = json_decode('{"tools": {"header": {"title": "invalid_type"}}}', true);
            new EditorJS(json_decode('{"blocks":[{"type":"header","data":{"title": "test"}}]}', true), $invalid_configuration);
        };

        $this->assertException($callable, EditorJSException::class, null, 'Unhandled type `invalid_type`');
    }

    public function testMixedStructure()
    {
        $data = json_decode('{"time":1539180803359,"blocks":[{"type":"header","data":{"text":"<b>t</b><i>e</i><u>st</u>","level":2}}, {"type":"quote","data":{"text":"<b>t</b><i>e</i><u>st</u>","caption":"", "alignment":"left"}}]}', true);
        $editor = new EditorJS($data, $this->configuration);
        $result = $editor->getBlocks();

        $this->assertEquals(2, count($result));
        $this->assertEquals('test', $result[0]['data']['text']);
        $this->assertEquals('<b>t</b><i>e</i>st', $result[1]['data']['text']);
    }
}
