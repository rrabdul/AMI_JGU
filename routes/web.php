<?php

use App\Http\Controllers\ApproveController;
use App\Http\Controllers\AuditPlanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MyAuditController;
use App\Http\Controllers\ObservationController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\StandardCategoryController;
use App\Http\Controllers\StandardCriteriaController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------composer require laravel/socialite

| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
})->name('home');
// Route::get('/', [SendEmailController::class, 'index']);

require __DIR__ . '/auth.php';

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');
Route::get('/login/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('/login/google/callback', [GoogleController::class, 'handleCallback']);
//Profile
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'showEditForm'])->name('profile.edit');
    Route::post('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
});
//change password
Route::middleware(['auth'])->group(function () {
    Route::get('change-password', [SettingController::class, 'change_password'])->name('change-password');
    Route::post('change-password/update', [SettingController::class, 'update_password'])->name('change-password.update');
});

Route::get('/documentation', [DashboardController::class, 'documentation'])->name('documentation');

//Audit Plan
Route::group(['middleware' => ['auth','role:admin, lpm']], function () {
    Route::group(['prefix' => 'audit_plan'], function () {
        Route::any('/', [AuditPlanController::class, 'index'])->name('audit_plan.index');
        Route::get('/data', [AuditPlanController::class, 'data'])->name('audit_plan.data');
        Route::delete('/delete', [AuditPlanController::class, 'delete'])->name('audit_plan.delete');
        Route::any('/add', [AuditPlanController::class, 'add'])->name('audit_plan.add');

        //Add Audit Plan Auditor Standard
        Route::get('/standard/{id}', [AuditPlanController::class, 'standard'])->name('audit_plan.standard');
        Route::get('/data_auditor/{id}', [AuditPlanController::class, 'data_auditor'])->name('audit_plan.data_auditor');
        Route::get('/standard/create/{id}', [AuditPlanController::class, 'create'])->name('audit_plan.standard.create');
        Route::any('/standard/create_auditor_std/{id}', [AuditPlanController::class, 'create_auditor_std'])->name('create_auditor_std');
        Route::get('/standard/edit/{id}', [AuditPlanController::class, 'edit_auditor_std'])->name('audit_plan.standard.edit');
        Route::any('/standard/update_auditor_std/{id}', [AuditPlanController::class, 'update_auditor_std'])->name('update_auditor_std');
    });
});

Route::get('/get_standard_criteria_by_category_ids', [AuditPlanController::class, 'getStandardCriteriaByCategoryIds'])->name('DOC.get_standard_criteria_id_by_id');
Route::get('/edit_audit/{id}', [AuditPlanController::class, 'edit'])->name('edit_audit');
Route::any('/update_audit/{id}', [AuditPlanController::class, 'update'])->name('update_audit');

//MY Audit
Route::group(['middleware' => ['auth', 'role:admin, auditee']], function () {
    Route::group(['prefix' => 'my_audit'], function () {
        Route::get('/', [MyAuditController::class, 'index'])->name('my_audit.index');
        Route::get('/data', [MyAuditController::class, 'data'])->name('my_audit.data');
        Route::any('/update/{id}', [MyAuditController::class, 'update'])->name('my_audit.update');
        Route::get('/obs/{id}', [MyAuditController::class, 'obs'])->name('my_audit.obs');
        Route::any('/show/{id}', [MyAuditController::class, 'show'])->name('my_audit.show');
        Route::any('/my_standard/{id}', [MyAuditController::class, 'my_standard'])->name('my_audit.my_standard');
        Route::any('/edit_rtm/{id}', [MyAuditController::class, 'edit_rtm'])->name('my_audit.edit_rtm');
        Route::any('/rtm/{id}', [MyAuditController::class, 'rtm'])->name('my_audit.rtm');
        Route::any('/delete_file/{id}', [MyAuditController::class, 'deleteFile'])->name('my_audit.delete_file');
        Route::any('/delete_link/{id}', [MyAuditController::class, 'deleteLink'])->name('my_audit.delete_link');
        Route::any('/delete_file_rtm/{id}', [MyAuditController::class, 'deleteFileRTM'])->name('my_audit.delete_file_rtm');
        Route::any('/delete_link_rtm/{id}', [MyAuditController::class, 'deleteLinkRTM'])->name('my_audit.delete_link_rtm');
    });
});

//Observations
Route::group(['middleware' => ['auth', 'role:admin, auditor']], function () {
    Route::group(['prefix' => 'observations'], function () {
        Route::get('/', [ObservationController::class, 'index'])->name('observations.index');
        Route::get('/data', [ObservationController::class, 'data'])->name('observations.data');
        Route::get('/create/{id}', [ObservationController::class, 'create'])->name('observations.create');
        Route::any('/make/{id}', [ObservationController::class, 'make'])->name('observations.make');
        Route::any('/saveDraft/{id}', [ObservationController::class, 'saveDraft'])->name('observations.saveDraft');
        Route::get('/edit/{id}', [ObservationController::class, 'edit'])->name('observations.edit');
        Route::any('/remark_doc/{id}', [ObservationController::class, 'remark_doc'])->name('observations.remark_doc');
        Route::get('/remark/{id}', [ObservationController::class, 'remark'])->name('observations.remark');
        Route::any('/update_remark/{id}', [ObservationController::class, 'update_remark'])->name('observations.update_remark');
        Route::any('/remark_rtm/{id}', [ObservationController::class, 'remark_rtm'])->name('observations.remark_rtm');
        Route::get('/rtm/{id}', [ObservationController::class, 'rtm'])->name('observations.rtm');
    });
});

