<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Response;

abstract class BaseAdminController extends BaseController
{
    protected function adminView(string $template, array $data = array(), array $meta = array()): Response
    {
        return $this->view($template, $data, array('layout' => 'admin/layouts/main', 'meta' => $meta));
    }

    protected function authView(string $template, array $data = array(), array $meta = array()): Response
    {
        return $this->view($template, $data, array('layout' => 'admin/layouts/auth', 'meta' => $meta));
    }
}
