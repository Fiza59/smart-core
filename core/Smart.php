<?php

namespace core;

class Smart
{
    public function run()
    {
        $reader = new ConfigService();
        $gridRows = $reader->get(CONFIGS_ROOT . '/grid.json')->parse();

        $system = $reader->get(CONFIGS_ROOT . '/system.json')->parse();
        $dashboard = $reader->get(CONFIGS_ROOT . '/dashboard.json')->parse();

        $context = new ContextService();
        $context = $context->createContext($gridRows, $system, $dashboard);

        $less = new CompilerService();
        $less->compile($context)->saveCode();

        try {
            $view = new View($context);
            $view->load('@core/index.twig')->assign(['context' => $context]);
        } catch (\Twig_Error_Loader $e) {
        } catch (\Twig_Error_Runtime $e) {
        } catch (\Twig_Error_Syntax $e) {
        } catch (\Throwable $e) {
        }
    }
}