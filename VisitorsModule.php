<?php
namespace visitors;

use visitors\models\LogTarget;
use yii\base\BootstrapInterface;
use yii\base\Module;
use yii\web\Application as WebApplication;
use yii\web\Response;

/**
 * Visitors module definition class
 */
class VisitorsModule extends Module implements BootstrapInterface
{
    /**
     * @var array|string[]|null List of client hints to request from the user agent.
     * If set to `null`, no client hints will be requested.
     *
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Client_hints
     * @see https://wicg.github.io/ua-client-hints/
     */
    public ?array $requestClientHints = [
        // User-agent client hints
        'Sec-CH-UA',
        'Sec-CH-UA-Arch',
        'Sec-CH-UA-Bitness',
        'Sec-CH-UA-Full-Version-List',
        'Sec-CH-UA-Full-Version',
        'Sec-CH-UA-Mobile',
        'Sec-CH-UA-Model',
        'Sec-CH-UA-Platform',
        'Sec-CH-UA-Platform-Version',
        // User preference media features client hints
        'Sec-CH-Prefers-Reduced-Motion',
        'Sec-CH-Prefers-Color-Scheme',
        // Device client hints
        'Device-Memory',
        'Viewport-Width',
        'Width',
        // Network client hints
        'Save-Data',
        'Downlink',
        'ECT',
        'RTT',
    ];

    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'visitors\controllers';

    /**
     * {@inheritdoc}
     */
    public function bootstrap($app): void
    {
        if ($app instanceof WebApplication) {
            $app->getLog()->targets['visitors'] = new LogTarget($this, $app->getRequest());

            // delay attaching event handler to the view component after it is fully configured
            $app->on($app::EVENT_BEFORE_REQUEST, function () use ($app) {
                $app->getResponse()->on(Response::EVENT_AFTER_PREPARE, [$this, 'addClientHintHeaders']);
            });
        }
    }

    public function addClientHintHeaders($event): void
    {
        if ($this->requestClientHints === null) {
            return;
        }
        $event->sender->getHeaders()
            ->set('Accept-CH', implode(', ', $this->requestClientHints));
    }
}
