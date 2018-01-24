<?php

namespace core;

class View
{

    /** @var \Twig_Environment $twig */
    private $twig;
    /** @var \Twig_Template $template */
    private $template;

    private $templatePaths = [];

    private $context;

    /**
     * View constructor.
     * @throws \Twig_Error_Loader
     */
    public function __construct($context)
    {
        $this->context = $context;
        $loader = new \Twig_Loader_Filesystem();
        $loader->addPath(TEMPLATES_ROOT, 'core');

        $this->collectModuleTemplates();

        foreach ($this->templatePaths as $namespace => $path) {
            $loader->addPath($path, $namespace);
        }

        $this->twig = new \Twig_Environment($loader, array(
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
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
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