<span class="rvw">
    <button class="rv" type="button" aria-label="{{ __('tour.actions.restart') }}" aria-describedby="tour-restart-tip" data-tour-restart data-tour="review">
        @if(file_exists(public_path('images/mascot/help.jpg')))
            <img alt="" src="{{ asset('images/mascot/help.jpg') }}">
        @else
            <span class="rv-fallback" aria-hidden="true">?</span>
        @endif
    </button>
    <span class="tip" id="tour-restart-tip" role="tooltip">{{ __('tour.actions.restart') }}</span>
</span>
