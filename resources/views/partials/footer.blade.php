
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
            <footer>
                <div class="shortcuts">
                    <a
                        href="{{ route('home') }}"
                        class="fa fa-search fa-fw"
                        title="Lookup something"
                        data-toggle="tooltip"></a>
                    <a
                        href="{{ route('definition.random') }}"
                        class="fa fa-retweet fa-fw"
                        title="Random definition"
                        data-toggle="tooltip"></a>
                    <a
                        href="{{ route('contribute') }}"
                        class="fa fa-plus fa-fw"
                        title="Add to Di Nkɔmɔ"
                        data-toggle="tooltip"></a>
                    <a
                        href="{{ route('about') }}"
                        class="fa fa-question fa-fw"
                        title="About this app"
                        data-toggle="tooltip"></a>
                    <a
                        href="{{ route('about.agoro') }}"
                        class="fa fa-graduation-cap fa-fw"
                        title="@lang('branding.learning_app_title')"
                        data-toggle="tooltip"></a>

                    @if (Auth::check())
                        <a
                            href="{{ route('admin.index') }}"
                            class="fa fa-cubes fa-fw"
                            title="Admin"
                            data-toggle="tooltip"></a>
                    @endif
                </div>

                <div class="footer-row">
                    <a href="{{ route('about') }}" class="credits">
                        &copy; 2014 - {{ date('Y') }}
                        <span class="ghana no-underline"></span>
                    	A <b>Ghanaian</b> app
                    </a>
                </div>

                <div class="footer-row">
                    <a href="{{ route('about') }}">
                        about
                    </a>
                    |
                    <a href="{{ route('sitemap') }}">
                        sitemap
                    </a>
                    |
                    <a href="{{ route('about.api') }}">
                        api
                    </a>
                    |
                    <a href="https://github.com/frnkly/dinkomo" target="_blank">
                        github
                    </a>
                    |
                    @if (Auth::check())
                        <a href="{{ route('auth.logout') }}">
                            sign out
                        </a>
                    @else
                        <a href="{{ route('auth.login') }}">
                            sign in
                        </a>
                    @endif
                </div>
            </footer>
        </div>
    </div>
</div>

@include('partials.dialogs')
