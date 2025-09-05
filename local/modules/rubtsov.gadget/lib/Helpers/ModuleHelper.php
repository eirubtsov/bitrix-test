<?php

declare(strict_types=1);

namespace Rubtsov\Gadget\Helpers;

class ModuleHelper
{
    /**
     * @param string $absoluteModulePath
     * @return string|null
     */
    public static function getModulePathRelative(string $absoluteModulePath): ?string
    {
        // Нормализуем разделители
        $path = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $absoluteModulePath);

        // Ищем ключевые маркеры (local или bitrix)
        foreach (['local', 'bitrix'] as $marker) {
            $pos = stripos(
                $path,
                DIRECTORY_SEPARATOR . $marker . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR
            );
            if ($pos !== false) {
                // Вырезаем путь начиная с marker
                $relative = substr($path, $pos + 1); // +1, чтобы убрать ведущий слэш/бэкслэш
                // Оставляем только часть до названия модуля
                $parts = explode(DIRECTORY_SEPARATOR, $relative);
                if (count($parts) >= 3) {
                    return "/$parts[0]/$parts[1]/$parts[2]";
                }
            }
        }

        return null;
    }
}
