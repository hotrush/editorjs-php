<?php

use EditorJS\ConfigLoader;
use EditorJS\EditorJS;
use EditorJS\EditorJSException;

/**
 * Class GeneralTest
 *
 * Check basic config and block data parsing functionality
 */
class GeneralTest extends TestCase
{
    const SAMPLE_VALID_DATA = '{"time":1537444483710,"blocks":[{"type":"header","data":{"text":"CodeX Editor","level":2}},{"type":"paragraph","data":{"text":"Привет. Перед вами наш обновленный редактор. На этой странице вы можете проверить его в действии — попробуйте отредактировать или дополнить материал. Код страницы содержит пример подключения и простейшей настройки."}}],"version":"2.0.3"}';

    const EMPTY_DATA = '';

    const CONFIGURATION_FILE = TESTS_DIR . "/samples/test-config.json";

    private $config = '';

    public function setUp(): void
    {
        $this->config = json_decode(file_get_contents(GeneralTest::CONFIGURATION_FILE), true);
    }

    public function testValidData()
    {
        new EditorJS(json_decode(GeneralTest::SAMPLE_VALID_DATA, true), $this->config);
        $this->assertTrue(true);
    }

    public function testEmptyArray()
    {
        $callable = function () {
            new EditorJS([], $this->config);
        };

        $this->assertException($callable, EditorJSException::class, null, 'Input array is empty');
    }

    public function testValidConfig()
    {
        new ConfigLoader(json_decode(file_get_contents(TESTS_DIR . "/samples/test-config.json"), true));
        $this->assertTrue(true);
    }

    public function testItemsMissed()
    {
        $callable = function () {
            new EditorJS(['foo' => 'bar'], $this->config);
        };

        $this->assertException($callable, EditorJSException::class, null, 'Field `blocks` is missing');
    }

    public function testInvalidBlock()
    {
        $callable = function () {
            new EditorJS(['blocks' => 'foo'], $this->config);
        };

        $this->assertException($callable, EditorJSException::class, null, 'Blocks is not an array');
    }

    public function testBlocksContent()
    {
        $callable = function () {
            new EditorJS(['blocks' => ['', '']], $this->config);
        };

        $this->assertException($callable, EditorJSException::class, null, 'Block must be an Array');
    }

    public function testNested()
    {
        $data = json_decode('{"blocks":[{"type":"table","data":{"header": {"description":"a table", "author": "codex"}, "rows": [["name", "age", "sex"],["Paul", "24", "male"],["Ann", "26", "female"]]}}]}', true);
        $editor = new EditorJS($data, $this->config);
        $result = $editor->getBlocks();

        $valid_rows = [["name", "age", "sex"],["Paul", "24", "male"],["Ann", "26", "female"]];

        $this->assertEquals('a table', $result[0]['data']['header']['description']);
        $this->assertEquals('codex', $result[0]['data']['header']['author']);
        $this->assertEquals(3, count($result[0]['data']['rows']));

        $this->assertEquals('name', $result[0]['data']['rows'][0][0]);
        $this->assertEquals('24', $result[0]['data']['rows'][1][1]);
        $this->assertEquals('female', $result[0]['data']['rows'][2][2]);
    }
}
