<?php

use EditorJS\EditorJS;
use EditorJS\EditorJSException;

/**
 * Class BlockHandlerTest
 *
 * Check block data sanitizing functionality
 */
class BlockHandlerTest extends TestCase
{
    const SAMPLE_VALID_DATA = '{"time":1537444483710,"blocks":[{"type":"header","data":{"text":"CodeX Editor","level":2}},{"type":"paragraph","data":{"text":"Привет. Перед вами наш обновленный редактор. На этой странице вы можете проверить его в действии — попробуйте отредактировать или дополнить материал. Код страницы содержит пример подключения и простейшей настройки."}}],"version":"2.0.3"}';
    const CONFIGURATION_FILE = TESTS_DIR . "/samples/test-config.json";

    /**
     * Sample configuration
     */
    public $configuration;

    /**
     * Setup configuration
     */
    public function setUp(): void
    {
        $this->configuration = json_decode(file_get_contents(BlockHandlerTest::CONFIGURATION_FILE), true);
    }

    public function testLoad()
    {
        new EditorJS(json_decode(BlockHandlerTest::SAMPLE_VALID_DATA, true), $this->configuration);
        $this->assertTrue(true);
    }

    public function testSanitizing()
    {
        $data = json_decode('{"blocks":[{"type":"header","data":{"text":"CodeX <b>Editor</b>", "level": 2}}]}', true);

        $editor = new EditorJS($data, $this->configuration);
        $result = $editor->getBlocks();

        $this->assertEquals('CodeX Editor', $result[0]['data']['text']);
    }

    public function testSanitizingAllowedTags()
    {
        $data = json_decode('{"blocks":[{"type":"paragraph","data":{"text":"<a>CodeX</a> <b>Editor</b> <a href=\"https://ifmo.su\">ifmo.su</a>"}}]}', true);

        $editor = new EditorJS($data, $this->configuration);
        $result = $editor->getBlocks();

        $this->assertEquals('<a>CodeX</a> <b>Editor</b> <a href="https://ifmo.su" target="_blank" rel="noreferrer noopener">ifmo.su</a>', $result[0]['data']['text']);
    }

    public function testCanBeOnly()
    {
        $callable = function () {
            new EditorJS(json_decode('{"blocks":[{"type":"header","data":{"text":"test","level":5}}]}', true), $this->configuration);
        };

        $this->assertException($callable, EditorJSException::class, null, 'Option \'level\' with value `5` has invalid value. Check canBeOnly param.');
    }

    public function testListTool()
    {
        $data = json_decode('{"time":1539180803359,"blocks":[{"type":"list","data":{"style":"ordered","items":["first","second","third"]}}],"version":"2.1.1"}', true);
        $editor = new EditorJS($data, $this->configuration);
        $result = $editor->getBlocks();

        $this->assertEquals(3, count($result[0]['data']['items']));
        $this->assertEquals("first", $result[0]['data']['items'][0]);
        $this->assertEquals("second", $result[0]['data']['items'][1]);
        $this->assertEquals("third", $result[0]['data']['items'][2]);
    }
}