//Print PDF
Route::get('/view/{id}', [PDFController::class, 'view'])->name('pdf.view');
Route::get('/att/{id}/{type}', [PDFController::class, 'att'])->name('pdf.att');
Route::get('/form_cl/{id}/{type}', [PDFController::class, 'form_cl'])->name('pdf.form_cl');
Route::get('/ks_kts/{id}/{type}', [PDFController::class, 'ks_kts'])->name('pdf.ks_kts');
Route::get('/meet_report/{id}/{type}', [PDFController::class, 'meet_report'])->name('pdf.meet_report');
Route::get('/ptp_ptk/{id}/{type}', [PDFController::class, 'ptp_ptk'])->name('pdf.ptp_ptk');

//LPM
Route::group(['middleware' => ['auth', 'role:admin, lpm']], function () {
    Route::group(['prefix' => 'lpm'], function () {
        Route::get('/', [ApproveController::class, 'lpm'])->name('lpm.index');
        Route::get('/approve_data', [ApproveController::class, 'approve_data'])->name('lpm.approve_data');
        Route::any('/lpm_apv_audit/{id}', [ApproveController::class, 'lpm_apv_audit'])->name('lpm.lpm_apv_audit');
        Route::get('/lpm_edit/{id}', [ApproveController::class, 'lpm_edit'])->name('lpm.lpm_edit');
        Route::any('/lpm_standard/{id}', [ApproveController::class, 'lpm_standard'])->name('lpm.lpm_standard');
        Route::get('/rtm_edit/{id}', [ApproveController::class, 'rtm_edit'])->name('lpm.rtm_edit');
    });
});

//Warek
Route::group(['middleware' => ['auth', 'role:approver']], function () {
    Route::group(['prefix' => 'approver'], function () {
        Route::get('/', [ApproveController::class, 'approver'])->name('approver.index');
        Route::get('/approve_data', [ApproveController::class, 'approve_data'])->name('approver.approve_data');
        Route::get('/app_rtm/{id}', [ApproveController::class, 'app_rtm'])->name('approver.app_rtm');
    });
});

//RTM
Route::group(['middleware' => ['auth', 'role:admin, lpm']], function () {
    Route::group(['prefix' => 'rtm'], function () {
        Route::get('/', [ApproveController::class, 'rtm'])->name('rtm.index');
        Route::get('/rtm_edit/{id}', [ApproveController::class, 'rtm_edit'])->name('rtm.rtm_edit');
        Route::get('/rtm_data', [ApproveController::class, 'rtm_data'])->name('rtm.rtm_data');
    });
});

