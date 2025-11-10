@extends('layouts.queue_layout')

@section('custom-css')
<style>
.card-header .card-title, .queue-name {
    font-size: 1.6rem !important;
    font-weight: bold !important;
    text-align: center;
    width: 100%;
    display: block;
    font-family: 'Times New Roman', Times, serif !important;
}
.header-title-section h1, .header-title-section h3 {
    font-family: 'Times New Roman', Times, serif !important;
}
.queue-type {
    font-size: 1.2rem;
    text-align: center;
    color: #555;
    margin-bottom: 0.5rem;
}
.queue-name.now-serving {
    color: #e40b0b !important;
}
.refresh-controls {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    margin-bottom: 10px;
    gap: 10px;
}
.row.full-height {
    height: calc(100vh - 80px); /* 80px is the height of the fixed marquee */
    overflow: hidden;
    margin-bottom: 0 !important;
}
.card.card-olive.fixed-height {
    height: 85% !important;
    min-height: calc(85vh - 140px);
    display: flex;
    flex-direction: column;
}
.card-body.scrollable {
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    overflow-y: auto;
    max-height: 70vh;
}
body, html {
    height: 100%;
    margin: 0;
    padding: 0;
    overflow: hidden !important;
}
.container-fluid.mb-1.mt-1 {
    padding-bottom: 90px; /* add a bit more space for safety */
}
.fixed-bottom-marquee {
    position: fixed;
    left: 0;
    bottom: 0;
    width: 100vw;
    background: #222d32;
    color: #fff;
    z-index: 9999;
    padding: 10px 0 5px 0;
    box-shadow: 0 -2px 8px rgba(0,0,0,0.1);
    font-family: 'Times New Roman', Times, serif;
}
.fixed-bottom-marquee .marquee-text {
    font-size: 1.2rem;
    font-weight: bold;
    white-space: nowrap;
    overflow: hidden;
}
.fixed-bottom-marquee .current-time {
    font-size: 1.1rem;
    font-weight: normal;
    float: right;
    margin-right: 30px;
}
.fixed-marquee-bottom {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100vw;
    z-index: 9999;
}
.main-content-with-marquee {
    padding-bottom: 60px; /* Height of marquee row */
}
</style>
@endsection

@section('content')

<div class="container-fluid mb-1 mt-1 main-content-with-marquee">
    <!-- <div class="refresh-controls">
        <button id="manual-refresh" class="btn btn-primary btn-sm">Refresh</button>
        <div class="form-check form-switch ml-2">
            <input class="form-check-input" type="checkbox" id="auto-refresh-toggle" checked>
            <label class="form-check-label" for="auto-refresh-toggle">Auto Refresh</label>
        </div>
    </div> -->
    <div class="row">
        <div class="col-md-6">
            <div id="queue-display-section" style="background:#000;min-height:calc(85vh - 140px);height:85%;display:flex;align-items:center;justify-content:center;">
                @if(($queue_display_type ?? '') === 'video' && !empty($queue_video))
                    <div class="mb-2 d-flex align-items-center justify-content-center w-100" style="height:100%;">
                        <video width="100%" style="max-height:80vh;object-fit:contain;" controls autoplay loop>
                            <source src="{{ asset('uploads/videos/' . $queue_video) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                @elseif(($queue_display_type ?? '') === 'image' && !empty($queue_image))
                    <div class="mb-2 d-flex align-items-center justify-content-center w-100" style="height:100%;">
                        <img src="{{ asset('uploads/images/' . $queue_image) }}" alt="Queue Image" style="max-width:100%;max-height:80vh;object-fit:contain;" />
                    </div>
                @elseif(($queue_display_type ?? '') === 'link' && !empty($queue_video_link))
                    <div class="mb-2 d-flex align-items-center justify-content-center w-100" style="height:100%;">
                        <div style="position:relative;padding-bottom:56.25%;height:0;width:100%;">
                            <iframe src="{{ $queue_video_link }}" frameborder="0" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%;background:#000;"></iframe>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="row full-height"> 
                <div class="col-md-6" style="height: 100%;">
                    <div class="card card-olive fixed-height" style="width: 100%;">
                        <div class="card-header">
                            <h3 class="card-title">{{ _lang('ON QUEUE') }}</h3>
                        </div>
                        <div class="card-body scrollable" id="on-queue-list">
                            <!-- On Queue Names Here -->
                        </div>
                    </div>
                </div>
                <div class="col-md-6" style="height: 100%;">
                    <div class="card card-olive fixed-height" style="width: 100%;">
                        <div class="card-header">
                            <h3 class="card-title">{{ _lang('NOW SERVING') }}</h3>
                        </div>
                        <div class="card-body scrollable" id="now-serving-list">
                            <!-- Now Serving Names Here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row fixed-bottom-marquee align-items-center m-0 p-0">
    <div class="col-12 d-flex align-items-center justify-content-between bg-danger">
        <div class="mr-4" style="font-weight:1000;"> NEWS UPDATE: </div>
        <div class="marquee-text flex-grow-1 bg-olive" id="marquee-text">
            <!-- Marquee text goes here -->
            <marquee behavior="scroll" direction="left" scrollamount="6">{{ $news_message }}</marquee>
        </div>
        <div class="current-time ml-3"  style="font-weight:1000;" id="current-time"></div>
    </div>
