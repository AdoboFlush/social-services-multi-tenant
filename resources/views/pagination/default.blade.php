@if(method_exists($paginator, 'total'))
<div>
	<ul class="pagination">
        @php
            $total = $paginator->total();
            $last_page = $paginator->lastPage();
            $current_page = $paginator->currentPage();

            $pages = [];

            $trail_dot = false;
            $lead_dot = false;
            
            if ($current_page < $total) {
                if ($current_page != 1) {
                    array_push($pages, $current_page - 1);        
                }

                array_push($pages, $current_page);

                if ($current_page != $last_page) {
                    array_push($pages, $current_page + 1);
                }

                if ($current_page > 2) {
                	$trail_dot = true;                	
            	}

            	if ( $current_page < ($last_page - 1)) {
        			$lead_dot = true;
            	}

            }
            sort($pages);
            
        @endphp
        <li class="page-item disabled">
            @if ($current_page == 1)
            <span class="page-link">&laquo;</span>
            @else
            <a class="page-link" href="{{ ($current_page - 1) }}">&laquo;</a>
            @endif
        </li>
        @if ($trail_dot)
        <li class="page-item"><a class="page-link" href="?{{ $paginator->getPageName() }}=1">1</a></li>
        <li class="page-item"><span class="page-link">...</span></li>
        @endif
        @foreach($pages as $p)
            @if ($p == $current_page)
            <li class="page-item selected"><span class="page-link">{{ $p }}</span></li>
            @else
            <li class="page-item"><a class="page-link" href="?{{ $paginator->getPageName() }}={{ $p }}">{{$p}}</a></li>
            @endif

        @endforeach
        @if ($lead_dot)
        <li class="page-item"><span class="page-link">...</span></li>
        <li class="page-item"><a class="page-link" href="?{{ $paginator->getPageName() }}={{ $last_page }}">{{ $last_page }}</a></li>
        @endif
        <li class="page-item">
            @if ($current_page == $last_page)
            <span class="page-link">&raquo;</span>
            @else
            <a class="page-link" href="?{{ $paginator->getPageName() }}={{ ($current_page + 1) }}">&raquo;</a>
            @endif
        </li>
    </ul>
</div>
@endif