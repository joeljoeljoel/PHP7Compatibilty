<?php

class Checker {
    const FOLDER = '/Users/joel/Documents/git/sites/';
    const PLUGIN_PATH = 'wp-content/plugins/';
    const OUTPUT = 'output.txt';
    protected $checked_plugins = [];

    public function check() {
        foreach (glob(self::FOLDER . '*', GLOB_ONLYDIR) as $project) {

            if (!$this->is_wpe_project($project)) {
                continue;
            }

            $plugin_dir = $project . '/' .  self::PLUGIN_PATH;

            $plugins = $this->list_plugins($plugin_dir);

            $output = basename($project) . "\r\n";
            $output .= shell_exec("php -d memory_limit=-1 vendor/bin/php7cc --level=error --except={$plugin_dir} {$project}");

            foreach ($plugins as $plugin) {
                $output .= shell_exec("php -d memory_limit=-1 vendor/bin/php7cc --level=error {$plugin_dir}{$plugin}");
                $this->checked_plugins[] = $plugin;
            }

            $output .= implode('', array_fill(0,30, '=')) . "\r\n\r\n";
            file_put_contents(self::OUTPUT, $output, FILE_APPEND);
            echo basename($project) . "\r\n";
        }
    }

    /**
     * Checks if the project located at $project is a WPEngine hosted project
     * @param string $project
     * @return bool
     */
    private function is_wpe_project($project) {
        $package = $project . '/' . 'package.json';
        if (!file_exists($package)) {
            return false;
        }

        $json = json_decode(file_get_contents($package), true);
        return !empty($json['wpengine-install']);
    }

    /**
     * List plugins that need to be checked for a project
     *
     * @param string $plugin_dir
     * @return array
     */
    private function list_plugins($plugin_dir) {
        return array_filter(scandir($plugin_dir), function ($plugin) {
            if ($plugin === '.' || $plugin === '..') {
                return false;
            }

            return !in_array($plugin, $this->checked_plugins);
        });
    }
}

(new Checker())->check();