</div>

@endsection

@section('js-script')
<script>
let autoRefresh = true;
let refreshInterval = null;
let ajaxInProgress = false;
let lastDisplayType = null;

function updateQueueDisplaySection() {
    $.get('/assistance-queue/display-data', function(res) {
        if(res.status === 1) {
            if (res.queue_display_type !== lastDisplayType) {
                let html = '';
                if(res.queue_display_type === 'video' && res.queue_video) {
                    html = `<div class=\"mb-2\"><video width=\"100%\" controls autoplay loop><source src=\"/uploads/videos/${res.queue_video}\" type=\"video/mp4\">Your browser does not support the video tag.</video></div>`;
                } else if(res.queue_display_type === 'image' && res.queue_image) {
                    html = `<div class=\"mb-2\"><img src=\"/uploads/images/${res.queue_image}\" alt=\"Queue Image\" style=\"max-width:100%;height:auto;\" /></div>`;
                } else if(res.queue_display_type === 'link' && res.queue_video_link) {
                    // Always convert YouTube URL to embed format and add autoplay (even on every refresh)
                    let link = res.queue_video_link.trim();
                    let embedUrl = link;
                    if (link.includes('youtube.com/watch?v=')) {
                        const videoId = link.split('v=')[1].split('&')[0];
                        embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
                    } else if (link.includes('youtu.be/')) {
                        const videoId = link.split('youtu.be/')[1].split(/[?&]/)[0];
                        embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
                    } else if (link.includes('youtube.com/embed/')) {
                        // Remove any existing autoplay param and add it at the end
                        embedUrl = link.replace(/([?&])autoplay=\d+(&)?/, '$1').replace(/[?&]$/, '');
                        embedUrl += (embedUrl.includes('?') ? '&' : '?') + 'autoplay=1';
                    }
                    html = `<div class=\"mb-2 d-flex align-items-center justify-content-center w-100\" style=\"height:100%;\"><div style=\"position:relative;padding-bottom:56.25%;height:0;width:100%;\"><iframe src=\"${embedUrl}\" frameborder=\"0\" allowfullscreen allow=\"autoplay\" style=\"position:absolute;top:0;left:0;width:100%;height:100%;background:#000;\"></iframe></div></div>`;
                }
                $('#queue-display-section').html(html);
                lastDisplayType = res.queue_display_type;
            }
        }
    });
}

function renderQueueSection(list, containerId, showType = false, highlightRed = false) {
    const container = $(containerId);
    container.empty();
    if (!list.length) {
        container.append('<div class="queue-name' + (highlightRed ? ' now-serving' : '') + '">-</div>');
        return;
    }
    list.forEach(item => {
        let html = `<div class='queue-name${highlightRed ? ' now-serving' : ''}'>${item.name}</div>`;
        if (showType && item.type) {
            html += `<div class='queue-type'>${item?.served_by?.full_name.replace(/_/g, ' ')}</div>`;
        }
        container.append(html);
    });
}

function fetchQueueData() {
    if (ajaxInProgress) return;
    ajaxInProgress = true;
    $.get('/assistance-queue/guest/data', function(res) {
        if (res.status === 1) {
            renderQueueSection(res.on_queue, '#on-queue-list');
            // Combine now_serving and now_serving_priority
            const combinedNowServing = [...res.now_serving, ...res.now_serving_priority];
            renderQueueSection(combinedNowServing, '#now-serving-list', true, true);
        }
    }).always(function() {
        ajaxInProgress = false;
    });
}

function startAutoRefresh() {
    if (refreshInterval) clearInterval(refreshInterval);
    refreshInterval = setInterval(() => {
        if (autoRefresh) fetchQueueData();
        updateQueueDisplaySection();
    }, 10000);
}

function updateCurrentTime() {
    const now = new Date();
    const options = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
    document.getElementById('current-time').textContent = "TIME: " + now.toLocaleTimeString([], options);
}
setInterval(updateCurrentTime, 1000);
updateCurrentTime();

$(document).ready(function() {
    fetchQueueData();
    updateQueueDisplaySection();
    startAutoRefresh();

    $('#manual-refresh').on('click', function() {
        fetchQueueData();
        updateQueueDisplaySection();
    });
    $('#auto-refresh-toggle').on('change', function() {
        autoRefresh = this.checked;
        if (autoRefresh) {
            startAutoRefresh();
        } else if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    });
});
</script>
@endsection