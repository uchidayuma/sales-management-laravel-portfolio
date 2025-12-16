<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     */
    // baseUrlを上書きする
    // これを設定しないとchromeが「Site can't be reached」となり何をやってもエラーになってしまう。
    protected function baseUrl()
    {
        if (config('app.env') == 'local' || config('app.env') == 'localhost' || config('app.env') == 'development') {
            return 'http://php';
        } else {
            return config('app.url');
        }
    }

    public static function prepare()
    {
        // static::startChromeDriver();
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions())->addArguments([
            '--disable-gpu',
            '--headless',
            '--lang=ja_JP', // 日本語化
            '--window-size=1900,1440',
            '--no-sandbox',
        ]);

        if (config('app.env') == 'local' || config('app.env') == 'localhost' || config('app.env') == 'development') {
            return RemoteWebDriver::create(
                'http://selenium:4444/wd/hub', DesiredCapabilities::chrome()
            );
        } else {
            return RemoteWebDriver::create(
                'http://localhost:9515', DesiredCapabilities::chrome()->setCapability(
                    ChromeOptions::CAPABILITY, $options
                ), 30 * 1000, 30 * 1000
            );
        }
    }
    /**
     * Temporal solution for cleaning up session
    protected function setUp()
    {
        parent::setUp();
        foreach (static::$browsers as $browser) {
            $browser->driver->manage()->deleteAllCookies();
        }
    }
    */
}
