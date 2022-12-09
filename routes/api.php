<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\LandingPage\LandingPageManagementController;
use App\Http\Controllers\LandingPage\SectionImageController;
use App\Http\Controllers\Product\CategoryProductController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\ProductImageController;
use App\Http\Controllers\Store\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

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

Route::group(["prefix" => "v1",], function () {

    require __DIR__ . '/auth.php';

    Route::group([
        "as" => "landingpage.",
        "prefix" => "landing-page",
        'middleware' => 'jwt.verify'
    ], function () {
        Route::as('section_images')->prefix('section-images')->group(function () {
            Route::delete("/{id}", [SectionImageController::class, "destroy"])
                ->name("delete");
            Route::put("/{id}", [SectionImageController::class, "update"])
                ->name("update");
            Route::post("/", [SectionImageController::class, "store"])
                ->name("store");
        });

        Route::delete("/{id}", [LandingPageManagementController::class, "destroy"])
            ->name("destroy");

        Route::put("/{id}", [LandingPageManagementController::class, "update"])
            ->name("update");

        Route::get("/{id}", [LandingPageManagementController::class, "show"])
            ->withoutMiddleware('jwt.verify')
            ->name("show");

        Route::post("/", [LandingPageManagementController::class, "store"])
            ->name("store");

        Route::get("/", [LandingPageManagementController::class, "index"])
            ->withoutMiddleware('jwt.verify')
            ->name("index");
    });

    Route::group(["as" => "products.", "prefix" => "products", 'middleware' => 'jwt.verify'], function () {
        Route::put("/images/{id}", [ProductImageController::class, 'update'])->name("images.update");
        Route::post("/images", [ProductImageController::class, 'store'])->name("images.store");

        Route::post("/{id}/category", [CategoryProductController::class, 'attachCategoriesProduct']);
        Route::delete("/{id}/category", [CategoryProductController::class, 'detachCategoryProduct']);
        Route::delete("/{product}", [ProductController::class, "destroy"])->name("destroy");
        Route::put("/{id}", [ProductController::class, "update"])->name("update");
        Route::get("/{id}", [ProductController::class, "show"])->name("show")->withoutMiddleware('jwt.verify');
        Route::post("/", [ProductController::class, "store"])->name("store");
        Route::get("/", [ProductController::class, "index"])->name("index")->withoutMiddleware('jwt.verify');
    });

    Route::group(["as" => "categories.", "prefix" => "categories", 'middleware' => 'jwt.verify'], function () {
        Route::put('/{id}', [CategoryController::class, "update"])->name("update");
        Route::post('/', [CategoryController::class, "store"])->name("create")->middleware('verified');
        Route::get('/', [CategoryController::class, "index"])->withoutMiddleware('jwt.verify');
    });


    Route::group(["as" => "store.", "prefix" => "stores", 'middleware' => 'jwt.verify'], function () {
        Route::put('/{slug}', [StoreController::class, "update"])->name("update");
        Route::delete('/{slug}', [StoreController::class, "destroy"])->name("delete");
        Route::post('/', [StoreController::class, "create"])->name("create")->middleware('verified');
        Route::get('/', [StoreController::class, "index"])->withoutMiddleware('jwt.verify');
    });

    Route::group(["as" => "user.", "prefix" => "users", 'middleware' => 'jwt.verify'], function () {
        Route::group(["as" => 'roles.', 'prefix' => 'roles'], function () {
            Route::post('/', [RoleController::class, 'store'])->name("store");
        });
    });
});
