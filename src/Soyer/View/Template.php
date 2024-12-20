<?php

    /**
     * Copyright 2023 mantvmass
     * 
     * 
     */


    namespace Soyer\View;

    use Soyer\View\TemplateFileSystemLoader;
    use Twig\Environment;
    use Twig\TwigFunction;
    use Soyer\View\Custom\Functions;
    use Soyer\View\Custom\UserCustomView;
    // use Twig\Extra\Intl\IntlExtension; // remove


    /**
     * This is class store function for twig engine
     */
    class Template {


        /**
         * This is function create 
         * 
         * @param string $dir
         */
        public static function create($dir) {
            $loader = new TemplateFileSystemLoader($dir);
            $twig = new Environment($loader, []);

            // add custom function as a Twig function //
            $twig -> addFunction(new TwigFunction('asset', [Functions::class, 'generateAssetUrl']));
            $twig -> addFunction(new TwigFunction('is_nav', [Functions::class, 'isNavActive']));

            // add global variables as a Twig variable //
            $twig -> addGlobal('env', $_ENV);

            // add extension
            // $twig -> addExtension(new IntlExtension()); // remove

            // load user custom view
            foreach (UserCustomView::getGlobalVariables() as $key => $value) {
                $twig -> addGlobal($value["name"], $value["value"]);
            }

            foreach (UserCustomView::getFunctions() as $key => $value) {
                $twig -> addFunction(new TwigFunction($value["name"], $value["callable"]));
            }

            return $twig;
        }

    }