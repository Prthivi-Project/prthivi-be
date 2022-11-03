<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\LandingPage\LandingPageManagementController;
use App\Http\Controllers\LandingPage\SectionImageController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\ProductImageController;
use App\Http\Controllers\Store\StoreController;
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

    require __DIR__ . '/auth.php';

    Route::group(["as" => "landingpage.", "prefix" => "landing-page", 'middleware' => 'jwt.verify'], function () {
        Route::delete("/{id}", [LandingPageManagementController::class, "destroy"])->name("destroy");

        Route::put("/{id}", [LandingPageManagementController::class, "update"])->name("update");

        Route::post("/", [LandingPageManagementController::class, "store"])->name("store");

        Route::get("/", [LandingPageManagementController::class, "index"])
            ->withoutMiddleware('jwt.verify')
            ->name("index");
        Route::put("/section-images/{sectionImages}", [SectionImageController::class, "update"])
            ->name("section_images.update");
    });

    Route::group(["as" => "products.", "prefix" => "products"], function () {
        Route::put("/images", [ProductImageController::class, 'update'])->name("images.update");
        Route::delete("/{id}", [ProductController::class, "destroy"])->name("destroy");
        Route::put("/{id}", [ProductController::class, "update"])->name("update");
        Route::get("/{id}", [ProductController::class, "show"])->name("show");
        Route::post("/", [ProductController::class, "store"])->name("store");
        Route::get("/", [ProductController::class, "index"])->name("index");
    });

    Route::group(["as" => "store.", "prefix" => "stores"], function () {
        Route::delete('/{id}', [StoreController::class, "destroy"])->name("delete");
        Route::put('/{id}', [StoreController::class, "update"])->name("update");
        Route::post('/', [StoreController::class, "create"])->name("create");
        Route::get('/', [StoreController::class, "index"]);
    });

    Route::group(["as" => "user.", "prefix" => "users", 'middleware' => 'jwt.verify'], function () {
        Route::group(["as" => 'roles.', 'prefix' => 'roles'], function () {
            Route::post('/', [RoleController::class, 'store'])->name("store");
        });
    });
});
