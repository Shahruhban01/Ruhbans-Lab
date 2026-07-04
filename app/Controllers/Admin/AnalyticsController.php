<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Application;
use App\Core\Request;
use App\Repositories\AnalyticsRepository;
use App\Services\Admin\AnalyticsService;

final class AnalyticsController extends BaseAdminController
{
    private AnalyticsService $analyticsService;

    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->analyticsService = new AnalyticsService(new AnalyticsRepository($this->app->database()->connection()));
    }

    public function index(Request $request)
    {
        return $this->adminView('admin/analytics/index', array_merge(
            $this->analyticsService->dashboard(),
            array('currentUser' => $this->currentUser())
        ), array(
            'title' => 'Analytics',
            'description' => 'Analytics dashboard for search, content, media, users, and system status.',
            'canonical' => url('/admin/analytics'),
            'robots' => 'noindex, nofollow',
        ));
    }
}