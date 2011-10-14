<?php

namespace MarcW\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Buzz\Browser;
use Buzz\Client;

/**
 * BuzzServiceProvider
 *
 * @author Marc Weistroff <marc.weistroff@gmail.com>
 */
class BuzzServiceProvider implements ServiceProviderInterface
{
    protected $options = array(
        'client'  => 'Buzz\Client\Curl',
        'browser' => 'Buzz\Browser',
    );

    public function register(Application $app)
    {
        $options = isset($app['buzz.options']) ? array_merge($this->options, $app['buzz.options']) : $this->options;

        $app['buzz'] = $app->share(function() use($options) {
            $client = null;
            if ($options['client'] instanceof Closure) {
                $callable = $options['client'];
                $client = $callable();
            } else {
                $class = $options['client'];
                $client = new $class();
            }

            $browser = null;
            if ($options['browser'] instanceof Closure) {
                $callable = $options['browser'];
                $browser = $callable($client);
            } else {
                $class = $options['browser'];
                $browser = new $class($client);
            }

            return $browser;
        });


        if (isset($app['buzz.class_path'])) {
            $app['autoloader']->registerNamespace('Buzz', $app['buzz.class_path']);
        }
    }
}