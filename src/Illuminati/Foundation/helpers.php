<?php

    use Illuminati\View\Template;
    use Illuminati\View\TemplateFileSystemLoader;

    if (!function_exists('render_template')) {
        /**
         * This function render_template
         *
         * @param  string  $name
         * @param  array|null  $$context
         */

        function render_template($name, array $context = []){
            $loader = new TemplateFileSystemLoader($GLOBALS["templates_path"]);
            $page = new Template($loader);
            return $page -> render($name, $context);
        }
    }

    if (!function_exists('redirect')) {
        /**
         * Get an instance of the redirector.
         *
         * @param  string|null  $to
         * @param  int  $status
         */

        function redirect($to = null, $status = 302){
            return header("Location: $to", true, $status);
        }
    }