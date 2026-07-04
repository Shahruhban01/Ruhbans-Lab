<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\BaseRepository;

abstract class BaseService
{
    protected BaseRepository $repository;

    public function __construct(BaseRepository $repository)
    {
        $this->repository = $repository;
    }
}
