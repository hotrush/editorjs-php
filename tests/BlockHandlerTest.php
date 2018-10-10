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
    public function setUp()
    {
        $this->configuration = file_get_contents(BlockHandlerTest::CONFIGURATION_FILE);
    }

    public function testLoad()
    {
        new EditorJS(BlockHandlerTest::SAMPLE_VALID_DATA, $this->configuration);
    }

    public function testSanitizing()
    {
        $data = '{"blocks":[{"type":"header","data":{"text":"CodeX <b>Editor</b>", "level": 2}}]}';

        $editor = new EditorJS($data, $this->configuration);
        $result = $editor->getBlocks();

        $this->assertEquals('CodeX Editor', $result[0]['data']['text']);
    }

    public function testSanitizingAllowedTags()
    {
        $data = '{"blocks":[{"type":"paragraph","data":{"text":"<a>CodeX</a> <b>Editor</b> <a href=\"https://ifmo.su\">ifmo.su</a>"}}]}';

        $editor = new EditorJS($data, $this->configuration);
        $result = $editor->getBlocks();

        $this->assertEquals('<a>CodeX</a> <b>Editor</b> <a href="https://ifmo.su" target="_blank" rel="noreferrer noopener">ifmo.su</a>', $result[0]['data']['text']);
    }

    public function testCanBeOnly()
    {
        $callable = function () {
            new EditorJS('{"blocks":[{"type":"header","data":{"text":"test","level":5}}]}', $this->configuration);
        };

        $this->assertException($callable, EditorJSException::class, null, 'Option \'level\' with value `5` has invalid value. Check canBeOnly param.');
    }
}