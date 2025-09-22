<?php
/**
 * Central text sanitization helper replacing deprecated FILTER_SANITIZE_STRING.
 * Usage: Sanitizer::text('fieldName');
 * Always escape again with htmlspecialchars() ONLY at output (views) to avoid double encoding.
 */
class Sanitizer
{
    /**
     * Sanitize a POST text field.
     * @param string $key POST key
     * @param int $maxLen Optional max length (multi‑byte safe)
     * @param bool $allowNewlines If true retain new lines
     * @return string
     */
    public static function text(string $key, int $maxLen = 255, bool $allowNewlines = false): string
    {
        if (!isset($_POST[$key])) {
            return '';
        }
        $value = $_POST[$key];
        if (is_array($value)) { // Prevent array injection
            return '';
        }
        $value = trim($value);
        // Strip tags (keep line breaks if allowed)
        $value = strip_tags($value);
        if (!$allowNewlines) {
            // Collapse internal newlines to space
            $value = preg_replace('/\r\n|\r|\n/', ' ', $value);
        } else {
            // Normalize line endings
            $value = preg_replace('/\r\n?|\n/', "\n", $value);
        }
        // Reduce multiple spaces
        $value = preg_replace('/\s{2,}/', ' ', $value);
        // Enforce length
        if (mb_strlen($value) > $maxLen) {
            $value = mb_substr($value, 0, $maxLen);
        }
        return $value;
    }
}
