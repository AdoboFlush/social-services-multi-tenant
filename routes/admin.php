<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;

Route::group(['middleware' => ['install', 'isMaintenance']], function () {
    //Frontend Route
    Route::get('/', function () {
        return redirect('login');
    });

    Auth::routes(['verify' => true]);

    Route::get('/logout', '\App\Http\Controllers\Auth\LoginController@logout');
    // Route::get('register/{id?}', 'Auth\RegisterController@showRegistrationForm')->name('register');

    //Email Verification Success
    Route::get('email/verified/{id}/{email?}', 'Auth\VerificationController@verified_success');

    //Verify Change Email
    Route::get('verification/change_email/{id}/{$email}', 'Auth\VerificationController@change_email');

    //Reset Password
    Route::get('password/change_password/{token}/{expiration}', 'Auth\ForgotPasswordController@change_password');

    //Create Password
    Route::get('password/create/{token}/{expiration}', 'Auth\ForgotPasswordController@createPassword');
    Route::get('password/resend/{email}', 'Auth\ForgotPasswordController@resendToken');
    Route::get('password/expired/{email}', 'Auth\ForgotPasswordController@viewExpiredPage');

    Route::get('language/{language}', 'UserController@switchLanguage');

    Route::get('/view_member_id/{id}', 'IdRequestController@showMemberId');

    Route::group(['middleware' => ['auth', 'verified', 'has_page_permission', 'checkaccount', 'forceChangePassword']], function () {

        Route::get("/switch-theme", 'DashboardController@switchTheme');
        
        #####################################################
        Route::get('/voters', 'VoterController@index');
        Route::post('/voters', 'VoterController@index');

        Route::get('/voters/archived', 'VoterController@indexArchived');
        Route::post('/voters/archived', 'VoterController@indexArchived');

        Route::get('/voter/create', 'VoterController@create');
        Route::get('/voter/search', 'VoterController@search');
        Route::post('/voter/import', 'VoterController@import');
        Route::post('/voter/store', 'VoterController@store');
        Route::get('/voter/edit/{id}', 'VoterController@edit');
        Route::get('/voter/get/{id}', 'VoterController@get');
        Route::post('/voter/update', 'VoterController@update');
        Route::get('/voter/show/{id}', 'VoterController@show');

        Route::post('/voter/delete', 'VoterController@delete');
        Route::post('/voter/restore', 'VoterController@restore');
        Route::post('/voter/force_delete', 'VoterController@forceDelete');

        Route::prefix('voters')->group(function () {
            Route::prefix('tagging')->group(function () {
                Route::get('/', 'VoterController@taggingIndex')->name('voter.tagging.view');
                Route::get('export', 'VoterController@exportVoterTagDetails')->name('voter.tagging.export');
                Route::post('update/{voter_tag_detail}', 'VoterController@updateTagDetail')->name('voter.tagging.update');
                Route::get('activity_log', 'ActivityLogController@indexTaggingActivity');
                Route::post('activity_log', 'ActivityLogController@indexTaggingActivity');
                Route::get('activity_log/show/{id}', 'ActivityLogController@show');
            });
           
            Route::get('import_to_members', 'VoterController@importToMembers');
            Route::post('import_to_members', 'VoterController@importToMembers');
            Route::post('tagging/clear-field', 'VoterController@clearFieldTag');
        });

        Route::get('/senior_citizen_voters', 'VoterController@indexSeniorCitizenVoters');
        Route::post('/senior_citizen_voters', 'VoterController@indexSeniorCitizenVoters');

        Route::get('/tags', 'TagController@index');
        Route::post('/tags', 'TagController@index');
        Route::get('/tag/create', 'TagController@create');
        Route::post('/tag/store', 'TagController@store');
        Route::get('/tag/edit/{id}', 'TagController@edit');
        Route::post('/tag/update', 'TagController@update');
        Route::get('/tag/show/{id}', 'TagController@show');
        Route::post('/tag/delete', 'TagController@delete');
        Route::get('/tag/get_child_tags/{parent_id}', 'TagController@getChildTags');
        Route::get('/tag/get_child_tags_by_parent_name/{parent_name}', 'TagController@getChildTagsByParentName');

        Route::get('/social_services', 'SocialServiceAssistanceController@index');
        Route::post('/social_services', 'SocialServiceAssistanceController@index');
        Route::get('/social_services/create', 'SocialServiceAssistanceController@create');
        Route::get('/social_services/create/{id}', 'SocialServiceAssistanceController@createWithVoter');
        Route::get('/social_services/create/member/{id}', 'SocialServiceAssistanceController@createWithMember');
        Route::post('/social_service/store', 'SocialServiceAssistanceController@store');
        Route::get('/social_services/edit/{id}', 'SocialServiceAssistanceController@edit');
        Route::get('/social_services/get_current_control_number/{id}', 'SocialServiceAssistanceController@getCurrentControlNumber');
        Route::post('/social_service/update', 'SocialServiceAssistanceController@update');
        Route::get('/social_service/show/{id}', 'SocialServiceAssistanceController@show');
        Route::get('/social_service/release/{id}', 'SocialServiceAssistanceController@release');
        Route::post('/social_service/delete', 'SocialServiceAssistanceController@delete');
        Route::post('/social_service/update_status_multiple', 'SocialServiceAssistanceController@updateStatusMultiple');
        Route::get('/social_service/events/{request_type_id}', 'SocialServiceAssistanceController@getEventsByRequestTypeId');
        Route::get('/social_services/release/scan-id', 'SocialServiceAssistanceController@scanIdForRelease');

        Route::match(['get', 'post'], '/id_system', 'IdRequestController@index');
        Route::get('/id_system/requests/export', 'IdRequestController@export');
        Route::match(['get', 'post'], '/id_system/requests', 'IdRequestController@index');
        Route::get('/id_system/requests/create', 'IdRequestController@create');
        Route::get('/id_system/requests/create/{id}', 'IdRequestController@createFromMember');
        Route::post('/id_system/requests/store', 'IdRequestController@store');
        Route::get('/id_system/requests/edit/{id}', 'IdRequestController@edit');
        Route::get('/id_system/requests/preview/{id}', 'IdRequestController@preview');
        Route::get('/id_system/requests/multiple-preview', 'IdRequestController@multiplePreview');
        Route::get('/id_system/requests/multiple-download', 'IdRequestController@multipleDownload');
        Route::get('/id_system/requests/generate-id-per-page', 'IdRequestController@getIdsPerPage');
        Route::get('/id_system/requests/generate-multi-ids', 'IdRequestController@generateMultipleIds');
        Route::post('/id_system/requests/update', 'IdRequestController@update');
        Route::post('/id_system/requests/delete', 'IdRequestController@delete');
        Route::get('/id_system/requests/scan/{id}', 'IdRequestController@scanResult');
        Route::post('/id_system/requests/update_download_stats/{id_request}', 'IdRequestController@updateDownloadStats');

        Route::match(['get', 'post'], '/id_system/members', 'MemberController@index');
        Route::get('/id_system/members/create', 'MemberController@create');
        Route::get('/id_system/members/export', 'MemberController@export');
        Route::post('/id_system/members/store', 'MemberController@store');
        Route::get('/id_system/members/edit/{id}', 'MemberController@edit');
        Route::get('/id_system/members/show/{id}', 'MemberController@show');
        Route::post('/id_system/members/update', 'MemberController@update');
        Route::post('/id_system/members/delete', 'MemberController@delete');
        Route::get('/id_system/members/import_to_request', 'MemberController@importToRequests');
        Route::post('/id_system/members/import_to_request', 'MemberController@importToRequests');
        Route::get('/id_system/members/get/{id}', 'MemberController@get');
        Route::get('/id_system/members/search', 'MemberController@search');
        Route::post('/id_system/members/reset_password', 'MemberController@resetPassword');

        Route::match(['get', 'post'], '/id_system/templates', 'TemplateController@index');
        Route::get('/id_system/templates/create', 'TemplateController@create');
        Route::post('/id_system/templates/store', 'TemplateController@store');
        Route::get('/id_system/templates/edit/{id}', 'TemplateController@edit');
        Route::get('/id_system/templates/show/{id}', 'TemplateController@show');
        Route::post('/id_system/templates/update', 'TemplateController@update');
        #Route::post('/id_system/templates/delete', 'TemplateController@delete');

        Route::get('/notifications', 'NotificationController@index');
        Route::get('/notification/mark_all_as_read', 'NotificationController@markAllAsRead');

        Route::post('/activity', 'ActivityLogController@indexActivity');
        Route::post('/activity/all', 'ActivityLogController@indexAllActivity');

        Route::get('/reports/social_services/overview', 'ReportController@socialServicesOverview');
        Route::get('/reports/social_services/beneficiaries', 'ReportController@socialServicesBeneficiaries');
        Route::get('/reports/social_services/beneficiaries/export', 'ReportController@exportSocialServicesBeneficiaries');

        Route::match(['get', 'post'], '/events', 'EventController@index');
        Route::get('/events/create', 'EventController@create');
        Route::post('/events/store', 'EventController@store');
        Route::get('/events/edit/{id}', 'EventController@edit');
        Route::get('/events/show/{id}', 'EventController@show');
        Route::post('/events/update', 'EventController@update');
        Route::post('/events/delete', 'EventController@delete');

        Route::get('/events/assistance-beneficaries/{event}', 'EventController@assistanceBeneficiaries');
        Route::post('/events/assistance-beneficaries/scan-id/{event}', 'EventController@scanBeneficiaryId');
        Route::post('/events/assistance-beneficaries/release/{social_service_assistance}', 'EventController@releaseAssistanceFromIdScan');

        Route::match(['get', 'post'], '/events/{id}/attendees', 'EventController@indexAttendees');
        Route::get('/events/attendees/create/{event_id}', 'EventController@createAttendee');
        Route::post('/events/attendees/store', 'EventController@storeAttendee');
        Route::get('/events/attendees/edit/{id}', 'EventController@editAttendee');
        Route::get('/events/attendees/show/{id}', 'EventController@showAttendee');
        Route::post('/events/attendees/update', 'EventController@updateAttendee');
        Route::post('/events/attendees/delete', 'EventController@deleteAttendee');
        Route::post('/events/attendees/release', 'EventController@releaseAssistance');
        Route::post('/events/create-attendee-from-member/{event}', 'EventController@createAttendeeFromMember');
        Route::post('/events/create-attendee-from-qr/{event}/{id_number}', 'EventController@createAttendeeFromQR');
        Route::get('/events/{event}/released-percentage', 'EventController@getReleasedPercentage');

        Route::match(['get', 'post'], '/member_codes', 'MemberCodeController@index')->name("member_codes");
        Route::match(['post'], '/member_codes/generate', 'MemberCodeController@generate')->name("member_codes.generate");
        Route::get('member_codes/create', 'MemberCodeController@create')->name("member_codes.create");


        Route::prefix('poll')->group(function () {

            Route::get('overview', 'PollController@overviewIndex')->name('poll.elections.overview');

            Route::get('elections', 'PollController@electionIndex')->name('poll.elections.index');
            Route::get('elections/create', 'PollController@createElection')->name('poll.elections.create');
            Route::post('elections/store', 'PollController@storeElection')->name('poll.elections.store');
            Route::get('elections/edit/{poll_election}', 'PollController@editElection')->name('poll.elections.edit');
            Route::post('elections/update/{poll_election}', 'PollController@updateElection')->name('poll.elections.update');
            Route::post('elections/delete/{poll_election}', 'PollController@deleteElection')->name('poll.elections.delete');
            Route::get('elections/show/{poll_election}', 'PollController@showElectionDetails')->name('poll.elections.show');
            Route::get('elections/candidates/{poll_election}', 'PollController@getElectionCandidates')->name('poll.elections.candidates');
            Route::get('elections/watchers/{poll_election}', 'PollController@getElectionWatchers')->name('poll.elections.watchers');
            Route::post('elections/{poll_election}/candidates/store', 'PollController@addCandidateToElection')->name('poll.elections.candidates.store');
            Route::post('elections/{poll_election}/watchers/store', 'PollController@addWatcherToElection')->name('poll.elections.watchers.store');

            Route::get('elections/candidates/edit/{poll_election_candidate}', 'PollController@editElectionCandidate')
            ->name('poll.elections.candidates.edit');

            Route::get('elections/candidates/show/{poll_election_candidate}', 'PollController@showElectionCandidateDetails')
            ->name('poll.elections.candidates.show');
            
            Route::post('elections/candidates/update/{poll_election_candidate}', 'PollController@updateElectionCandidate')
            ->name('poll.elections.candidates.update');

            Route::get('elections/watchers/edit/{poll_election_candidate}', 'PollController@editElectionWatcher')
            ->name('poll.elections.watchers.edit');
            Route::post('elections/watchers/update/{poll_election_candidate}', 'PollController@updateElectionWatcher')
            ->name('poll.elections.watchers.update');

            Route::get('/election/{poll_election}/watcher/{poll_watcher}/candidates/get', 'PollController@getWatcherCandidates')->name('poll.election.watcher_candidates.get');

            Route::get('/election/{poll_election}/watcher/{poll_watcher}/candidates/show', 'PollController@showWatcherCandidates')->name('poll.election.watcher_candidates.show');
            
            Route::get('/election/{poll_election}/watcher/{poll_watcher}/activity-logs/show', 'PollController@showWatcherActivityLogFromAdmin')->name('poll.election.watcher.activity_logs.show');

            Route::get('/election/{poll_election}/watcher/{poll_watcher}/activity-logs/get', 'PollController@getWatcherActivityLogFromAdmin')->name('poll.election.watcher.activity_logs.get');
            
            Route::get('/election/{poll_election}/watcher/{poll_watcher}/candidates/export-csv', 'PollController@exportWatcherCandidatesCsv')->name('poll.election.watcher_candidates.export_csv');
            
            Route::get('elections/candidates/list/{poll_election}', 'PollController@electionCandidatesList')
            ->name('poll.elections.candidates.list');
            Route::get('elections/watchers/list/{poll_election}', 'PollController@electionWatchersList')
            ->name('poll.elections.watchers.list');

            Route::get('elections/{poll_election}/filtered-candidates', 'PollController@getFilteredElectionCandidates')->name('poll.elections.filtered.candidates');
            Route::get('elections/{poll_election}/filtered-watchers', 'PollController@getFilteredElectionWatchers')->name('poll.elections.filtered.watchers');

            Route::get('poll/elections/{poll_election}/leaderboard', 'PollController@getElectionLeaderboard')->name('poll.elections.leaderboard');

            Route::get('candidates', 'PollController@candidateIndex')->name('poll.candidates.index');
            Route::get('candidates/create', 'PollController@createCandidate')->name('poll.candidates.create');
            Route::post('candidates/store', 'PollController@storeCandidate')->name('poll.candidates.store');
            Route::get('candidates/edit/{poll_candidate}', 'PollController@editCandidate')->name('poll.candidates.edit');
            Route::post('candidates/update/{poll_candidate}', 'PollController@updateCandidate')->name('poll.candidates.update');
            Route::post('candidates/delete/{poll_candidate}', 'PollController@deleteCandidate')->name('poll.candidates.delete');
            Route::get('candidates/show/{poll_election}', 'PollController@showCandidate')->name('poll.candidates.show');
            
            Route::get('entries', 'PollController@entriesIndex')->name('poll.entries.index');
            Route::get('entries/create', 'PollController@createEntries')->name('poll.entries.create');
            Route::post('entries/store', 'PollController@storeEntries')->name('poll.entries.store');
            Route::get('entries/edit/{poll_entry}', 'PollController@editEntries')->name('poll.entries.edit');
            Route::post('entries/update/{poll_entry}', 'PollController@updateEntries')->name('poll.entries.update');
            Route::post('entries/delete/{poll_entry}', 'PollController@deleteEntries')->name('poll.entries.delete');
            Route::post('poll/entries/bulk-delete', 'PollController@bulkDeleteEntries')->name('poll.entries.bulk_delete');

            Route::get('watchers', 'PollController@watcherIndex')->name('poll.watchers.index');
            Route::get('watchers/create', 'PollController@createWatcher')->name('poll.watchers.create');
            Route::post('watchers/store', 'PollController@storeWatcher')->name('poll.watchers.store');
            Route::get('watchers/edit/{poll_watcher}', 'PollController@editWatcher')->name('poll.watchers.edit');
            Route::post('watchers/update/{poll_watcher}', 'PollController@updateWatcher')->name('poll.watchers.update');
            Route::post('watchers/delete/{poll_watcher}', 'PollController@deleteWatcher')->name('poll.watchers.delete');
            
            Route::get('watchers/user/search', 'PollController@searchUsers')->name('poll.watchers.user.search');
            Route::get('watchers/search', 'PollController@searchWatchers')->name('poll.watchers.search');
            Route::get('candidates/search', 'PollController@searchCandidates')->name('poll.candidates.search');

            Route::get('elections/candidates/export/{poll_election_candidate}', 'PollController@exportVotesBreakdown')
                ->name('poll.elections.candidates.export');

            Route::get('elections/candidates/export-cluster/{poll_election_candidate}', 'PollController@exportVotesBreakdownCluster')
                ->name('poll.elections.candidates.export_cluster');

            Route::prefix('guest')->group(function () {
                Route::get('/watcher/dashboard', 'PollController@watcherDashboardIndex')->name('poll.guest.watcher.dashboard');
                Route::get('/election/{poll_election}/candidates/get', 'PollController@watcherCandidatesList')->name('poll.guest.election.candidates.get');

                Route::post('/election/{poll_election}/candidates/{poll_election_candidate}/upsert-votes', 'PollController@upsertVotes')->name('poll.guest.election.candidates.upsert_votes');

                Route::get('/watcher/stats', 'PollController@getWatcherStats')->name('poll.guest.watcher.stats');
                Route::get('poll/guest/watcher/activity-logs/get', 'PollController@getWatcherActivityLogs')->name('poll.guest.watcher.activity_logs.get');
                Route::get('poll/guest/watcher/activity-logs/show', 'PollController@showWatcherActivityLog')->name('poll.guest.watcher.activity_logs.show');
            });

            Route::get('overview/refresh', 'PollController@refreshOverviewData')->name('poll.overview.refresh');

            // Candidates Per Barangay Report
            Route::get('reports/candidates_per_brgy', function() {
                return view('backend.poll.reports.candidates_per_brgy_report');
            })->name('poll.reports.candidates_per_brgy');
            Route::get('reports/candidates_per_brgy_data', 'PollController@candidatesPerBrgyData')->name('poll.reports.candidates_per_brgy_data');

            // Candidates Per Cluster Report
            Route::get('reports/candidates_per_cluster', function() {
                return view('backend.poll.reports.candidates_per_cluster');
            })->name('poll.reports.candidates_per_cluster');
            Route::get('reports/candidates_per_cluster_data', 'PollController@candidatesPerClusterData')->name('poll.reports.candidates_per_cluster_data');
            Route::get('reports/candidates_per_cluster_group_data', 'PollController@candidatesPerClusterGroupData')->name('poll.reports.candidates_per_cluster_group_data');

            // Candidate Comparison Report
            Route::get('reports/candidate_comparison_report', function() {
                return view('backend.poll.reports.candidate_comparison_report');
            })->name('poll.reports.candidate_comparison_report');
            Route::get('reports/candidate_comparison_data', 'PollController@candidateComparisonData')->name('poll.reports.candidate_comparison_data');

            // Candidate Ranking Report Endpoints
            Route::get('reports/candidate_ranking_by_brgy', 'PollController@candidateRankingByBrgy')->name('poll.reports.candidate_ranking_by_brgy');
            Route::get('reports/candidate_ranking_by_cluster', 'PollController@candidateRankingByCluster')->name('poll.reports.candidate_ranking_by_cluster');

            // Candidate Ranking Report View
            Route::get('reports/candidate_ranking', 'PollController@candidateRankingReport')->name('poll.reports.candidate_ranking');
           
        });

        #####################################################

        Route::prefix('assistance-queue')->group(function () {
            Route::get('/', 'AssistanceQueueController@index')->name('assistance-queue.index');
            Route::get('/table', 'AssistanceQueueController@queueTable')->name('assistance-queue.table');
            Route::get('get', 'AssistanceQueueController@get')->name('assistance-queue.get');
            Route::post('/reset', 'AssistanceQueueController@resetQueue')->name('assistance-queue.reset');
            Route::post('/store', 'AssistanceQueueController@store')->name('assistance-queue.store'); 
            Route::post('/update-status/{assistance_queue}', 'AssistanceQueueController@updateStatus')->name('assistance-queue.update-status');
            Route::delete('/cancel/{assistance_queue}', 'AssistanceQueueController@cancelQueue')->name('assistance-queue.cancel'); 
            Route::prefix('guest')->group(function () {
                Route::get('/', 'AssistanceQueueController@guestIndex')->name('guest.assistance-queue.index');
                Route::get('data', 'AssistanceQueueController@guestQueueData');
            }); 
            Route::post('/update-queue-settings', 'AssistanceQueueController@updateQueueSettings')->name('assistance-queue.update-queue-settings');
            Route::get('/display-data', 'AssistanceQueueController@guestQueueDisplayData')->name('assistance-queue.display-data');
        });

        #####################################################

        Route::get('/dashboard', 'DashboardController@index');

        Route::get('/require-change-password', 'UserController@viewForceChangePassword');
        Route::post('/require-change-password', 'UserController@submitForceChangePassword');

        //Profile Controller
        Route::get('profile/edit', 'ProfileController@edit');
        Route::post('profile/update', 'ProfileController@update');
        Route::post('profile/update_password', 'ProfileController@update_password');
        Route::post('profile/update_email', 'ProfileController@update_email');
        Route::post('profile/update_verification', 'ProfileController@update_verification');
        Route::get('profile/update_information_request', 'ProfileController@update_information_request');
        Route::post('profile/send_request', 'ProfileController@send_request');

        //Messaging Route
        Route::get('message/inbox', 'MessageController@inbox');
        Route::get('message/outbox', 'MessageController@outbox');
        Route::get('message/compose', 'MessageController@compose');
        Route::get('message/view_inbox/{id}', 'MessageController@view_inbox');
        Route::get('message/view_outbox/{id}', 'MessageController@view_outbox');
        Route::get('message/remove/{id}', 'MessageController@remove');
        Route::post('message/send', 'MessageController@send')->name('messages.store');
        Route::post('message/reply_message', 'MessageController@reply_message')->name('messages.reply_message');
        Route::post('message/bulk_action', 'MessageController@bulk_action');

        Route::prefix('voter_assistance')->group(function () {
            Route::prefix('events')->group(function () {
                Route::get('/', 'VoterAssistanceController@assistanceEventIndex')->name('voter_assistance.events.index');
                Route::get('create', 'VoterAssistanceController@createAssistanceEvent')->name('voter_assistance.events.create');
                Route::post('store', 'VoterAssistanceController@storeAssistanceEvent')->name('voter_assistance.events.store');
                Route::get('show/{assistance_event}', 'VoterAssistanceController@showAssistanceEventIndex')->name('voter_assistance.events.show');
                Route::get('edit/{assistance_event}', 'VoterAssistanceController@editAssistanceEvent')->name('voter_assistance.events.edit');
                Route::post('update/{assistance_event}', 'VoterAssistanceController@updateAssistanceEvent')->name('voter_assistance.events.update');
                Route::post('claim/qr/{assistance_event}', 'VoterAssistanceController@claimAssistanceByQR')->name('voter_assistance.claim.qr');
                Route::post('claim/{assistance_event}', 'VoterAssistanceController@claimAssistance')->name('voter_assistance.claim');
                Route::get('/generate-multi-coupons/{assistance_event}', 'VoterAssistanceController@generateMultipleCoupons')->name('voter_assistance.generate-coupon');
                Route::post('multi-delete', 'VoterAssistanceController@deleteMultipleAssistanceEvents')->name('voter_assistance.events.multi-delete');
                Route::get('/stats/{assistance_event}', 'VoterAssistanceController@getEventStats')->name('voter_assistance.events.stats');
            });

            
        });

        Route::prefix('paymaster')->group(function () {
            Route::prefix('voter_assistance')->group(function () {
                Route::prefix('events')->group(function () {
                    Route::get('/', 'VoterAssistanceController@assistanceEventPayMasterIndex')->name('paymaster.voter_assistance.events.index');
                    Route::get('show/{assistance_event}', 'VoterAssistanceController@showAssistanceEventPayMasterIndex')->name('paymaster.voter_assistance.events.show');
                });
            });
        });

        //Admin Only Routes
        Route::group(['middleware' => ['auth'], 'prefix' => 'admin'], function () {

            //Staff Controller
            Route::resource('staffs', 'StaffController');

            Route::get('advertisements/list', 'AdvertisementController@getAdvertisements')->name('advertisements.list');
            Route::resource('advertisements', 'AdvertisementController')->only([
                'create',
                'edit',
                'index',
                'store',
                'destroy',
                'update'
            ]);

            Route::get('dashboard', 'DashboardController@index')->name("admin.dashboard");

            //User Controller
            Route::get('users/status/{account_status}', 'UserController@index');

            Route::get('users', [
                'uses' => 'UserController@index',
                'permission' => 'users_view'
            ]);

            Route::get('users/varify/{user_id}', [
                'uses' => 'UserController@varify',
                'permission' => 'users_document_update'
            ]);

            Route::get('users/unvarify/{user_id}', [
                'uses' => 'UserController@unvarify',
                'permission' => 'users_document_update'
            ]);

            Route::post('security_settings/edit/{id}', [
                'uses' => 'UserController@editSecurityPassword',
                'permission' => 'security_password_edit',
            ]);
            Route::post('security_settings/reset/{id}', [
                'uses' => 'UserController@resetSecurityPassword',
                'permission' => 'security_password_edit'
            ]);

            Route::get('users/documents/all', [
                'uses' => 'UserController@getDocuments',
                'permission' => 'users_document_view',
            ]);

            Route::get('users/documents/{user_id}', [
                'uses' => 'UserController@view_documents',
                'permission' => 'users_document_view'
            ]);
            Route::post('users/documents/{user_id}', [
                'uses' => 'UserController@updateStatusById',
                'permission' => 'users_document_view',
                'as' => 'user.update'
            ]);
            Route::post('users/documents/update/{user_id}', [
                'uses' => 'UserDocumentController@updateByUserId',
                'permission' => 'users_document_view',
                'as' => 'user.update.document'
            ]);

            Route::post('users/documents/upload/{user_id}', [
                'uses' => 'UserDocumentController@multiUpload',
                'permission' => 'users_document_view',
                'as' => 'user.upload.document'
            ]);

            Route::get('users/documents', [
                'uses' => 'UserController@documents',
                'permission' => 'users_document_view',
            ]);

            Route::post('users/documents', [
                'uses' => 'UserController@filterDocuments',
                'permission' => 'users_document_view',
                'as' => 'user.documents'
            ]);

            Route::post('users/document/status', [
                'uses' => 'UserController@updateKycStatus',
                'permission' => 'users_document_update',
                'as' => 'user.documents_status'
            ]);

            Route::post('users/search', [
                'uses' => 'UserController@searchUserBy',
                'as' => 'users.search',
            ]);

            Route::get('/users/create', [
                'uses' => 'UserController@create',
                'as' => 'users.create',
                'permission' => 'users_create'
            ]);

            Route::get('/users/show/{id}', [
                'uses' => 'UserController@show',
                'as' => 'users.view',
                'permission' => 'users_view'
            ]);

            Route::get('/users/{id}/edit/', [
                'uses' => 'UserController@edit',
                'permission' => 'users_view'
            ]);

            Route::match(['get', 'post'], '/users/edit/{account_number}', [
                'uses' => 'UserController@edit',
                'as' => 'users.edit',
                'permission' => 'users_update'
            ]);

            Route::post('/users/store', [
                'uses' => 'UserController@registerViaAdmin',
                'as' => 'users.store',
                'permission' => 'users_create'
            ]);

            Route::post('/users/resend', [
                'uses' => 'UserController@resendVerificationEmail',
                'as' => 'users.resend_verification_email',
                'permission' => 'users_update'
            ]);

            Route::put('/users/{id}', [
                'uses' => 'UserController@update',
                'as' => 'users.update',
                'permission' => 'users_update'
            ]);

            Route::delete('/users/destroy/{id}', [
                'uses' => 'UserController@destroy',
                'as' => 'users.destroy',
                'permission' => 'users_delete'
            ]);


            //Account Controller
            Route::resource('accounts', 'AccountController');

            Route::group(['middleware' => ['auth'], 'prefix' => 'administration'], function () {
                Route::get('/roles_permissions', [
                    'uses' => 'PermissionController@index',
                    'permission' => 'settings_view'
                ]);

                Route::get('/roles_permissions/permission', [
                    'as' => 'permission.create',
                    'uses' => 'PermissionController@create',
                    'permission' => 'settings_view'
                ]);

                Route::post('/roles_permissions/permission/store', [
                    'as' => 'permission.store',
                    'uses' => 'PermissionController@store',
                    'permission' => 'settings_view'
                ]);

                Route::delete('/roles_permissions/permission/{id}', [
                    'uses' => 'PermissionController@destroy',
                    'permission' => 'settings_view'
                ]);

                Route::get('/roles_permissions/role', [
                    'as' => 'role.create',
                    'uses' => 'RoleController@create',
                    'permission' => 'settings_view'
                ]);

                Route::post('/roles_permissions/role/store', [
                    'as' => 'role.store',
                    'uses' => 'RoleController@store',
                    'permission' => 'settings_view'
                ]);

                Route::get('/roles_permissions/role/{id}', [
                    'as' => 'role.edit',
                    'uses' => 'RoleController@edit',
                    'permission' => 'settings_view'
                ]);

                Route::put('/roles_permissions/role/update/{id}', [
                    'as' => 'role.update',
                    'uses' => 'RoleController@update',
                    'permission' => 'settings_view'
                ]);

                Route::get('/activity_log', [
                    'as' => 'activity_logs.view',
                    'uses' => 'ActivityLogController@indexAllActivity',
                    'permission' => 'admin_view'
                ]);

                Route::post('/activity_log', [
                    'as' => 'activity_logs.view',
                    'uses' => 'ActivityLogController@indexAllActivity',
                ]);

                Route::get('/activity_log/show/{id}', [
                    'as' => 'activity_logs.view',
                    'uses' => 'ActivityLogController@show',
                    'permission' => 'admin_view'
                ]);

                Route::get('/activity_log/delete', [
                    'as' => 'activity_logs.delete',
                    'uses' => 'ActivityLogController@delete',
                    'permission' => 'admin_view'
                ]);

                Route::get('/database/backup', [
                    'uses' => 'DatabaseBackupController@index',
                    'as' => 'database.backup',
                    'permission' => 'database_backup_view'
                ]);

                Route::get('/database/backup/create', [
                    'uses' => 'DatabaseBackupController@backup',
                    'as' => 'database.backup',
                    'permission' => 'database_backup_create'
                ]);

                Route::post('upload_logo', 'UtilityController@upload_logo')->name('general_settings.update_logo');
                Route::get('backup_database', 'UtilityController@backup_database')->name('utility.backup_database');
                Route::get('message_template', 'UtilityController@message_template')->name('utility.message_template');
                Route::get('welcome_message', 'SettingsController@showWelcomeMessage')->name('utility.welcome_message');
                Route::post('welcome_message/{message}', 'SettingsController@updateWelcomeMessage')
                    ->name('utility.update_welcome_message');

                Route::group(['middleware' => ['auth'], 'prefix' => 'settings'], function () {
                    Route::get('/service_maintenance', [
                        'as' => 'maintenance.service.view',
                        'uses' => 'SettingsController@serviceMaintenance',
                        'permission' => 'maintenance_service_view'
                    ]);
                    Route::post('/service_maintenance', [
                        'as' => 'maintenance.service.update',
                        'uses' => 'SettingsController@serviceMaintenance',
                        'permission' => 'maintenance_service_update'
                    ]);
                    Route::match(['get', 'post'], '{category}', [
                        'as' => 'settings.view',
                        'uses' => 'SettingsController@index',
                        'permission' => 'settings_view'
                    ]);
                });
            });

            Route::get('ticket/create/', [
                'uses' => 'TicketController@index',
                'permission' => 'tickets_view'
            ]);

            Route::get('ticket/get/{status}', [
                'uses' => 'TicketController@get',
                'permission' => 'tickets_view'
            ]);

            Route::post('ticket/create/', [
                'uses' => 'TicketController@create',
                'permission' => 'tickets_create'
            ]);

            Route::get('ticket/show/{id}', [
                'uses' => 'TicketController@showConversation',
                'permission' => 'tickets_view'
            ]);

            Route::get('get-canned-messages/{id}', [
                'uses' => 'TicketController@getCannedMessages',
                'permission' => 'tickets_view'
            ]);

            Route::get('ticket/canned-messages', [
                'uses' => 'TicketController@viewCannedMessages',
                'permission' => 'tickets_view'
            ]);

            Route::get('ticket/canned-message/{id?}', [
                'uses' => 'TicketController@viewCannedMessage',
                'permission' => 'tickets_view'
            ]);

            Route::post('ticket/canned-message/{id?}', [
                'uses' => 'TicketController@updateOrCreateCannedMessage',
                'permission' => 'tickets_view'
            ]);

            Route::post('ticket/canned-message/delete/{id?}', [
                'uses' => 'TicketController@deleteCannedMessage',
                'permission' => 'tickets_view'
            ]);

            Route::get('ticket/{status?}', [
                'uses' => 'TicketController@show',
                'permission' => 'tickets_view'
            ]);

            Route::post('ticket/edit/{id}', 'TicketController@addMessage');


            //Language Controller
            Route::resource('languages', 'LanguageController');

            Route::post('/import/users/review', [
                'as' => 'users.review',
                'uses' => 'UserController@reviewCsv',
                'permission' => 'users_create'
            ]);

            Route::post('/import/users', [
                'uses' => 'UserController@registerViaCsv',
                'permission' => 'users_create'
            ]);

            Route::get('/users/download_incorrect_balance_users', [
                'uses' => 'UserController@generateIncorrectBalanceUserCSV',
                'permission' => 'users_create'
            ]);

            Route::get('/dashboard/users', [
                'uses' => 'DashboardController@getAllDashboardUsers',
                'permission' => 'dashboard_users_view'
            ]);

            Route::get('/dashboard/export/users', [
                'uses' => 'DashboardController@getDashboardExport',
                'permission' => 'dashboard_users_export'
            ]);

            Route::get('/dashboard/export/user-balances', [
                'uses' => 'DashboardController@getDashboardExportBalance',
                'permission' => 'dashboard_balances_export'
            ]);

            /**
             * Risk management
             */
            Route::prefix('risk-management')->group(function () {

                Route::get('countries', [
                    'uses' => 'RiskManagementController@availableCountries',
                    'as' => 'risk.management.view.country',
                    'permission' => 'risk_countries_view'
                ]);

                Route::post('countries/{country}', [
                    'uses' => 'RiskManagementController@updateCountry',
                    'as' => 'risk.management.update.country',
                    'permission' => 'risk_countries_view'
                ]);

                Route::get('sessions', 'UserController@userSessions')->name('risk.management.user-session.view');

                Route::get('/history', [
                    'uses' => 'UserController@userSessionsHistory',
                    'permission' => 'users_view'
                ]);
            });
                   
        });

        //User Only Route
        Route::group(['middleware' => ['auth', 'forceChangePassword'], 'prefix' => 'user'], function () {

            Route::get('language/{language}', 'UserController@switchLanguage');
            //Client Overview
            Route::get('overview', 'ClientController@overview');
            Route::match(['get', 'post'], 'security_settings', 'UserController@securityPassword')->middleware('businessOnly');
            Route::match(['get', 'post'], 'security_settings/create', 'UserController@createSecurityPassword')->middleware('businessOnly');
            Route::post('security_settings/confirm', 'UserController@confirmSecurityPassword')->middleware('businessOnly');

            //Upload Document Page
            Route::get('submit_documents', 'UserDocumentController@create');
            //Submit Documents
            Route::post('upload/documents', [
                'uses' => 'UserDocumentController@store',
                'as' => 'upload.document'
            ]);

            //search user
            Route::post('search', 'UserController@search');

            //Referral Link
            Route::get('profile/referral_link', 'ProfileController@referral_link');

            Route::post('generate_code', 'TwoFactorController@generate');
            Route::post('verify_code', 'TwoFactorController@verify');

            //Withrawal
            Route::post('withdraw/debit_amount', 'WithdrawController@debitAmount');

            Route::delete('delete_beneficiary', 'ClientController@deleteBeneficiary');

            Route::post('update-change-password', 'UserController@updateForceChangePassword');

            Route::get('notifications', [
                'uses' => 'NotificationController@userList',
                'as' => 'user_notification.list',
            ]);
        });
    });
});

// Route::get('/installation', 'Install\InstallController@index');
// Route::get('install/database', 'Install\InstallController@database');
// Route::post('install/process_install', 'Install\InstallController@process_install');
// Route::get('install/create_user', 'Install\InstallController@create_user');
// Route::post('install/store_user', 'Install\InstallController@store_user');
// Route::get('install/system_settings', 'Install\InstallController@system_settings');
// Route::post('install/finish', 'Install\InstallController@final_touch');