<?php

namespace App\Http\Controllers;

use View;
use Datatables;
use App\Events;
use Illuminate\Http\Request;
use App\Services\PluginManager;

class PluginController extends Controller
{
    /**
     * @codeCoverageIgnore
     */
    public function showMarket()
    {
        return redirect('/')->setTargetUrl(
            'https://github.com/printempw/blessing-skin-server/wiki/Plugins'
        );
    }

    public function showManage()
    {
        return view('admin.plugins');
    }

    public function config($name, Request $request)
    {
        $plugin = plugin($name);

        if ($plugin && $plugin->isEnabled() && $plugin->hasConfigView()) {
            return $plugin->getConfigView();
        } else {
            return abort(404, trans('admin.plugins.operations.no-config-notice'));
        }
    }

    public function manage(Request $request, PluginManager $plugins)
    {
        $plugin = plugin($name = $request->get('name'));

        if ($plugin) {
            // Pass the plugin title through the translator.
            $plugin->title = trans($plugin->title);

            switch ($request->get('action')) {
                case 'enable':
                    $plugins->enable($name);

                    return json(trans('admin.plugins.operations.enabled', ['plugin' => $plugin->title]), 0);

                case 'disable':
                    $plugins->disable($name);

                    return json(trans('admin.plugins.operations.disabled', ['plugin' => $plugin->title]), 0);

                case 'delete':
                    $plugins->uninstall($name);

                    return json(trans('admin.plugins.operations.deleted'), 0);

                default:
                    return json(trans('admin.invalid-action'), 1);
            }
        } else {
            return json(trans('admin.plugins.operations.not-found'), 1);
        }
    }

    public function getPluginData(PluginManager $plugins)
    {
        $installed = $plugins->getPlugins();

        return Datatables::of($installed)
            ->setRowId('plugin-{{ $name }}')
            ->editColumn('title', function ($plugin) {
                return trans($plugin->title);
            })
            ->editColumn('description', function ($plugin) {
                return trans($plugin->description);
            })
            ->editColumn('author', function ($plugin) {
                return ['author' => trans($plugin->author), 'url' => $plugin->url];
            })
            ->addColumn('status', function ($plugin) {
                return trans('admin.plugins.status.'.($plugin->isEnabled() ? 'enabled' : 'disabled'));
            })
            ->addColumn('operations', function ($plugin) {
                return ['enabled' => $plugin->isEnabled(), 'hasConfigView' => $plugin->hasConfigView()];
            })
            ->make(true);
    }
}
