<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', 'API\Auth\AuthController@register');
Route::post('/login', 'API\Auth\AuthController@login');
Route::post('/forgot-password', 'API\Auth\ForgotPasswordController@forgotPassword');
Route::post('/reset-password', 'API\Auth\ForgotPasswordController@resetPassword');
Route::post('/create-organization', 'API\Organization\OrganizationController@createOrganization');
Route::post('/get-org-register', 'API\Organization\OrganizationController@getOrganizationsForRegister');

//Landing Page
Route::post('/get-home-trainings', 'API\Home\HomeController@allHomePageTrainings');

Route::middleware('auth:api')->group(function () {
    //Common
    Route::post('/auth-details', 'API\UserController@authDetails');
    //Route::post('/get-register-org', 'API\Organization\OrganizationController@getOrganizationsForRegister');
    Route::post('/logout', 'API\Auth\AuthController@logout');
    Route::post('/change-password', 'API\Auth\AuthController@changePassword');

    /********************************** Start Organization  **********************************/
    Route::post('/get-org-approved', 'API\Organization\OrganizationController@getOrganizationForApproved');
    Route::post('/create-org-training', 'API\Organization\TrainingController@createOrgTraining');
    Route::post('/get-org-training', 'API\Organization\TrainingController@allOrgTrainings');
    //Route::post('/add-user-org-training', 'API\Organization\OrgTrainingUser@allOrgTrainings');
    //Route::post('/get-user-org-training', 'API\Organization\OrgTrainingUser@allOrgTrainings');     

    /********************************** End Organization **********************************/


    /********************************** Start Admin **********************************/
    Route::post('/get-orgs', 'API\Admin\OrganizationController@getAllOrganizations');
    Route::post('/get-org-approve', 'API\Admin\OrganizationController@getAllNewRegisterOrganizations');
    Route::post('/org-approved', 'API\Admin\OrganizationController@approvedOrganization');
    // Route::post('/get-new-orgs', 'API\Admin\OrganizationController@getAllNewRegisterOrganizations');

    /********************************** End Admin **********************************/


    /********************************** Start Learning Provider**********************************/
    //Training
    Route::post('/create-training', 'API\LearningProvider\TrainingController@createTraining');
    Route::post('/update-training', 'API\LearningProvider\TrainingController@updateTraining');
    Route::post('/get-training', 'API\LearningProvider\TrainingController@getTraining');
    Route::post('/all-trainings', 'API\LearningProvider\TrainingController@allTrainings');
    Route::post('/delete-training', 'API\LearningProvider\TrainingController@deleteTraining');
    Route::post('/lp-training-details', 'API\LearningProvider\TrainingController@getTrainingDetailsForMeeting');
    Route::post('/lp-public-training', 'API\LearningProvider\TrainingController@addFreeTraining');
    Route::post('/get-dashboard-data', 'API\LearningProvider\DashboardController@getDashboardData');
    Route::post('/update-training-min', 'API\LearningProvider\TrainingController@updateTrainingTime');


    // Training User
    Route::post('/lp/add-training-user', 'API\LearningProvider\LPUserTraining@addLearningProviderTrainingUser');
    Route::post('/lp/import-training-user', 'API\LearningProvider\LPUserTraining@importLearningProviderTrainingUser');
    Route::post('/lp/get-training-users', 'API\LearningProvider\LPUserTraining@getTrainingUsers');

    ////// Provider User

    Route::post('/lpu/get-trainings', 'API\LearningProvider\ProviderUser\TrainingController@getTrainingUsersWise');
    Route::post('/lpu/update-join-status', 'API\LearningProvider\ProviderUser\TrainingController@updateTrainingJoinStatus');
    //Add Learning Provider user 
    /**********************************END Learning Provider**********************************/
    //Admin 

});
