<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\SatuSehat\OnBoarding\PatientController;
use App\Http\Controllers\Api\SatuSehat\OnBoarding\LocationController;
use App\Http\Controllers\Api\SatuSehat\OnBoarding\PractitionerController;
use App\Http\Controllers\Api\SatuSehat\Interoperability\AllergyController;
use App\Http\Controllers\Api\SatuSehat\Interoperability\CarePlanController;
use App\Http\Controllers\Api\SatuSehat\Interoperability\SpecimenController;
use App\Http\Controllers\Api\SatuSehat\Interoperability\ConditionController;
use App\Http\Controllers\Api\SatuSehat\Interoperability\EncounterController;
use App\Http\Controllers\Api\SatuSehat\Interoperability\ProcedureController;
use App\Http\Controllers\Api\SatuSehat\OnBoarding\SubOrganizationController;
use App\Http\Controllers\Api\SatuSehat\Interoperability\MedicationController;
use App\Http\Controllers\Api\SatuSehat\Interoperability\CompositionController;
use App\Http\Controllers\Api\SatuSehat\Interoperability\ObservationController;
use App\Http\Controllers\Api\SatuSehat\Interoperability\ImagingStudyController;
use App\Http\Controllers\Api\SatuSehat\Interoperability\ServiceRequestController;
use App\Http\Controllers\Api\SatuSehat\Interoperability\DiagnosticReportController;
use App\Http\Controllers\Api\SatuSehat\Interoperability\MedicationRequestController;
use App\Http\Controllers\Api\SatuSehat\Interoperability\ClinicalImpressionController;
use App\Http\Controllers\Api\SatuSehat\Interoperability\MedicationDispenseController;
use App\Http\Controllers\Api\SatuSehat\Interoperability\MedicationStatementController;
use App\Http\Controllers\Api\SatuSehat\Interoperability\QuestionnaireResponseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('/v1/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

Route::group(['middleware' => 'auth:sanctum', 'prefix' => '/v1/satusehat-integration'], function () {
    Route::get('/satusehat-token', [AuthController::class, 'generate_satusehat_token']);

    Route::group(['prefix' => '/sub-organization'], function () {
        Route::get('/{sub_organization_id}', [SubOrganizationController::class, 'get_sub_organization']);
        Route::post('/create', [SubOrganizationController::class, 'create_sub_organization']);
        Route::put('/update/{sub_organization_id}', [SubOrganizationController::class, 'update_sub_organization']);
        Route::patch('/update/{sub_organization_id}', [SubOrganizationController::class, 'update_partial_sub_organization']);
    });

    Route::group(['prefix' => '/location'], function () {
        Route::get('/{location_id}', [LocationController::class, 'get_location']);
        Route::post('/create', [LocationController::class, 'create_location']);
        Route::put('/update/{location_id}', [LocationController::class, 'update_location']);
        Route::patch('/update/{location_id}', [LocationController::class, 'update_partial_location']);
    });

    Route::group(['prefix' => '/patient'], function () {
        Route::get('/{patient_id}', [PatientController::class, 'get_patient']);
        Route::post('/create', [PatientController::class, 'create_patient']);
    });

    Route::group(['prefix' => '/practitioner'], function () {
        Route::get('/{practitioner_id}', [PractitionerController::class, 'get_practitioner']);
    });

    Route::group(['prefix' => '/encounter'], function () {
        Route::post('/create', [EncounterController::class, 'create_encounter']);
        Route::put('/update/{encounter_id}', [EncounterController::class, 'update_encounter']);
    });

    Route::group(['prefix' => '/condition'], function () {
        Route::post('/create', [ConditionController::class, 'create_condition']);
    });

    Route::group(['prefix' => '/allergy'], function () {
        Route::post('/create', [AllergyController::class, 'create_allergy']);
    });

    Route::group(['prefix' => '/observation'], function () {
        Route::post('/create', [ObservationController::class, 'create_observation']);
    });

    Route::group(['prefix' => '/service-request'], function () {
        Route::post('/create', [ServiceRequestController::class, 'create_service_request']);
    });

    Route::group(['prefix' => '/specimen'], function () {
        Route::post('/create', [SpecimenController::class, 'create_specimen']);
    });

    Route::group(['prefix' => '/diagnostic-report'], function () {
        Route::post('/create', [DiagnosticReportController::class, 'create_diagnostic_report']);
    });

    Route::group(['prefix' => '/procedure'], function () {
        Route::post('/create', [ProcedureController::class, 'create_procedure']);
    });

    Route::group(['prefix' => '/medication'], function () {
        Route::post('/create', [MedicationController::class, 'create_medication']);
    });

    Route::group(['prefix' => '/medication-request'], function () {
        Route::post('/create', [MedicationRequestController::class, 'create_medication_request']);
    });

    Route::group(['prefix' => '/medication-dispense'], function () {
        Route::get('/', [MedicationDispenseController::class, 'get_medication_dispense']);
        Route::post('/create', [MedicationDispenseController::class, 'create_medication_dispense']);
    });

    Route::group(['prefix' => '/medication-statement'], function () {
        Route::post('/create', [MedicationStatementController::class, 'create_medication_statement']);
    });

    Route::group(['prefix' => '/composition'], function () {
        Route::post('/create', [CompositionController::class, 'create_composition']);
    });

    Route::group(['prefix' => '/clinical-impression'], function () {
        Route::post('/create', [ClinicalImpressionController::class, 'create_clinical_impression']);
    });

    Route::group(['prefix' => '/care-plan'], function () {
        Route::post('/create', [CarePlanController::class, 'create_care_plan']);
    });

    Route::group(['prefix' => '/questionnaire-response'], function () {
        Route::post('/create', [QuestionnaireResponseController::class, 'create_questionnaire_response']);
    });

    Route::group(['prefix' => '/imaging-study'], function () {
        Route::post('/create', [ImagingStudyController::class, 'create_imaging_study']);
    });
});
