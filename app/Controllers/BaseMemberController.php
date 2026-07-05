<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;

abstract class BaseMemberController extends BaseController
{
    protected function memberView(string $template, array $data = array(), array $meta = array()): Response
    {
        return $this->view($template, $data, array(
            'layout' => 'layouts/member',
            'meta' => $meta
        ));
    }
}
