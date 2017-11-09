<?php

namespace App\Http\Controllers;

use DoraBoateng\Api\Client;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * @var \DoraBoateng\Api\Client
     */
    protected $api;

    /**
     * Internal name used to map controller to its model, views, etc.
     *
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $defaultQueryLimit = 20;

    /**
     * @var array
     */
    protected $supportedOrderColumns = [];

    /**
     * @var string
     */
    protected $defaultOrderColumn = 'id';

    /**
     * @var string
     */
    protected $defaultOrderDirection = 'desc';

    /**
     * @param  \Illuminate\Http\Request                  $request
     * @param  \Illuminate\Contracts\Cache\Repository    $cache
     * @param  \DoraBoateng\Api\Client                   $api
     */
    public function __construct(Request $request, Cache $cache, Client $api)
    {
        // Determine internal name from class name.
        if (! $this->name) {
            $namespace  = explode('\\', get_class($this));
            $this->name = strtolower(substr(array_pop($namespace), 0, -10));
        }

        $this->api      = $api;
        $this->cache    = $cache;
        $this->request  = $request;

        $this->boot();
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort(501);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort(501);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        abort(501);
    }

    /**
     * @param  string $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort(501);
    }

    /**
     * @param  string $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort(501);
    }

    /**
     * @param  string $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        abort(501);
    }

    /**
     * @param  string $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort(501);
    }

    /**
     * Retrieves search results.
     *
     * @param  string $langCode
     * @return array
     */
    protected function getSearchResults($langCode = null)
    {
        $search = [
            'query'     => trim($this->request->get('q')),
            'results'   => null,
        ];

        if (! $search['query']) {
            return $search;
        }

        $cacheKey = ($langCode ? 'search.'.$langCode : 'search.all').'-'.base64_encode($search['query']);

        $response = $this->cache->remember($cacheKey, 5, function() use ($search, $langCode) {
            return $langCode
                ? $this->api->searchDefinitions($search['query'], $langCode)
                : $this->api->search($search['query']);
        });

        if (is_object($response)) {
            $search['results'] = $response->results;
        }

        return $search;
    }

    /**
     * @return \stdClass|null
     */
    protected function getWeeklyLanguage()
    {
        try {
            $language = $this->api->getLanguageOfTheWeek();
        } catch (\Exception $e) {
            return null;
        }

        // Cache language of the week for 3 hours
        $this->cache->add('language.weekly', $language, 180);

        return $language;
    }

    /**
     * Shortcut to return a redirect response with some error messages.
     *
     * @param  string     $route
     * @param  string     $errorMsg
     * @param  \Exception $exception
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectWithErrors(
        $route,
        $errorMsg = null,
        \Exception $exception = null
    ) {
        $errors = [];

        if ($errorMsg) {
            $errors[] = $errorMsg;
        }

        if ($exception && app()->environment() === 'local') {
            $errors[] = $exception->getMessage();
        }

        return redirect($route)->withErrors($errors);
    }

    /**
     * Builds a name-spaced cache key.
     *
     * @param  string $id
     * @return string
     */
    protected function getCacheKey($id)
    {
        return $this->name.'.'.$id;
    }

    /**
     * The boot method can be used by child controllers for bootstrapping and setup.
     */
    protected function boot() {}
}
