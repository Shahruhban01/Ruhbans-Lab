<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Application;
use App\Core\HttpException;
use App\Core\Request;
use App\Repositories\EngagementRepository;
use App\Repositories\PostRepository;

final class EngagementController extends BaseController
{
    private EngagementRepository $engagementRepository;
    private PostRepository $postRepository;

    public function __construct(Application $app)
    {
        parent::__construct($app);

        $connection = $this->app->database()->connection();
        $this->engagementRepository = new EngagementRepository($connection);
        $this->postRepository = new PostRepository($connection);
    }

    public function comment(Request $request, int $postId)
    {
        $post = $this->postRepository->findPublishedById($postId);

        if (!$post) {
            throw new HttpException('Content not found.', 404);
        }

        $parentId = (int) $request->input('parent_id', 0);
        $result = $this->engagementRepository->addComment(
            $postId,
            $this->interactionIdentity(),
            (string) $request->input('body', ''),
            $parentId > 0 ? $parentId : null,
            (string) $request->input('guest_name', ''),
            (string) $request->input('guest_email', '')
        );

        $this->app->session()->flash($result['success'] ? 'success' : 'error', (string) $result['message']);

        return $this->redirect($this->backToContent((string) $post['slug']));
    }

    public function react(Request $request, int $postId, string $type)
    {
        $post = $this->postRepository->findPublishedById($postId);

        if (!$post) {
            throw new HttpException('Content not found.', 404);
        }

        $result = $this->engagementRepository->toggleReaction($postId, $type, $this->interactionIdentity());
        $this->app->session()->flash('success', ucfirst($type) . ($result['active'] ? ' saved.' : ' removed.'));

        return $this->redirect($this->backToContent((string) $post['slug']));
    }

    public function newsletter(Request $request)
    {
        $email = trim((string) $request->input('email', ''));
        $name = trim((string) $request->input('name', ''));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->app->session()->flash('error', 'Enter a valid email address.');
            return $this->redirect($this->backUrl('/'));
        }

        $result = $this->engagementRepository->subscribeNewsletter($email, $name);
        $this->app->session()->flash($result['success'] ? 'success' : 'error', (string) $result['message']);

        return $this->redirect($this->backUrl('/'));
    }

    public function contact(Request $request)
    {
        $result = $this->engagementRepository->storeContactMessage(array(
            'name' => $request->input('name', ''),
            'email' => $request->input('email', ''),
            'subject' => $request->input('subject', ''),
            'message' => $request->input('message', ''),
        ));

        $this->app->session()->flash($result['success'] ? 'success' : 'error', (string) $result['message']);

        return $this->redirect('/contact');
    }

    private function backToContent(string $slug): string
    {
        $referer = (string) $this->app->request()->header('Referer', '');

        if ($referer !== '') {
            return $referer;
        }

        return url('/content/' . $slug);
    }

    private function backUrl(string $fallback): string
    {
        $referer = (string) $this->app->request()->header('Referer', '');

        return $referer !== '' ? $referer : url($fallback);
    }
}