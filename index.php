<?php

require __DIR__ . '/vendor/autoload.php';

error_reporting(E_ALL);

define('APPLICATION_ROOT', __DIR__);
define('CONFIGS_ROOT', __DIR__ . '/configs');
define('MODULES_ROOT', __DIR__ . '/modules');
define('TEMPLATES_ROOT', __DIR__ . '/templates');
define('CACHE_ROOT', __DIR__ . '/var/cache');

$reader = new JSONReader();

$gridItems = $reader->get(CONFIGS_ROOT . '/grid.json')->parse();
$system = $reader->get(CONFIGS_ROOT . '/system.json')->parse();

$context = new Context();
$context = $context->createContext($gridItems, $system['mode']);

$view = new View();
$view->load('@core/index.twig')->assign(['context' => $context]);


class Context
{
    /** @var array $grid */
    protected $grid;
    /** @var string $mode */
    protected $mode;
    /** @var array $modules */
    protected $modules;

    public function createContext($gridItems, $mode)
    {
        $this->grid = $gridItems;
        $this->mode = $mode;
        $this->getUsedModules($gridItems);

        return $this->toArray($this);
    }

    private function getUsedModules($items)
    {
        $modules = [];

        foreach ($items['items'] as $item) {
            if(!in_array($item['module'], $modules)) {
                $modules[] = $item['module'];
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

    public function __construct()
    {
        $loader = new Twig_Loader_Filesystem();
        $loader->addPath(TEMPLATES_ROOT, 'core');
        $loader->addPath(MODULES_ROOT . '/smart-clock/template', 'smart-clock');
        $this->twig = new Twig_Environment($loader, array(
            'cache' => CACHE_ROOT,
            'auto_reload' => true
        ));
    }

    public function load($template)
    {
        $this->template = $this->twig->load($template);
        return $this;
    }

    public function assign(array $variables)
    {
        echo $this->template->render($variables);
    }
}