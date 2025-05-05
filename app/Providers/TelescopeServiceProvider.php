<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Only turn on Telescope’s UI in local/staging:
        if ($this->app->environment('local', 'staging')) {
            Telescope::night();
        }

        $this->hideSensitiveRequestDetails();

        // Filter what entries get recorded:
        Telescope::filter(function (IncomingEntry $entry) {
            // keep everything in local/staging...
            if ($this->app->environment('local', 'staging')) {
                return true;
            }

            // otherwise only report errors, failed jobs, etc.
            return $entry->isReportableException()
                || $entry->isFailedRequest()
                || $entry->isFailedJob()
                || $entry->isScheduledTask()
                || $entry->hasMonitoredTag();
        });

        // In production we still need to ignore Telescope’s own migrations
        if ($this->app->isProduction()) {
            Telescope::ignoreMigrations();
        }
    }

    /**
     * Prevent sensitive request details from being logged.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if (! $this->app->environment('local')) {
            Telescope::hideRequestParameters(['_token']);
            Telescope::hideRequestHeaders(['cookie', 'x-csrf-token', 'x-xsrf-token']);
        }
    }

    /**
     * Register the Telescope gate for non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user) {
            return in_array($user->email, [
                // put your admin emails here
                'ibin.shrs@gmai.com',
            ]);
        });
    }
}
