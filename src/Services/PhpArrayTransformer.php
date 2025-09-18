<?php

namespace AbdelhamidErrahmouni\ChartBuilder\Services;

class PhpArrayTransformer
{
	public static function toJsLiteral(mixed $value): string
	{
		if (is_array($value)) {
			$isAssoc = array_keys($value) !== range(0, count($value) - 1);

			if ($isAssoc) {
				$parts = [];
				foreach ($value as $key => $val) {
					$parts[] = "'" . self::escapeJsString((string) $key) . "': " . self::toJsLiteral($val);
				}

				return '{' . implode(', ', $parts) . '}';
			}

			$parts = array_map(fn ($v) => self::toJsLiteral($v), $value);

			return '[' . implode(', ', $parts) . ']';
		}

		if (is_string($value)) {
			if (self::looksLikeJsFunction($value)) {
				return $value;
			}

			return "'" . self::escapeJsString($value) . "'";
		}

		if (is_bool($value)) {
			return $value ? 'true' : 'false';
		}

		if (is_null($value)) {
			return 'null';
		}

		if (is_int($value) || is_float($value)) {
			return (string) $value;
		}

		if ($value instanceof \JsonSerializable) {
			return self::toJsLiteral($value->jsonSerialize());
		}

		return "'" . self::escapeJsString((string) $value) . "'";
	}

	private static function escapeJsString(string $value): string
	{
		// Escape backslashes and single quotes, normalize newlines
		$value = str_replace(["\\", "'"], ["\\\\", "\\'"], $value);
		$value = str_replace(["\r\n", "\n", "\r"], ["\\n", "\\n", "\\n"], $value);

		return $value;
	}

	private static function looksLikeJsFunction(string $value): bool
	{
		$value = trim($value);

		// Matches: `function (...) { ... }`, `(...) => ...`, `param => ...`
		return preg_match('/^\s*function\s*\(/', $value) === 1
			|| preg_match('/^\s*\([^)]*\)\s*=>/', $value) === 1
			|| preg_match('/^\s*[A-Za-z_$][A-Za-z0-9_$]*\s*=>/', $value) === 1;
	}
}