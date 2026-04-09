<?php
/**
 * Internacionalización (i18n)
 * Idiomas soportados: es (Español), en (English), zh (中文)
 * LogiSystem
 */

$GLOBALS['translations'] = [];

function loadLanguage(string $lang): void
{
    $supported = ['es', 'en', 'zh'];
    if (!in_array($lang, $supported, true)) {
        $lang = 'es';
    }
    $_SESSION['lang'] = $lang;
    $file = __DIR__ . '/../lang/' . $lang . '.php';
    if (file_exists($file)) {
        $GLOBALS['translations'] = require $file;
    }
}

/**
 * Traduce una clave con soporte para reemplazos :key
 */
function t(string $key, array $replace = []): string
{
    $value = $GLOBALS['translations'][$key] ?? $key;
    foreach ($replace as $k => $v) {
        $value = str_replace(':' . $k, (string) $v, $value);
    }
    return $value;
}

function currentLang(): string
{
    return $_SESSION['lang'] ?? 'es';
}
