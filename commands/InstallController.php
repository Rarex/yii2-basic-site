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

    public static function composerPostInstallCmd($event)
    {
        var_dump($event->getComposer()->getConfig()->get('env'));
        echo 'composer post install cmd...';
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
}