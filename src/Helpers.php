<?php

namespace Nullform\TGStatClient;

/**
 * Various helpers.
 *
 * @package Nullform\TGStatClient
 */
final class Helpers
{
    /**
     * Preparing a query consisting of several phrases to search for their EXACT matches.
     *
     * ---------
     *
     * Example
     *
     * For array `['John Doe', 'Bill Gates']` the result will be a string:
     *
     * ```
     * "John Doe" | "Bill Gates"
     * ```
     *
     * @param array $phrases
     * @return string
     */
    public static function extendedSyntaxStrictPhrases(array $phrases): string
    {
        $query = '';

        if (!empty($phrases)) {
            $phrases = array_map([self::class, 'extendedSyntaxCleanupString'], $phrases);

            $query = '"' . implode('" | "', $phrases) . '"';
        }

        return $query;
    }

    /**
     * Removing special characters used in extended syntax.
     *
     * @param string $string
     * @return string
     */
    public static function extendedSyntaxCleanupString(string $string): string
    {
        $search = ['(', ')', '|', '=', '"'];

        $string = str_replace($search, '', $string);
        $string = htmlspecialchars($string);

        return $string;
    }
}