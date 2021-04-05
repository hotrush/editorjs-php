<?php

namespace EditorJS;

/**
 * Class EditorJS
 *
 * @package EditorJS
 */
class EditorJS
{
    public array $blocks = [];
    public array $config;
    public BlockHandler $handler;

    /**
     * EditorJS constructor.
     *
     * @param array $data
     * @param mixed $configuration
     *
     * @throws EditorJSException()
     */
    public function __construct(array $data, array $configuration)
    {
        $this->handler = new BlockHandler($configuration);

        /**
         * Count elements in data array
         */
        if (count($data) === 0) {
            throw new EditorJSException('Input array is empty');
        }

        /**
         * Check if blocks param is missing in data
         */
        if (!isset($data['blocks'])) {
            throw new EditorJSException('Field `blocks` is missing');
        }

        if (!is_array($data['blocks'])) {
            throw new EditorJSException('Blocks is not an array');
        }

        foreach ($data['blocks'] as $blockData) {
            if (is_array($blockData)) {
                array_push($this->blocks, $blockData);
            } else {
                throw new EditorJSException('Block must be an Array');
            }
        }

        /**
         * Validate blocks structure
         */
        $this->validateBlocks();
    }

    /**
     * Sanitize and return array of blocks according to the Handler's rules.
     *
     * @return array
     */
    public function getBlocks(): array
    {
        $sanitizedBlocks = [];

        foreach ($this->blocks as $block) {
            $sanitizedBlock = $this->handler->sanitizeBlock(
                $block['type'],
                $block['data'],
                $block['tunes'] ?? []
            );
            if (!empty($sanitizedBlock)) {
                array_push($sanitizedBlocks, $sanitizedBlock);
            }
        }

        return $sanitizedBlocks;
    }

    /**
     * Validate blocks structure according to the Handler's rules.
     *
     * @return bool
     */
    private function validateBlocks(): bool
    {
        foreach ($this->blocks as $block) {
            if (!$this->handler->validateBlock($block['type'], $block['data'])) {
                return false;
            }
        }

        return true;
    }
}
