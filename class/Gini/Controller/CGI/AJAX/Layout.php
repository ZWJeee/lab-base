<?php

namespace Gini\Controller\CGI\AJAX;

class Layout extends \Gini\Controller\CGI
{
    public function actionHeader()
    {
        $top_menu = new \ArrayObject();
        \Gini\Event::trigger('header.top-menu', $top_menu);

        $vars = [
            'route' => $this->env['route'],
            'top_menu' => $top_menu,
        ];

        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V('layout/header', $vars));
    }

    public function actionSidebar()
    {
        $me = _G('ME');
        $apps = (array) _G('GROUP')->getApps();
        $alloweds = \Gini\Config::get('sidebar.apps') ?: [];
        foreach ($alloweds as $clientID=>$actions) {
            $actions = (array) $actions;
            if (!isset($apps[$clientID])) continue;
            foreach ($actions as $action) {
                if ($me->isAllowedTo($action)) {
                    continue 2;
                }
            }
            unset($apps[$clientID]);
        }

        $i = 0;
        $apps = array_map(function($v) use (&$i) { 
            $v['weight'] = $v['weight'] ?: $i; $i++; return $v; }, (array)$apps);
        uasort($apps, function($a, $b){
            return ($a['weight'] < $b['weight']) ? -1 : 1;
        });

        $vars = [
            'route' => $this->env['route'],
            'currentAppID'=> \Gini\Gapper\Client::getId(),
            'apps' => $apps,
        ];

        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V('layout/sidebar', $vars));
    }

    public function actionFooter()
    {
        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V('layout/footer'));
    }

    public function actionBackToTop()
    {
        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V('layout/back-to-top'));
    }

}
