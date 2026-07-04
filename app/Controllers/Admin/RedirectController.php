<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Application;
use App\Core\Request;
use App\Repositories\RedirectRepository;

final class RedirectController extends BaseController
{
    private RedirectRepository $redirectRepository;

    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->redirectRepository = new RedirectRepository($this->app->database()->connection());
    }

    public function index(Request $request)
    {
        return $this->view('admin/redirects/index', array(
            'currentUser' => $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user')),
            'redirects' => $this->redirectRepository->allRedirects(),
            'form' => array(),
            'errors' => array(),
        ), array(
            'title' => 'Redirects',
            'description' => 'Manage legacy URL redirects.',
            'canonical' => url('/admin/redirects'),
            'robots' => 'noindex, nofollow',
        ));
    }

    public function store(Request $request)
    {
        $id = $request->input('id', '');

        try {
            $this->redirectRepository->saveRedirect(array(
                'source_path' => $request->input('source_path', ''),
                'target_path' => $request->input('target_path', ''),
                'status_code' => $request->input('status_code', 301),
                'reason' => $request->input('reason', ''),
                'is_active' => $request->input('is_active', 1),
            ), $id);

            $this->app->session()->flash('success', 'Redirect saved successfully.');
            return $this->redirect('/admin/redirects');
        } catch (\Throwable $exception) {
            $this->app->session()->flash('error', $exception->getMessage());

            return $this->view('admin/redirects/index', array(
                'currentUser' => $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user')),
                'redirects' => $this->redirectRepository->allRedirects(),
                'form' => array(
                    'id' => $id,
                    'source_path' => $request->input('source_path', ''),
                    'target_path' => $request->input('target_path', ''),
                    'status_code' => $request->input('status_code', 301),
                    'reason' => $request->input('reason', ''),
                    'is_active' => $request->input('is_active', 1),
                ),
                'errors' => array('general' => $exception->getMessage()),
            ), array(
                'title' => 'Redirects',
                'description' => 'Manage legacy URL redirects.',
                'canonical' => url('/admin/redirects'),
                'robots' => 'noindex, nofollow',
            ));
        }
    }

    public function delete(Request $request, int $id)
    {
        $this->redirectRepository->deleteRedirect($id);
        $this->app->session()->flash('success', 'Redirect removed.');

        return $this->redirect('/admin/redirects');
    }
}