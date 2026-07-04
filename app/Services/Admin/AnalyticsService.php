<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Repositories\AnalyticsRepository;

final class AnalyticsService
{
    private AnalyticsRepository $analytics;

    public function __construct(AnalyticsRepository $analytics)
    {
        $this->analytics = $analytics;
    }

    public function dashboard(): array
    {
        return array(
            'cards' => $this->analytics->summaryCards(),
            'content' => $this->analytics->contentStats(),
            'media' => $this->analytics->mediaStats(),
            'search' => $this->analytics->searchStats(),
            'users' => $this->analytics->userStats(),
            'activity' => $this->analytics->activityStats(),
            'systemInfo' => $this->analytics->systemInfo(),
        );
    }
}