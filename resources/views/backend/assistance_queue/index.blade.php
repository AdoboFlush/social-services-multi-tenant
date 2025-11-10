@extends('backend.assistance_queue.layout')

@section('tab-content')

<style>
    .queue-type-card { margin-bottom: 2rem; border-radius: 10px; box-shadow: 0 2px 8px #e0e0e0; }
    .queue-section-title { font-weight: bold; margin-top: 1rem; font-size: 1.1rem; }
    .queue-list {
        min-height: 40px;
        margin-bottom: 1rem;
        max-height: 320px; /* or any height you prefer */
        overflow-y: auto;
    }
    .on-queue-badge { background: #ffc107; color: #212529; }
    .serving-badge { background: #28a745; }
    .btn-serve { background: #007bff; color: #fff; }
    .btn-complete { background: #28a745; color: #fff; }
    .list-group-item { display: flex; align-items: center; justify-content: space-between; }
    @media (min-width: 992px) {
        .modal-dialog.desktop-max-width {
            max-width: 30vw;
        }
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <a href="{{route('guest.assistance-queue.index', false, [])}}" target="_blank"><i class="fas fa-external-link-alt"></i> Go to Queue Board </a>

                    <div class="float-right">
                        <span id="last-updated-time" class="ml-2 text-muted mr-2" style="font-size: 0.9rem;">Last updated: {{ now()->format('F j, Y, g:i A') }}</span>
                        <button id="refreshData" class="btn btn-primary btn-sm">
                            <i class="fa fa-sync"></i> Refresh Data
                        </button>
                        <button id="toggleAutoRefresh" class="btn btn-secondary btn-sm ml-2">
                            <i class="fa fa-play"></i> Auto Refresh
                        </button>
                        @can('assistance_queue_reset')
                        <button id="resetQueue" class="btn btn-danger btn-sm ml-2">
                            <i class="fa fa-sync"></i> Reset Queue
                        </button>
                        @endcan
                        <button id="toggleNewsUpdate" class="btn btn-warning btn-sm ml-2">
                            <i class="fa fa-cog"></i> Settings
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body bg-info py-2">
                                    <div id="request-type-filters" class="d-flex flex-wrap align-items-center">
                                        <span class="mr-3 font-weight-bold"><i class="fa fa-filter mr-2"> </i>Filter by Request Type:</span>
                                        @foreach(\App\AssistanceQueue::REQUEST_TYPES as $type)
                                            <div class="form-check form-check-inline mb-0">
                                                <input class="form-check-input request-type-checkbox" type="checkbox" id="filter-{{ Str::slug($type) }}" value="{{ $type }}" checked>
                                                <label class="form-check-label" for="filter-{{ Str::slug($type) }}">{{ $type }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="queue-board-row"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('content-modal')

<div class="modal fade" id="createQueueModal" tabindex="-1" role="dialog" aria-labelledby="createQueueModalLabel" aria-hidden="true">
  <div class="modal-dialog desktop-max-width" role="document">
    <form id="createQueueForm" autocomplete="off">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="createQueueModalLabel">Add to Queue [ <span id="modalTypeLabel" style="font-weight: 1000;"></span> ]</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="type" id="queueTypeInput">
            <div class="form-group">
                <label for="queueNameInput">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" id="queueNameInput" required autocomplete="off">
            </div>
            <div class="form-group">
                <label for="queueRemarksInput">Remarks</label>
                <textarea class="form-control" name="remarks" id="queueRemarksInput" rows="2"></textarea>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success"><i class="fa fa-plus"></i> Add to Queue</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- News Update Modal -->
<div class="modal fade" id="queueSettingsModal" tabindex="-1" role="dialog" aria-labelledby="queueSettingsModalLabel" aria-hidden="true">
  <div class="modal-dialog desktop-max-width" role="document">
    <form id="queueSettingsForm" autocomplete="off">
      <div class="modal-content">
        <div class="modal-header bg-warning">
          <h5 class="modal-title" id="queueSettingsModalLabel">Update Queue Settings</h5>
          <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="displayTypeInput">Display Type</label>
                <select class="form-control" name="display_type" id="displayTypeInput" required>
                    <option value="video" @if(($queue_display_type ?? '') == 'video') selected @endif>Video</option>
                    <option value="image" @if(($queue_display_type ?? '') == 'image') selected @endif>Image</option>
                    <option value="link" @if(($queue_display_type ?? '') == 'link') selected @endif>Link</option>
                </select>
            </div>
            <div class="form-group">
                <label for="newsImageInput">Upload Image File (jpg, png, jpeg)</label>
                @if(!empty($queue_image))
                    <div class="mb-2">
                        <img src="{{ asset('uploads/images/' . $queue_image) }}" alt="Queue Image" style="max-width:100%;height:auto;" />
                    </div>
                @endif
                <input type="file" class="form-control" name="news_image" id="newsImageInput" accept="image/png,image/jpeg,image/jpg">
            </div>
            <div class="form-group">
                <label for="newsVideoLinkInput">Video Link (YouTube)</label>
                <input type="text" class="form-control" name="news_video_link" id="newsVideoLinkInput" value="{{ $queue_video_link ?? '' }}" placeholder="https://youtube.com/...">
            </div>
            <div class="form-group">
                <label for="newsVideoInput">Upload Video File (mp4)</label>
                @if(!empty($queue_video))
                    <div class="mb-2">
                        <video width="100%" controls>
                            <source src="{{ asset('uploads/videos/' . $queue_video) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                @endif
                <input type="file" class="form-control" name="news_video" id="newsVideoInput" accept="video/mp4">
            </div>
            <div class="form-group">
                <label for="newsMessageInput">Message</label>
                <textarea class="form-control" name="news_message" id="newsMessageInput" rows="5" required>{{ $news_message }}</textarea>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning"><i class="fa fa-save"></i> Save Message</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>

@endsection

@section('js-script')

<script>
 $(function () {

    const REQUEST_TYPES = @json(\App\AssistanceQueue::REQUEST_TYPES);
    const REQUEST_TYPE_LABELS = @json(\App\AssistanceQueue::REQUEST_TYPE_LABELS);
    const STATUS_ON_QUEUE = "on_queue";
    const STATUS_PROCESSING = "processing";
    const STATUS_COMPLETED = "completed";

    let activeTypes = [...REQUEST_TYPES];

    $(document).on('change', '.request-type-checkbox', function() {
        activeTypes = $('.request-type-checkbox:checked').map(function(){ return $(this).val(); }).get();
        renderQueueBoard(lastQueueData || []);
    });

    let lastQueueData = [];
    function renderQueueBoard(data) {
        lastQueueData = data;
        const boardRow = $('#queue-board-row');
        boardRow.empty();
        REQUEST_TYPES.forEach(type => {
            if (!activeTypes.includes(type)) return;
            const queues = data.filter(q => q.type === type);
            const onQueue = queues.filter(q => q.status === STATUS_ON_QUEUE);
            const processing = queues.filter(q => q.status === STATUS_PROCESSING);

            let onQueueHtml = onQueue.length
                ? onQueue.map(q => `
                    <li class="list-group-item">
                        <div>
                            <i class="fa fa-user mr-1"></i>
                            <b>${q.name}</b>
                            <span class="badge on-queue-badge ml-2">#${q.sequence_number}</span>
                            <div class="text-muted small">Remarks: ${q.remarks ? q.remarks : '<em>None</em>'}</div>
                        </div>
                        <div>
                            <button class="btn btn-serve btn-sm ml-2" data-id="${q.id}" data-action="serve">
                                <i class="fa fa-play"></i> Serve
                            </button>
                            <button class="btn btn-danger cancel-queue btn-sm" data-id="${q.id}" data-action="cancel">
                                <i class="fa fa-close"></i> Cancel
                            </button>
                        </div>
                    </li>
                `).join('')
                : '<li class="list-group-item text-muted">No one on queue</li>';

            let servingHtml = processing.length
                ? processing.map(q => `
                    <li class="list-group-item">
                        <div>
                            <i class="fa fa-user-check mr-1"></i>
                            <b>${q.name}</b>
                            <span class="badge serving-badge ml-2">#${q.sequence_number}</span>
                            <div class="text-muted small">Remarks: ${q.remarks ? q.remarks : '<em>None</em>'}</div>
                            <div class="text-info small">Serving by: <b>${q.served_by ? q.served_by?.full_name : 'N/A'}</b></div>
                        </div>
                        <button class="btn btn-complete btn-sm" data-id="${q.id}" data-action="complete">
                            <i class="fa fa-check"></i> Complete
                        </button>
                    </li>
                `).join('')
                : '<li class="list-group-item text-muted">No one is being served</li>';

            boardRow.append(`
                <div class="col-md-4 mb-2">
                    <div class="card queue-type-card">
                        <div class="card-header bg-olive text-white d-flex align-items-center" style="gap: 10px;">
                            <div class="flex-grow-1">
                                <h5 class="mb-0"><i class="fa fa-layer-group mr-2"></i>${REQUEST_TYPE_LABELS[type] || type}</h5>
                            </div>
                            <button class="btn btn-light btn-sm font-weight-bold new-queue-btn ml-auto" data-type="${type}">
                                <i class="fa fa-plus-circle text-primary"></i> New Queue
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="queue-section-title"><i class="fa fa-user-clock mr-1"></i> Serving (${processing.length})</div>
                                    <ul class="queue-list list-group">${servingHtml}</ul>
                                </div>
                                <div class="col-md-12">
                                    <div class="queue-section-title"><i class="fa fa-list-ol mr-1"></i> On Queue (${onQueue.length})</div>
                                    <ul class="queue-list list-group">${onQueueHtml}</ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        });
    }

    function fetchQueueData() {
        $.get('/assistance-queue/get', function(res) {
            if(res.status === 1) {
                renderQueueBoard(res.data);
                $('#last-updated-time').text('Last updated: ' + (new Date()).toLocaleString());
            }
        });
    }

    $('#refreshData').on('click', fetchQueueData);

    let autoRefresh = false, autoRefreshInterval = null;
    $('#toggleAutoRefresh').on('click', function() {
        autoRefresh = !autoRefresh;
        $(this).html(autoRefresh ? '<i class="fa fa-pause"></i> Stop Auto Refresh' : '<i class="fa fa-play"></i> Auto Refresh');
        if(autoRefresh) {
            fetchQueueData();
            autoRefreshInterval = setInterval(fetchQueueData, 5000);
        } else {
            clearInterval(autoRefreshInterval);
        }
    });

    // Delegate serve/complete button actions
    $('#queue-board-row').on('click', 'button[data-action]', function() {
        const id = $(this).data('id');
        const action = $(this).data('action');
        let status = '';
        if(action === 'serve') status = STATUS_PROCESSING;
        if(action === 'complete') status = STATUS_COMPLETED;

        if(!status) return;

        $.ajax({
            url: '/assistance-queue/update-status/' + id,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            success: function(res) {
                if(res.status === 1) {
                    fetchQueueData();
                    swal(`Queue status successfully updated to "${status}"`, { icon: "success" });
                } else {
                    swal(res.message || 'Action failed.', { icon: "error" });
                }
            },
            error: function() {
                alert('Server error.');
            }
        });
    });

    $('#queue-board-row').on('click', '.cancel-queue', function() {
        const id = $(this).data('id');
        swal({
            title: "Are you sure?",
            text: "You want to cancel this queue record ?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                 $.ajax({
                    url: '/assistance-queue/cancel/' + id,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(res) {
                        if(res.status === 1) {
                            fetchQueueData();
                            swal(`Canceled successfully"`, { icon: "success" });
                        } else {
                            swal(res.message || 'Action failed.', { icon: "error" });
                        }
                    },
                    error: function() {
                        alert('Server error.');
                    }
                });
            }
        });
    });

    // Initial load
    fetchQueueData();

    // Open modal and set type
    $('#queue-board-row').on('click', '.new-queue-btn', function() {
        const type = $(this).data('type');
        $('#queueTypeInput').val(type);
        $('#modalTypeLabel').text(REQUEST_TYPE_LABELS[type] || type);
        $('#queueNameInput').val('');
        $('#queueRemarksInput').val('');
        $('#createQueueModal').modal('show');
        setTimeout(() => { $('#queueNameInput').focus(); }, 500);
    });

    // Handle form submission
    $('#createQueueForm').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const data = $form.serialize();
        $.ajax({
            url: '/assistance-queue/store',
            method: 'POST',
            data: data + '&_token={{ csrf_token() }}',
            success: function(res) {
                if(res.status === 1) {
                    swal(`Added successfully`, { icon: "success" });
                    $form[0].reset();
                    fetchQueueData();
                    setTimeout(() => { $('#queueNameInput').focus(); }, 200);
                } else {
                   swal(res.message || 'Failed to add.', { icon: "error" });
                }
            },
            error: function(xhr) {
                let msg = 'Server error.';
                if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                $('#createQueueAlert').removeClass('alert-success').addClass('alert-danger').removeClass('d-none').text(msg);
            }
        });
    });

    $('#resetQueue').on('click', function() {
        swal({
            title: "Are you sure?",
            text: "You want to reset the entire queue ?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willReset) => {
            if (willReset) {
                $.ajax({
                    url: '/assistance-queue/reset',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(res) {
                        if(res.status === 1) {
                            fetchQueueData();
                            swal(`Queue reset successfully`, { icon: "success" });
                        } else {
                            swal(res.message || 'Action failed.', { icon: "error" });
                        }
                    },
                    error: function() {
                        swal('Server error.', { icon: "error" });
                    }
                });
            }
        });
    });

    // Toggle News Update Modal
    $('#toggleNewsUpdate').on('click', function() {
        $('#queueSettingsModal').modal('show');
    });

    // Optionally handle queue settings form submission here
    $('#queueSettingsForm').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const formData = new FormData($form[0]);
        formData.append('_token', '{{ csrf_token() }}');
        $.ajax({
            url: '{{ route("assistance-queue.update-queue-settings", false, []) }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                $('#queueSettingsModal').modal('hide');
                swal('Queue settings saved!', { icon: 'success' });
                setTimeout(function() { location.reload(); }, 1000); // Refresh page after 1s
            },
            error: function(xhr) {
                let msg = 'Server error.';
                if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                swal(msg, { icon: 'error' });
            }
        });
    });

    // Remove video preview if a new file is selected
    $('#newsVideoInput').on('change', function() {
        var videoPreview = $(this).closest('.form-group').find('video');
        if (videoPreview.length) {
            videoPreview.parent().remove(); // Remove the containing div
        }
    });
});

</script>

@endsection