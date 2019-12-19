<?php

namespace Nullform\TGStatClient\Params;

use Nullform\TGStatClient\Exceptions\InvalidParamsException;
use Nullform\TGStatClient\Exceptions\EmptyRequiredParamsException;

/**
 * Base class for parameters.
 *
 * @package Nullform\TGStatClient\Params
 */
abstract class AbstractParams
{
    /**
     * Parameters as query string.
     *
     * @return string
     * @uses http_build_query()
     */
    public function toString(): string
    {
        return http_build_query($this);
    }

    /**
     * Parameters as associative array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return (array)$this;
    }

    /**
     * Parameters as JSON string.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Check if required parameters are passed.
     *
     * @param array $params Required parameters.
     * @throws EmptyRequiredParamsException
     */
    public function checkRequiredParams(array $params): void
    {
        foreach ($params as $param) {
            if (is_null($this->{$param})) {
                throw new EmptyRequiredParamsException('Empty required parameter: ' . $param);
            }
        }
    }

    /**
     * Check 'limit' and 'offset' parameters.
     *
     * Method operates with the constants MAX_LIMIT and MAX_OFFSET.
     *
     * @throws InvalidParamsException
     */
    public function checkLimitAndOffset(): void
    {
        if (defined('static::MAX_LIMIT')) {
            if (!empty($this->limit)) {
                $this->limit = (int)$this->limit;
                if ($this->limit > static::MAX_LIMIT) {
                    throw new InvalidParamsException('Max limit: ' . static::MAX_LIMIT);
                }
            }
        }
        if (defined('static::MAX_OFFSET')) {
            if (!empty($this->offset)) {
                $this->offset = (int)$this->offset;
                if ($this->offset > static::MAX_OFFSET) {
                    throw new InvalidParamsException('Max offset: ' . static::MAX_OFFSET);
                }
            }
        }
    }
}