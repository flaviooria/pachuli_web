<?php

namespace Pachuli\Web\Core;

use Exception;

class Config
{
    public static array $config;

    /**
     * Obtiene un array asociativo con la configuración establecida
     * @param string $key Nombre de la sección a obtener
     * @return array|null Array asociativo con las variables determinadas si la `$key` se encuentra en el fichero
     * de configuración, de otra manera null.
     * @since 0.1.0
     */
    private static function get(string $key): ?array
    {
        try {
            if (!self::$config) {
                self::load();
            }

            return self::$config[$key] ?? null;

        } catch (\Exception $e) {
            print($e->getMessage());
        }
    }

    /**
     * Carga las variables de configuración de la app
     * @throws Exception
     */
    private static function load(): void
    {
        $fileConfig = CONFIG . 'config.php';

        if (!file_exists(CONFIG)) {
            throw new Exception("File config not found");
        }

        self::$config = require $fileConfig;
    }
}