Route::get('log-viewers', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->middleware(['can:log-viewers.read']);

Route::group(['prefix' => 'setting', 'middleware' => ['auth']], function () {
    Route::group(['prefix' => 'manage_account'], function () {
        Route::group(['prefix' => 'users'], function () { //route to manage users
            Route::any('/', [UserController::class, 'index'])->name('users.index');
            Route::get('/data', [UserController::class, 'data'])->name('users.data');
            Route::any('/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
            Route::any('/reset_password/{id}', [UserController::class, 'reset_password'])->name('users.reset_password');
            Route::delete('/delete', [UserController::class, 'delete'])->name('users.delete');
        });
        Route::group(['prefix' => 'roles'], function () { //route to manage roles
            Route::any('/', [RoleController::class, 'index'])->name('roles.index');
            Route::get('/data', [RoleController::class, 'data'])->name('roles.data');
            Route::any('/edit/{id}', [RoleController::class, 'edit'])->name('roles.edit');
            Route::delete('/destroy', [RoleController::class, 'destroy'])->name('roles.destroy');
        });
        Route::group(['prefix' => 'permissions'], function () { //route to manage permissions
            Route::any('/', [PermissionController::class, 'index'])->name('permissions.index');
            Route::get('/data', [PermissionController::class, 'data'])->name('permissions.data');
            Route::get('/view/{id}', [PermissionController::class, 'view'])->name('permissions.view');
            Route::get('/view/{id}/users', [PermissionController::class, 'view_users_data'])->name('permissions.view_users_data');
            Route::get('/view/{id}/roles', [PermissionController::class, 'view_roles_data'])->name('permissions.view_roles_data');
        });
    });
    //route category
    Route::group(['prefix' => 'manage_standard'], function () {
        Route::group(['prefix' => 'category'], function () {
            Route::any('/', [StandardCategoryController::class, 'category'])->name('standard_category.category');
            Route::any('/category_add', [StandardCategoryController::class, 'category_add'])->name('standard_category.category_add');
            Route::get('/data', [StandardCategoryController::class, 'data'])->name('standard_category.data');
            Route::get('/category_edit/{id}', [StandardCategoryController::class, 'category_edit'])->name('standard_category.category_edit');
            Route::put('/category_update/{id}', [StandardCategoryController::class, 'category_update'])->name('standard_category.category_update');
            Route::delete('/delete', [StandardCategoryController::class, 'delete'])->name('standard_category.delete');
        });

        //route criteria
        Route::group(['prefix' => 'criteria'], function () {
            Route::any('/', [StandardCriteriaController::class, 'criteria'])->name('standard_criteria.criteria');
            Route::get('/criteria_edit/{id}', [StandardCriteriaController::class, 'criteria_edit'])->name('standard_criteria.criteria_edit');
            Route::get('/data', [StandardCriteriaController::class, 'data'])->name('standard_criteria.data');
            Route::put('/criteria_update/{id}', [StandardCriteriaController::class, 'criteria_update'])->name('standard_criteria.criteria_update');
            Route::delete('/delete', [StandardCriteriaController::class, 'delete'])->name('standard_criteria.delete');

            // Route standard_statement
            Route::get('/standard_statement', [StandardCriteriaController::class, 'standard_statement'])->name('standard_criteria.standard_statement');
            Route::any('/standard_criteria.standard_statement.create', [StandardCriteriaController::class, 'create'])->name('standard_criteria.standard_statement.create');
            Route::get('/data_standard_statement', [StandardCriteriaController::class, 'data_standard_statement'])->name('standard_criteria.standard_statement.data_standard_statement');
            Route::get('/show/standard_statement/{id}', [StandardCriteriaController::class, 'show'])->name('show.standard_statement');
            Route::get('/edit/standard_statement/{id}', [StandardCriteriaController::class, 'edit'])->name('edit.standard_statement');
            Route::put('/update_standard_statement/standard_statement/{id}', [StandardCriteriaController::class, 'update_standard_statement'])->name('update_standard_statement');
            Route::delete('/delete_standard_statement', [StandardCriteriaController::class, 'delete_standard_statement'])->name('delete_standard_statement');

            //Route Indicator
            Route::get('/indicator', [StandardCriteriaController::class, 'indicator'])->name('standard_criteria.indicator.index');
            Route::get('/data_indicator', [StandardCriteriaController::class, 'data_indicator'])->name('standard_criteria.data_indicator');
            Route::get('/standard_criteria.indicator.create', [StandardCriteriaController::class, 'create_indicator'])->name('standard_criteria.indicator.create');
            Route::any('/add/indicator', [StandardCriteriaController::class, 'store_indicator'])->name('store_indicator.indicator');
            Route::get('/edit_indicator/indicator/{id}', [StandardCriteriaController::class, 'edit_indicator'])->name('edit_indicator.indicator');
            Route::put('/update_indicator/{id}', [StandardCriteriaController::class, 'update_indicator'])->name('update_indicator');
            Route::delete('/delete_indicator', [StandardCriteriaController::class, 'delete_indicator'])->name('indicator.delete_indicator');

            // Route Review Document
            Route::get('/review_docs', [StandardCriteriaController::class, 'review_docs'])->name('standard_criteria.review_docs');
            Route::get('/data_docs', [StandardCriteriaController::class, 'data_docs'])->name('standard_criteria.data_docs');
            Route::get('/standard_criteria.review_docs.create', [StandardCriteriaController::class, 'create_docs'])->name('standard_criteria.review_docs.create');
            Route::post('/add/store_docs', [StandardCriteriaController::class, 'store_docs'])->name('store_docs.review_docs');
            Route::get('/edit_docs/review_docs/{id}', [StandardCriteriaController::class, 'edit_docs'])->name('edit_docs.review_docs');
            Route::put('/update_docs/review_docs/{id}', [StandardCriteriaController::class, 'update_docs'])->name('update_docs.review_docs');
            Route::delete('/delete_docs', [StandardCriteriaController::class, 'delete_docs'])->name('delete_docs.review_docs');
        });
            Route::get('/get_standard_statement_id_by_id', [StandardCriteriaController::class, 'getStandardStatementId'])->name('DOC.get_standard_statement_id_by_id');
            Route::get('/get_indicator_id_by_id', [StandardCriteriaController::class, 'getIndicatorId'])->name('DOC.get_indicator_id_by_id');

        Route::group(['prefix' => 'hod_ami'], function () {
            Route::any('/', [SettingController::class, 'hod_ami'])->name('hod_ami.index');
        });
    });

    Route::group(['prefix' => 'manage_unit'], function () {
        Route::group(['prefix' => 'hod_ami'], function () {
            Route::any('/', [SettingController::class, 'hod_ami'])->name('hod_ami.index');
        });
    });
});
