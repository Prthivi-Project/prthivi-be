<?php

use App\Http\Controllers\LandingPage\LandingPageManagementController;
use App\Http\Controllers\LandingPage\SectionImageController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(["prefix" => "v1",], function () {
    //
    Route::group(["as" => "landingpage.", "prefix" => "landing-page"], function () {
        Route::delete("/{id}", [LandingPageManagementController::class, "destroy"])->name("destroy");
        Route::put("/{id}", [LandingPageManagementController::class, "update"])->name("update");
        Route::post("/", [LandingPageManagementController::class, "store"])->name("store");
        Route::get("/", [LandingPageManagementController::class, "index"])->name("index");
        Route::put("/section-images/{sectionImages}", [SectionImageController::class, "update"])->name("section_images.update");
    });
});
