<?php

    /**
     * Copyright 2023 mantvmass
     * 
     * 
     */
    

    namespace Soyer\View\Custom;


    use Soyer\Http\Request;


    /**
     * This is class store function for twig engine
     */
    class Functions {


        /**
         * generateAssetUrl function
         * 
         * @param string $static
         * @param string $assetPath
         * @return string
         */
        public static function generateAssetUrl(string $static, string $assetPath): string {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
            $domain = $protocol . $_SERVER['HTTP_HOST'];
            return rtrim($domain, '/') . '/' . $static . '/' . ltrim($assetPath, '/');
        }


        /**
         * isNavActive function
         * 
         * @param string $path
         * @return string
         */
        public static function isNavActive(string $path, string $result = "active"): string {
            return ($path == Request::$path ? $result : "");
        }

    }

?>