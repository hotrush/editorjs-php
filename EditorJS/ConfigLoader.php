<?php

namespace EditorJS;

/**
 * Class ConfigLoader
 *
 * @package EditorJS
 */
class ConfigLoader
{
    public array $tools = [];

    /**
     * ConfigLoader constructor
     *
     * @param array $config – configuration data
     *
     * @throws EditorJSException
     */
    public function __construct(array $config)
    {
        if (empty($config)) {
            throw new EditorJSException("Configuration data is empty");
        }

        $this->loadTools($config);
    }

    /**
     * Load settings for tools from configuration
     *
     * @param array $config
     *
     * @throws EditorJSException
     */
    private function loadTools(array $config): void
    {
        if (!isset($config['tools'])) {
            throw new EditorJSException('Tools not found in configuration');
        }

        foreach ($config['tools'] as $toolName => $toolData) {
            if (isset($this->tools[$toolName])) {
                throw new EditorJSException("Duplicate tool $toolName in configuration");
            }

            $this->tools[$toolName] = $this->loadTool($toolData);
        }
    }

    /**
     * Load settings for tool
     *
     * @param array $data
     *
     * @return array
     */
    private function loadTool(array $data): array
    {
        return $data;
    }
}
