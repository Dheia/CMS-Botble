<div class="navbar-expand-md hd-exp">
    <div
        class="navbar-collapse collapse"
        id="navbar-menu"
    >
        <div class="navbar">
            <div class="{{ AdminAppearance::getContainerWidth() }}">
                <div class="row flex-fill align-items-center">
                    <div class="col">
                        @include('core/base::layouts.partials.navbar-nav-horizontal', [
                            'autoClose' => 'outside',
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>