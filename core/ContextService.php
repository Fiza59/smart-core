<?php

namespace core;

class ContextService
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