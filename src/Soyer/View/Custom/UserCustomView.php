<?php

    /**
     * Copyright 2023 mantvmass
     * 
     * 
     */
    

    namespace Soyer\View\Custom;


    class UserCustomView {

        protected static $variables = [];

        protected static $functions = [];

        public static function defineGlobalVariable(string $name, $value){
            array_push(
                self::$variables,
                ["name" => $name, "value" => $value]
            );
        }

        public static function defineFunction(string $name, $callable = null){
            array_push(
                self::$functions,
                ["name" => $name, "callable" => $callable]
            );
        }

        public static function getGlobalVariables(): array {
            return self::$variables;
        }

        public static function getFunctions(): array {
            return self::$functions;
        }


    }