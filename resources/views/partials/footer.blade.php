<footer>
    <div class="shortcuts-wrap">
        <div class="shortcuts collapsed">
            <a
                href="{{ route('home') }}"
                class="fa fa-home has-inline-tooltip"
                data-position="top center"></a>
            <span class="ui popup">Home of Di Nkɔmɔ</span>

            <a
                href="{{ route('about') }}"
                class="fa fa-info has-inline-tooltip"
                onclick="return App.openDialog('info');"
                data-position="top center"></a>
            <span class="ui popup">About this app</span>

            <a
                href="{{ url('login') }}"
                class="fa fa-unlock-alt has-inline-tooltip"
                onclick="return App.openDialog('login');"
                data-position="top center"></a>
            <span class="ui popup">Login</span>

            <a
                href="#"
                class="fa fa-graduation-cap has-inline-tooltip"
                onclick="alert('To do: learning games'); return false;"
                data-position="top center"></a>
            <span class="ui popup">Learn</span>

            <a
                href="#"
                class="fa fa-wrench has-inline-tooltip"
                onclick="return App.openDialog('settings');"
                data-position="top center"></a>
            <span class="ui popup">Settings</span>

            <a
                href="#"
                class="fa fa-pencil has-inline-tooltip"
                onclick="return App.openDialog('resource');"
                data-position="top center"></a>
            <span class="ui popup">Edit Di Nkɔmɔ</span>
        </div>
    </div>
	<div class="credits">
		<small>
            &copy; 2014 - {{ date('Y') }}
            &nbsp;&ndash;&nbsp;
			<a href="{{ route('about') }}">A Ghanaian app</a>
		</small>
	</div>
</footer>

@include('partials.dialogs')
