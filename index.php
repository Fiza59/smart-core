<?php

require __DIR__ . '/vendor/autoload.php';

error_reporting(E_ALL);

define('APPLICATION_ROOT', __DIR__);
define('CONFIGS_ROOT', __DIR__ . '/configs');
define('MODULES_ROOT', __DIR__ . '/modules');
define('TEMPLATES_ROOT', __DIR__ . '/templates');
define('CACHE_ROOT', __DIR__ . '/var/cache');
define('RESOURCE_DIR', __DIR__ . '/resources');

$reader = new JSONReader();

$gridRows = $reader->get(CONFIGS_ROOT . '/grid.json')->parse();
$system = $reader->get(CONFIGS_ROOT . '/system.json')->parse();
$dashboard = $reader->get(CONFIGS_ROOT . '/dashboard.json')->parse();

$context = new Context();
$context = $context->createContext($gridRows, $system, $dashboard);

$system = new System($context);
$system->updateSystem();

$less = new Compiler();
$less->compile($context)->saveCode();

try {
    $view = new View($context);
    $view->load('@core/index.twig')->assign(['context' => $context]);
} catch (Twig_Error_Loader $e) {
} catch (Twig_Error_Runtime $e) {
} catch (Twig_Error_Syntax $e) {
} catch (Throwable $e) {
}

class System
{
    private $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    public function updateSystem()
    {
        $this->moduleUpdatesAvailable();
    }

    public function moduleUpdatesAvailable()
    {
        //https://github.com/frederikdengler/smart-clock/archive/master.zip
        foreach ($this->context['system']['modules'] as $module) {
            $url = 'https://github.com/frederikdengler/' . $module . '/archive/master.zip';
            $headers = @get_headers($url);
            if (strpos($headers[0], '404') === false) {

                $file = fopen($module . '.zip', "w+");

                if (flock($file, LOCK_EX)) {
                    fwrite($file, fopen($url, 'r'));
                    $zip = new ZipArchive;
                    $res = $zip->open($module . '.zip');
                    if ($res === TRUE) {
                        $zip->extractTo(MODULES_ROOT . '/' . $module);
                        $zip->close();
                    }

                    flock($file, LOCK_UN);
                }

            } else {
                //log errors
            }
        }
    }
}

class Context
{
    /** @var array $grid */
    protected $grid;
    /** @var array $system */
    protected $system;
    /** @var array $modules */
    protected $modules;
    /** @var array $dashboard */
    protected $dashboard;

    public function createContext($gridRows, $system, $dashboard)
    {
        $this->grid = $gridRows;
        $this->system = $system;
        $this->dashboard = $dashboard;
        $this->getUsedModules($gridRows);

        return $this->toArray($this);
    }

    private function getUsedModules($items)
    {
        $modules = [];
        foreach ($items['rows'] as $row) {
            foreach ($row as $item) {
                if (!in_array($item['module'], $modules)) {
                    $modules[] = $item['module'];
                }
            }
        }

        $this->modules = $modules;
    }

    public function toArray($data)
    {
        if (is_array($data) || is_object($data)) {
            $result = array();
            foreach ($data as $key => $value) {
                $result[$key] = $this->toArray($value);
            }
            return $result;
        }
        return $data;
    }
}

class JSONReader
{
    /** @var string $filePath */
    private $filePath;

    public function get($path)
    {
        $this->filePath = $path;
        return $this;
    }

    public function parse()
    {
        $content = file_get_contents($this->filePath);
        return json_decode($content, true);
    }
}

class View
{
    /** @var Twig_Environment $twig */
    private $twig;
    /** @var Twig_Template $template */
    private $template;

    private $templatePaths = [];

    private $context;

    /**
     * View constructor.
     * @throws Twig_Error_Loader
     */
    public function __construct($context)
    {
        $this->context = $context;
        $loader = new Twig_Loader_Filesystem();
        $loader->addPath(TEMPLATES_ROOT, 'core');

        $this->collectModuleTemplates();

        foreach ($this->templatePaths as $namespace => $path) {
            $loader->addPath($path, $namespace);
        }

        $this->twig = new Twig_Environment($loader, array(
            'cache' => CACHE_ROOT,
            'auto_reload' => true
        ));
    }

    private function collectModuleTemplates()
    {
        foreach ($this->context['modules'] as $module) {
            $this->templatePaths[$module] = MODULES_ROOT . '/' . $module . '/template';
        }
    }

    /**
     * @param $template
     * @return $this
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function load($template)
    {
        $this->template = $this->twig->load($template);
        return $this;
    }

    /**
     * @param array $variables
     * @throws Throwable
     */
    public function assign(array $variables)
    {
        echo $this->template->render($variables);
    }
}

class Compiler
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
        $less = new lessc;

        $lessList = [
            RESOURCE_DIR . '/less/all.less',
            RESOURCE_DIR . '/less/reset.css'
        ];

        $lessList = array_merge($lessList, $this->collectModuleLessCode());

        try {
            foreach ($lessList as $file) {
                $this->cssCode .= $less->compileFile($file);
            }
        } catch (Exception $e) {
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
        } catch (Exception $e) {
        }
    }

    private function collectModuleJsCode()
    {
        $modules = $this->context['modules'];
        $list = [];

        foreach ($modules as $module) {
            $files = array_diff(scandir(MODULES_ROOT . '/' . $module . '/src/js'), array('.', '..', 'app.js'));
            foreach ($files as $file) {
                $list[] = MODULES_ROOT . '/' . $module . '/src/js/' . $file;
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