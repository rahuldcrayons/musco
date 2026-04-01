@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex gap-2 items-center justify-between">

        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-neutral-600 bg-white border border-neutral-300 cursor-not-allowed leading-5 rounded-md">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-4 py-2 text-sm font-medium text-neutral-800 bg-white border border-neutral-300 leading-5 rounded-md hover:text-neutral-700 focus:outline-none focus:ring ring-neutral-300 focus:border-[#B76E79] active:bg-neutral-100 active:text-neutral-800 transition ease-in-out duration-150 hover:bg-neutral-100">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-4 py-2 text-sm font-medium text-neutral-800 bg-white border border-neutral-300 leading-5 rounded-md hover:text-neutral-700 focus:outline-none focus:ring ring-neutral-300 focus:border-[#B76E79] active:bg-neutral-100 active:text-neutral-800 transition ease-in-out duration-150 hover:bg-neutral-100">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-neutral-600 bg-white border border-neutral-300 cursor-not-allowed leading-5 rounded-md">
                {!! __('pagination.next') !!}
            </span>
        @endif

    </nav>
@endif
