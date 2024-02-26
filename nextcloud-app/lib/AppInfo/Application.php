<?php
/**
 * Zimbra Drive App
 * Copyright (C) 2017  Zextras Srl
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 * If you require any further information, feel free to contact legal@zextras.com.
 */

namespace OCA\ZimbraDrive\AppInfo;

use OCA\ZimbraDrive\Service\LogService;
use OCA\ZimbraDrive\Service\DisableZimbraDriveHandler;
use OCP\App\ManagerEvent;
use OC;
use OCP\IURLGenerator;
use OCP\L10N\IFactory;
use OCP\Server;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

class Application extends App implements IBootstrap
{
    const APP_ID = 'zimbradrive';

    public function __construct(array $urlParams=[]){
        parent::__construct(self::APP_ID, $urlParams);
    }

    public function register(IRegistrationContext $context): void {

        $context->registerService('IUserSession', function($c) {
            return $c->query('ServerContainer')->getUserSession();
        });

        $context->registerService('ILogger', function($c) {
            return $c->query('ServerContainer')->getLogger();
        });

        $context->registerService('LogService', function($c) {
            $logger = $c->query('ILogger');

            return new LogService($logger, self::APP_ID);
        });

        $context->registerService('IServerContainer', function($c) {
            return $c->query('ServerContainer');
        });

        $context->registerService('IConfig', function($c) {
            return $c->query('ServerContainer')->getConfig();
        });
    }

    public function boot(IBootContext $context): void {
        $this->registerNavigation($context);

        $serverContainer = $context->getServerContainer();
        /** @var IEventDispatcher $eventDispatcher */
        $eventDispatcher = $serverContainer->get(IEventDispatcher::class);

        $eventDispatcher->addListener(ManagerEvent::EVENT_APP_DISABLE, function (ManagerEvent $event) {
            DisableZimbraDriveHandler::handle(array ('app' => $event->getAppID()));
        });
    }

    private function registerNavigation(IBootContext $context): void {
        $urlGenerator = $context->getAppContainer()->query('OCP\IURLGenerator');
        $l = Server::get(IFactory::class)->get(self::APP_ID);

        $context->getAppContainer()->query('OCP\INavigationManager')->add([
            // the string under which your app will be referenced in *Cloud
            'id' => Application::APP_ID,

            // sorting weight for the navigation. The higher the number, the higher
            // will it be listed in the navigation
            'order' => 10,

            // the route that will be shown on startup
            'href' => $urlGenerator->linkToRoute('zimbradrive.page.index'),

            // the icon that will be shown in the navigation
            // this file needs to exist in img/
            'icon' => $urlGenerator->imagePath(Application::APP_ID, 'app.svg'),

            // the title of your application. This will be used in the
            // navigation or on the settings page of your app
            'name' => $l->t('Zimbra'),
        ]);
    }

}

OC::$CLASSPATH['OC_User_Zimbra'] = 'zimbradrive/lib/auth/oc_user_zimbra.php';
