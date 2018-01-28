<?php

namespace core;

class CompilerService
{
    /** @var string $cssCode */
    private $cssCode = '';
    /** @var string $jsCode */
    private $jsCode = '';

    private $context;

    public function compile($context)
    {
        $this->context = $context;
        $this->compileLessCode();
        $this->compileJsCode();

        return $this;
    }

    private function compileLessCode()
    {
        $less = new \lessc();

        $less->setFormatter("compressed");

        $lessList = [
            RESOURCE_DIR . '/less/all.less',
            RESOURCE_DIR . '/less/reset.css'
        ];

        $lessList = array_merge($lessList, $this->collectModuleLessCode());

        try {
            foreach ($lessList as $file) {
                $this->cssCode .= $less->compileFile($file);
            }
        } catch (\Exception $e) {
        }
    }

    private function collectModuleLessCode()
    {
        $modules = $this->context['modules'];
        $list = [];

        foreach ($modules as $module) {
            if (is_file(MODULES_ROOT . '/' . $module . '/src/less/all.less')) {
                $list[] = MODULES_ROOT . '/' . $module . '/src/less/all.less';
            }
        }

        return $list;
    }

    private function compileJsCode()
    {
        $jsList = [
            RESOURCE_DIR . '/js/jquery.min.js',
            RESOURCE_DIR . '/js/artyom.min.js',
            RESOURCE_DIR . '/js/app.js'
        ];

        $jsList = array_merge($jsList, $this->collectModuleJsCode());

        try {
            foreach ($jsList as $js) {
                if (is_file($js)) {
                    $code = file_get_contents($js);

                    if (!empty($code)) {
                        $this->jsCode .= \JShrink\Minifier::minify($code);
                    }
                }
            }
        } catch (\Exception $e) {
        }
    }

    private function collectModuleJsCode()
    {
        $modules = $this->context['modules'];
        $list = [];

        foreach ($modules as $module) {
            $files = array_diff(scandir(MODULES_ROOT . '/' . $module . '/src/js'), array('.', '..', 'app.js', 'intents.js'));
            foreach ($files as $file) {
                $list[] = MODULES_ROOT . '/' . $module . '/src/js/' . $file;
            }

            if (is_file(MODULES_ROOT . '/' . $module . '/src/js/intents.js')) {
                $list[] = MODULES_ROOT . '/' . $module . '/src/js/intents.js';
            }

            if (is_file(MODULES_ROOT . '/' . $module . '/src/js/app.js')) {
                $list[] = MODULES_ROOT . '/' . $module . '/src/js/app.js';
            }
        }

        return $list;
    }

    public function saveCode()
    {
        if (!is_file(RESOURCE_DIR . '/_output/web.css')) {
            fopen(RESOURCE_DIR . '/_output/web.css', 'w');
        }

        file_put_contents(RESOURCE_DIR . '/_output/web.css', $this->cssCode);

        if (!is_file(RESOURCE_DIR . '/_output/web.js')) {
            fopen(RESOURCE_DIR . '/_output/web.js', 'w');
        }

        file_put_contents(RESOURCE_DIR . '/_output/web.js', $this->jsCode);
    }
}