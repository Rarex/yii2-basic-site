<?php

namespace app\commands;

use yii\console\Controller;


/**
 * Initialise application
 */
class InstallController extends Controller
{

    /**
     * @var array
     */
    protected $directories = [
        'runtime' => '0777',
        'web/assets' => '0777',
    ];
    protected $configCacheFiles = ['web', 'console'];

    public static function composerPostInstallCmd($event)
    {
        $envFile = __DIR__ . '/../config/env.php';
        if (!is_file($envFile)) {
            $env = getenv('YII_ENV');
            if (!$env) {
                $env = $event->getComposer()->getConfig()->get('YII_ENV');
            }
            if (!$env) {
                $env = 'local';
            }

            file_put_contents($envFile, "<?php return '$env';");
            echo "env file created for '" . $env . "' environment \n";
        }

        echo "composer post install ... ok\n";
    }

    /**
     * Create required directories and files
     * @param mixed $env application environment: prod, dev, stage, local
     */
    public function actionIndex($env = YII_ENV)
    {
        $basePath = \Yii::getAlias('@app');
        $configFile = $basePath . '/config/override/' . $env . '.php';

        if (is_file($configFile)) {
            $this->stdout('Environment: "' . $env . "\"\n");
        } else {
            $this->stderr("Unknown environment!\n");
        }

        foreach ($this->directories as $directory => $mode) {
            $this->stdout('Directory "' . $directory . '" ... ');
            $dirName = $basePath . '/' . $directory;
            if (is_dir($dirName)) {
                $this->stdout("ok.\n");
                chmod($dirName, octdec($mode));
            } elseif (mkdir($dirName, octdec($mode), true)) {
                $this->stdout("created.\n");
                chmod($dirName, octdec($mode));
            } else {
                $this->stderr("FAILED!\n");
            }
        }

        $this->stdout('Override config file ... ');
        $overrideFile = $basePath . '/config/override.php';

        if (YII_ENV === $env && YII_ENV_LOCAL && is_file($overrideFile)) {
            $this->stdout("ok.\n");
        } elseif (copy($configFile, $overrideFile)) {
            $this->stdout("copied.\n");
        } else {
            $this->stderr("FAILED!\n");
        }

        $this->stdout('Local environment file ... ');
        $envFile = $basePath . '/config/env.php';

        if (YII_ENV === $env && is_file($envFile)) {
            $this->stdout("ok.\n");
        } elseif (file_put_contents($envFile, "<?php return '$env';")) {
            $this->stdout("changed.\n");
        } else {
            $this->stderr("FAILED!\n");
        }
    }

    public function actionConfig()
    {
        $configDir = \Yii::getAlias('@app') . '/config';
        $cacheDir = \Yii::getAlias('@runtime');
        foreach ($this->configCacheFiles as $configName) {
            $configPath = $configDir . '/' . $configName . '.php';
            if (!is_file($configPath)) {
                $this->stderr("Config file not found for '" . $configName . "'\n");
            } else {
                $config = require($configDir . '/' . $configName . '.php');
                $this->stdout("Caching '" . $configName . "' config ... ");
                file_put_contents($cacheDir . '/' . $configName . '.php',
                    '<?php return ' . $this->varExport($config) . ';');
                $this->stdout("OK \n");
            }
        }
    }

    protected function varExport($var, $indent = "")
    {
        switch (gettype($var)) {
            case "string":
                return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
            case "array":
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = "$indent    "
                        . ($indexed ? "" : $this->varExport($key) . " => ")
                        . $this->varExport($value, "$indent    ");
                }
                return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
            case "boolean":
                return $var ? "TRUE" : "FALSE";
            case "object":
                if ($var instanceof \Closure) {
                    return $this->varExportClosure($var);
                }
                break;
            default:
                return var_export($var, true);
        }

        return '';
    }

    protected function varExportClosure(\Closure $c)
    {
        $str = 'function (';
        $r = new \ReflectionFunction($c);
        $params = array();
        foreach ($r->getParameters() as $p) {
            $s = '';
            if ($p->isArray()) {
                $s .= 'array ';
            } else {
                if ($p->getClass()) {
                    $s .= $p->getClass()->name . ' ';
                }
            }
            if ($p->isPassedByReference()) {
                $s .= '&';
            }
            $s .= '$' . $p->name;
            if ($p->isOptional()) {
                $s .= ' = ' . var_export($p->getDefaultValue(), true);
            }
            $params [] = $s;
        }
        $str .= implode(', ', $params);
        $str .= '){' . PHP_EOL;
        $lines = file($r->getFileName());
        for ($l = $r->getStartLine(); $l < $r->getEndLine(); $l++) {
            $str .= $lines[$l];
        }
        return rtrim(trim($str), ",");
    }
}