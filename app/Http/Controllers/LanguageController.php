<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
namespace App\Http\Controllers;

use Session;
use Redirect;
use Request;
use Validator;
use App\Http\Requests;
use App\Models\Language;
use App\Models\Definition;
use App\Models\Definitions\Word;
use App\Http\Controllers\Controller;

class LanguageController extends Controller
{
    /**
     * Display the language page.
     *
     * @param string $id    Either the ISO 639-3 language code or language ID.
     * @return Response
     */
    public function show($id)
    {
        // Retrieve the language object.
        if (!$lang = $this->getLanguage($id)) {
            abort(404, 'Can\'t find that languge :(');
        }

        // TODO: count number of words, not all definitions.
        $total = $lang->definitions()->count();
        $first = $latest = $random = null;

        // Retrieve first definition.
        if ($total > 0) {
            $first = $lang->definitions()->with('titles')->orderBy('created_at', 'asc')->first();
        }

        // Retrieve latest definition.
        if ($total > 1) {
            $latest = $lang->definitions()->with('titles')->orderBy('created_at', 'desc')->first();
        }

        // Retrieve random definition.
        if ($total > 2) {
            $random = Word::random($lang);
        }

        return view('pages.language', [
            'lang' => $lang,
            'random' => $random,
            'first' => $first,
            'latest' => $latest
        ]);
    }

	/**
	 * Displays the form to add a new language.
	 *
	 * @return Response
	 */
	public function walkthrough() {
        return view('forms.language.walkthrough');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        // Retrieve the language details.
        $data = Request::only(['code', 'parent_code', 'name', 'alt_names']);

        // Validate input data
        $test = Language::validate($data);
        if ($test->fails())
        {
            // Flash input data to session
            Request::flashExcept('_token');

            // Return to form
            return redirect(route('language.create'))->withErrors($test);
        }

        // Create language.
        $lang = Language::create($data);

        $rdir = Request::input('return') == 'continue' ?
            route('admin.language.edit', $lang->code) :
            $lang->uri;

        Session::push('messages', 'The details for <em>'. $lang->name .'</em> were successfully saved, thanks :)');
        return redirect($rdir);
	}

    /**
     * @param string $query
     * @return mixed
     */
    public function search($query = '')
    {
        // This method should really only be called through the API.
        if (Request::method() != 'POST' && env('APP_ENV') == 'production') {
            abort(405);
        }

        // Performance check
        $query  = trim(preg_replace('/[\s+]/', ' ', strip_tags((string) $query)));
        if (strlen($query) < 2) {
            return $this->abort(400, 'Query too short');
        }

        $offset = min(0, (int) Request::get('offset', 0));
        $limit = min(1, max(100, (int) Request::get('limit', 100)));

        $langs = Language::search($query, $offset, $limit);

        // Format results
        $results  = [];
        $semantic = (bool) Request::has('semantic');
        if (count($langs)) {
            foreach ($langs as $lang) {
                $results[] = $semantic ? array_add($lang->toArray(), 'title', $lang->name) : $lang->toArray();
            }
        }

        // return $this->send(['query' => $query, 'results' => $results]);
        return $this->send($semantic ? $results : compact('query', 'results'));
    }

    /**
     * Shortcut to retrieve a language object.
     *
     * @param string $id    Either the ISO 639-3 language code or language ID.
     * @return \App\Models\Language|null
     */
    private function getLanguage($id, array $embed = ['parent'])
    {
        // Performance check.
        if (empty($id) || is_numeric($id) || !is_string($id)) {
            return null;
        }

        // Find langauge by code.
        if (strlen($id) == 3 || strlen($id) == 7) {
            $lang = Language::findByCode($id, $embed);
        }

        // Find language by ID.
        else {
            $lang = Language::with($embed)->find($id);
        }

        return $lang;
    }
}
