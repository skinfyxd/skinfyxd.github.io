<?php

namespace App\Http\Controllers;

use Log;
use File;
use Utils;
use Schema;
use Option;
use Storage;
use Artisan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use App\Exceptions\PrettyPageException;

class SetupController extends Controller
{
    public function welcome()
    {
        $type = get_db_type();

        if ($type === 'SQLite') {
            $server = get_db_config()['database'];
        } else {
            $config = get_db_config();
            $server = "{$config['username']}@{$config['host']}";
        }

        return view('setup.wizard.welcome')->with(compact('type', 'server'));
    }

    public function info()
    {
        $existingTables = static::checkTablesExist([], true);

        // Not installed completely
        if (count($existingTables) > 0) {
            Log::info('Remaining tables detected, exit setup wizard now', [$existingTables]);

            $existingTables = array_map(function ($item) {
                return get_db_config()['prefix'].$item;
            }, $existingTables);

            throw new PrettyPageException(trans('setup.database.table-already-exists', ['tables' => json_encode($existingTables)]), 1);
        }

        if (! function_exists('escapeshellarg')) {
            throw new PrettyPageException(trans('setup.disabled-functions.escapeshellarg'), 1);
        }

        return view('setup.wizard.info');
    }

    public function finish(Request $request)
    {
        $this->validate($request, [
            'email'     => 'required|email',
            'password'  => 'required|min:8|max:16|confirmed',
            'site_name' => 'required'
        ]);

        if ($request->has('generate_random')) {
            // Generate new APP_KEY & SALT randomly
            if (is_writable(app()->environmentFile())) {
                Artisan::call('key:random');
                Artisan::call('salt:random');

                Log::info("[SetupWizard] Random application key & salt set successfully.", [
                    'key'  => config('app.key'),
                    'salt' => config('secure.salt')
                ]);
            } else {
                // @codeCoverageIgnoreStart
                Log::warning("[SetupWizard] Failed to set application key. No write permission.");
                // @codeCoverageIgnoreEnd
            }
        }

        // Create tables
        Artisan::call('migrate', ['--force' => true]);
        Log::info("[SetupWizard] Tables migrated.");

        Option::set('site_name', $request->input('site_name'));

        $siteUrl = url('/');

        if (ends_with($siteUrl, '/index.php')) {
            $siteUrl = substr($siteUrl, 0, -10);    // @codeCoverageIgnore
        }

        Option::set('site_url',  $siteUrl);

        // Register super admin
        $user = User::register(
            $request->input('email'),
            $request->input('password'), function ($user)
        {
            $user->ip           = Utils::getClientIp();
            $user->score        = option('user_initial_score');
            $user->register_at  = Utils::getTimeFormatted();
            $user->last_sign_at = Utils::getTimeFormatted(time() - 86400);
            $user->permission   = User::SUPER_ADMIN;
        });
        Log::info("[SetupWizard] Super Admin registered.", ['user' => $user]);

        $this->createDirectories();
        Log::info("[SetupWizard] Installation completed.");

        return view('setup.wizard.finish')->with([
            'email'    => $request->input('email'),
            'password' => $request->input('password')
        ]);
    }

    public function update()
    {
        if (Utils::versionCompare(config('app.version'), option('version', ''), '<=')) {
            // No updates available
            return view('setup.locked');
        }

        return view('setup.updates.welcome');
    }

    public function doUpdate()
    {
        $resource = opendir(database_path('update_scripts'));
        $updateScriptExist = false;

        $tips = [];

        while($filename = @readdir($resource)) {
            if ($filename != "." && $filename != "..") {
                preg_match('/update-(.*)-to-(.*).php/', $filename, $matches);

                // Skip if the file is not valid or expired
                if (! isset($matches[2]) ||
                    Utils::versionCompare($matches[2], config('app.version'), '<')) {
                    continue;
                }

                $result = require database_path('update_scripts')."/$filename";

                if (is_array($result)) {
                    // Push the tip into array
                    foreach ($result as $tip) {
                        $tips[] = $tip;
                    }
                }

                $updateScriptExist = true;
            }
        }
        closedir($resource);

        foreach (config('options') as $key => $value) {
            if (! Option::has($key)) {
                Option::set($key, $value);
            }
        }

        if (! $updateScriptExist) {
            // If there is no update script given
            Option::set('version', config('app.version'));
        }

        // Clear all compiled view files
        try {
            Artisan::call('view:clear');
        } catch (\Exception $e) {
            Log::error('Error occured when processing view:clear', [$e]);

            $files = collect(File::files(storage_path('framework/views')));
            $files->reject(function ($path) {
                return ends_with($path, '.gitignore');
            })->each(function ($path) {
                File::delete($path);
            });
        }

        return view('setup.updates.success', ['tips' => $tips]);
    }

    /**
     * Check if the given tables exist in current database.
     *
     * @param  array $tables
     * @param  bool  $returnExisting
     * @return bool|array
     */
    public static function checkTablesExist($tables = [], $returnExistingTables = false) {
        $existingTables = [];
        $tables = $tables ?: ['users', 'closets', 'players', 'textures', 'options'];

        foreach ($tables as $tableName) {
            // Table prefix will be added automatically
            if (Schema::hasTable($tableName)) {
                $existingTables[] = $tableName;
            }
        }

        if (count($existingTables) == count($tables)) {
            return true;
        } else {
            return $returnExistingTables ? $existingTables : false;
        }
    }

    public static function checkDirectories()
    {
        $directories = ['storage/textures', 'plugins'];

        try {
            foreach ($directories as $dir) {
                if (! Storage::disk('root')->has($dir)) {
                    // Try to mkdir
                    if (! Storage::disk('root')->makeDirectory($dir)) {
                        return false;
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function createDirectories()
    {
        return self::checkDirectories();
    }

    /**
     * {@inheritdoc}
     */
    protected function formatValidationErrors(Validator $validator)
    {
        return $validator->errors()->all();
    }
}
