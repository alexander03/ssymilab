<?php

use App\Http\Controllers\Equipment\MaintenanceController;
use App\Http\Controllers\Equipment\PreventativeController;
use App\Http\Controllers\Equipment\InstallationController;
use App\Http\Controllers\Equipment\ProximomantenimientoController;
use App\Http\Controllers\General\IndexController;
use App\Http\Controllers\Maintenance\ActiveIngredientController;
use App\Http\Controllers\Maintenance\BrandController;
use App\Http\Controllers\Maintenance\ReasonController;
use App\Http\Controllers\Maintenance\CategoryController;
use App\Http\Controllers\Maintenance\PersonController;
use App\Http\Controllers\Maintenance\ProductController;
use App\Http\Controllers\Maintenance\UnitController;
use App\Http\Controllers\Purchase\EntryGuideController;
use App\Http\Controllers\Purchase\InstallmentController;
use App\Http\Controllers\Purchase\RequirementController;
use App\Http\Controllers\Purchase\PurchaseController;
use App\Http\Controllers\Report\ReportLotController;
use App\Http\Controllers\Report\ReportStockController;
use App\Http\Controllers\Report\ReportProductSerieController;
use App\Http\Controllers\Report\ReportPurchaseController;
use App\Http\Controllers\Report\ReportProductSupplierController;
use App\Http\Controllers\Report\ReportSupplierController;
use App\Http\Controllers\Report\ReportRequirementController;
use App\Http\Controllers\Report\ReportExchangeController;
use App\Http\Controllers\Report\ReportSaleCheckoutController;
use App\Http\Controllers\Sale\OrderController;
use App\Http\Controllers\Sale\ProformaController;
use App\Http\Controllers\Sale\SaleCheckoutController;
use App\Http\Controllers\Sale\SaleController;
use App\Http\Controllers\Sale\CreditNoteController;
use App\Http\Controllers\Sale\ComisionesController;
use App\Http\Controllers\Sale\ComisionessupervisorController;
use App\Http\Controllers\Sale\SaleGuideController;
use App\Http\Controllers\Sale\CuentasxcobrarController;
use App\Http\Controllers\Sale\AnularfacturaController;
use App\Http\Controllers\Security\ActionController;
use App\Http\Controllers\Security\MenuCategoryController;
use App\Http\Controllers\Security\MenuOptionController;
use App\Http\Controllers\Store\KardexController;
use App\Http\Controllers\Store\LotController;
use App\Http\Controllers\Store\ProductSerieController;
use App\Http\Controllers\Store\ReferralGuideController;
use App\Http\Controllers\Store\StockController;
use App\Http\Controllers\Store\StoreDocumentController;
use App\Http\Controllers\Store\ShippingFormatController;
use App\Http\Controllers\System\BranchOfficeController;
use App\Http\Controllers\System\CardController;
use App\Http\Controllers\System\CheckoutController;
use App\Http\Controllers\System\CompanyController;
use App\Http\Controllers\System\TransferMethodController;
use App\Http\Controllers\Users\ProfileController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Users\UserTypeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [IndexController::class, 'index'])->name('root');
Route::get('/files/{id}', [ProductController::class, 'download_file'])->name('product-file');
Route::get('/installation-file/{id}/{name}', [InstallationController::class, 'download_file'])->name('installation-file');

//Clear Cache facade value:
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    return '<h1>Cache facade value cleared</h1>';
});
//Reoptimized class loader:
Route::get('/optimize', function() {
    $exitCode = Artisan::call('optimize');
    return '<h1>Reoptimized class loader</h1>';
});
//Route cache:
Route::get('/route-cache', function() {
    $exitCode = Artisan::call('route:cache');
    return '<h1>Routes cached</h1>';
});
//Clear Route cache:
Route::get('/route-clear', function() {
    $exitCode = Artisan::call('route:clear');
    return '<h1>Route cache cleared</h1>';
});
//Clear View cache:
Route::get('/view-clear', function() {
    $exitCode = Artisan::call('view:clear');
    return '<h1>View cache cleared</h1>';
});
//Clear Config cache:
Route::get('/config-cache', function() {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});


Route::group([
    'middleware' => ['auth:sanctum', 'verified']
], function () {

    /**
     *  DASHBOARD
     */

    Route::get('dashboard', [IndexController::class, 'dashboard'])->name('dashboard');


    /**
     * SEGURIDAD
     */

    Route::resource('menu-category', MenuCategoryController::class)->only(['index', 'create', 'edit'])->names([
        'index' => 'menu-category',
        'create' => 'create-menu-category',
        'edit' => 'edit-menu-category',
    ]);

    Route::resource('menu-option', MenuOptionController::class)->only(['index', 'create', 'edit'])->names([
        'index' => 'menu-option',
        'create' => 'create-menu-option',
        'edit' => 'edit-menu-option',
    ]);

    Route::resource('action', ActionController::class)->only(['index', 'create', 'edit'])->names([
        'index' => 'action',
        'create' => 'create-action',
        'edit' => 'edit-action',
    ]);


    /**
     * USUARIOS
     */

    Route::resource('user-type', UserTypeController::class)->only(['index', 'create', 'edit'])->names([
        'index' => 'user-type',
        'create' => 'create-user-type',
        'edit' => 'edit-user-type',
    ]);
    Route::get('user-type/{user_type}/assign-permission', [UserTypeController::class, 'assign_permission'])->name('assign-permission');

    Route::resource('profile', ProfileController::class)->only(['index'])->names([
        'index' => 'profile'
    ]);

    Route::resource('user', UserController::class)->only(['index', 'create', 'edit'])->names([
        'index' => 'user',
        'create' => 'create-user',
        'edit' => 'edit-user',
    ]);



    /**
     * MANTENIMIENTO
     */

    Route::resource('person', PersonController::class)->only(['index', 'create', 'edit'])->names([
        'index' => 'person',
        'create' => 'create-person',
        'edit' => 'edit-person',
    ]);

    Route::get('user-person/{person}/pdf-person', [PersonController::class, 'pdf'])->name('pdf-person');
    Route::get('user-person/{person}/pdf-migrar', [PersonController::class, 'migrar'])->name('pdf-migrar');

    Route::resource('brand', BrandController::class)->only(['index', 'create', 'edit'])->names([
        'index' => 'brand',
        'create' => 'create-brand',
        'edit' => 'edit-brand',
    ]);

    Route::get('user-brand/{brand}/pdf-brand', [BrandController::class, 'pdf'])->name('pdf-brand');

    Route::resource('reason', ReasonController::class)->only(['index', 'create', 'edit'])->names([
        'index' => 'reason',
        'create' => 'create-reason',
        'edit' => 'edit-reason',
    ]);

    Route::resource('category', CategoryController::class)->only(['index', 'create', 'edit'])->names([
        'index' => 'category',
        'create' => 'create-category',
        'edit' => 'edit-category',
    ]);

    Route::get('user-category/{category}/pdf-category', [CategoryController::class, 'pdf'])->name('pdf-category');

    Route::resource('unit', UnitController::class)->only(['index', 'create', 'edit'])->names([
        'index' => 'unit',
        'create' => 'create-unit',
        'edit' => 'edit-unit',
    ]);

    Route::resource('active-ingredient', ActiveIngredientController::class)->only(['index', 'create', 'edit'])->names([
        'index' => 'active-ingredient',
        'create' => 'create-active-ingredient',
        'edit' => 'edit-active-ingredient',
    ]);

    Route::resource('product', ProductController::class)->only(['index', 'create', 'edit'])->names([
        'index' => 'product',
        'create' => 'create-product',
        'edit' => 'edit-product',
    ]);

    Route::get('user-product/{product}/pdf-migrar', [ProductController::class, 'migrar'])->name('pdf-migrar');
    Route::get('user-product/{product}/pdf-product', [ProductController::class, 'pdf'])->name('pdf-product');
    Route::get('user-product/{product}/pdf-stock-product', [ProductController::class, 'pdfStock'])->name('pdf-stock-product');
    Route::get('user-product/{product}/pdf-price-product', [ProductController::class, 'pdfPrice'])->name('pdf-price-product');

    /**
     * SISTEMA
     */
    Route::resource('company', CompanyController::class)->only(['index', 'edit'])->names([
        'index' => 'company',
        'edit' => 'edit-company',
    ]);

    Route::resource('branch-office', BranchOfficeController::class)->only(['index', 'create', 'edit'])->names([
        'index' => 'branch-office',
        'create' => 'create-branch-office',
        'edit' => 'edit-branch-office',
    ]);

    Route::resource('card', CardController::class)->only(['index', 'create', 'edit'])->names([
        'index' => 'card',
        'create' => 'create-card',
        'edit' => 'edit-card',
    ]);

    Route::resource('transfer-method', TransferMethodController::class)->only(['index', 'create', 'edit'])->names([
        'index' => 'transfer-method',
        'create' => 'create-transfer-method',
        'edit' => 'edit-transfer-method',
    ]);

    Route::resource('checkout', CheckoutController::class)->only(['index', 'create', 'edit'])->names([
        'index' => 'checkout',
        'create' => 'create-checkout',
        'edit' => 'edit-checkout',
    ]);

    /**
     * COMPRAS
     */

    Route::resource('requirement', RequirementController::class)->only(['index', 'create', 'edit', 'show'])->names([
        'index' => 'requirement',
        'create' => 'create-requirement',
        'edit' => 'edit-requirement',
        'show' => 'show-requirement',
    ]);
    Route::get('user-requirement/{requirement}/pdf-requirement', [RequirementController::class, 'pdf'])->name('pdf-requirement');
    Route::resource('purchase', PurchaseController::class)->only(['index', 'create', 'edit', 'show'])->names([
        'index' => 'purchase',
        'create' => 'create-purchase',
        'show' => 'show-purchase',
    ]);
    Route::get('user-purchase/{purchase}/pdf-purchase', [PurchaseController::class, 'pdf'])->name('pdf-purchase');
    Route::get('user-purchase/excel-purchase', [PurchaseController::class, 'excel'])->name('excel-purchase');

    Route::resource('debts-to-pay', InstallmentController::class)->only(['index'])->names([
        'index' => 'debts-to-pay',
    ]);
    Route::get('debts-to-pay/pdf-debts-to-pay', [InstallmentController::class, 'pdf'])->name('pdf-debts-to-pay');
    Route::get('debts-to-pay/excel-debts-to-pay', [InstallmentController::class, 'excel'])->name('excel-debts-to-pay');

    Route::resource('entry-guide', EntryGuideController::class)->only(['index', 'create', 'show'])->names([
        'index' => 'entry-guide',
        'create' => 'create-entry-guide',
        'show' => 'show-entry-guide',
    ]);
    Route::get('user-entry-guide/{entryguide}/pdf-entryguide', [EntryGuideController::class, 'pdf'])->name('pdf-entryguide');
    Route::get('user-entry-guide/{entryguide}/pdf2-entryguide', [EntryGuideController::class, 'pdf2'])->name('pdf2-entryguide');
    Route::get('user-entry-guide/{entryguide}/pdf3-entryguide', [EntryGuideController::class, 'pdf3'])->name('pdf3-entryguide');

    /**
     * ALMACEN
     */
    Route::resource('store-document', StoreDocumentController::class)->only(['index', 'create', 'show'])->names([
        'index' => 'store-document',
        'create' => 'create-store-document',
        'show' => 'show-store-document',
    ]);
    Route::post('import-store-document', [StoreDocumentController::class, 'saveimport'])->name('import-store-document');
    Route::get('user-store-document/{store_document}/pdf-store-document', [StoreDocumentController::class, 'pdf'])->name('pdf-store-document');

    Route::resource('referral-guide', ReferralGuideController::class)->only(['index', 'create', 'show'])->names([
        'index' => 'referral-guide',
        'create' => 'create-referral-guide',
        'show' => 'show-referral-guide',
    ]);

    Route::resource('shipping-format', ShippingFormatController::class)->only(['index','edit'])->names([
        'index' => 'shipping-format',
        'edit' => 'edit-shipping-format',
    ]);
    Route::get('user-shipping-format/{shippingformat}/pdf-shipping-format', [ShippingFormatController::class, 'pdf'])->name('pdf-shippingformat');

    Route::resource('lot', LotController::class)->only(['index','edit'])->names([
        'index' => 'lot',
        'edit'  => 'edit-lot'
    ]);
    Route::get('user-pdf-kardex-lot/{lot}/pdf', [LotController::class, 'pdf'])->name('pdf-kardex-lot');

    Route::resource('serie', ProductSerieController::class)->only(['index'])->names([
        'index' => 'serie',
    ]);
    Route::get('user-pdf-product-serie/pdf-product-serie', [ProductSerieController::class, 'pdf'])->name('pdf-product-serie');    

    Route::resource('stock', StockController::class)->only(['index'])->names([
        'index' => 'stock',
    ]);
    Route::get('user-pdf-stock/pdf-stock', [StockController::class, 'pdf'])->name('pdf-stock');  
    Route::get('stock/excel', [StockController::class, 'excel'])->name('excel-stock');  

    Route::resource('kardex', KardexController::class)->only(['index'])->names([
        'index' => 'kardex',
    ]);

    Route::get('user-kardex/pdf-kardex', [KardexController::class, 'pdf'])->name('pdf-kardex');
    Route::get('user-kardex/pdf2-kardex', [KardexController::class, 'pdf2'])->name('pdf2-kardex');


    /**
     * VENTAS
     */

    Route::resource('proforma', ProformaController::class)->only(['index', 'create', 'edit', 'show'])->names([
        'index' => 'proforma',
        'create' => 'create-proforma',
        'edit' => 'edit-proforma',
        'show' => 'show-proforma',
    ]);
    Route::get('user-proforma/{proforma}/pdf-proforma', [ProformaController::class, 'pdf'])->name('pdf-proforma');

    Route::resource('sale-checkout', SaleCheckoutController::class)->only(['index'])->names([
        'index' => 'sale-checkout',
    ]);
    Route::get('sale/excel-sale-checkout', [SaleCheckoutController::class, 'excel'])->name('excel-sale-checkout');
    Route::get('sale-checkout/{checkout}/opening', [SaleCheckoutController::class, 'opening'])->name('opening');
    Route::get('sale-checkout/{checkout}/closing', [SaleCheckoutController::class, 'closing'])->name('closing');
    Route::get('sale-checkout/{checkout}/payment', [SaleCheckoutController::class, 'payment'])->name('payment');

    Route::resource('report-sale-checkout', ReportSaleCheckoutController::class)->only(['index'])->names([
        'index' => 'report-sale-checkout',
    ]);
    Route::get('reporte-sale-checkout/excel-sale-checkout', [ReportSaleCheckoutController::class, 'excel'])->name('report-excel-sale-checkout');

    Route::get('sale/excel-sale', [SaleController::class, 'excel'])->name('excel-sale');
    Route::post('sale/anular-pago', [SaleController::class, 'anularPago'])->name('sale-anular-pago');

    Route::resource('sale', SaleController::class)->only(['index', 'create', 'edit', 'show'])->names([
        'index' => 'sale',
        'create' => 'create-sale',
        'show' => 'show-sale',
    ]);

    Route::resource('creditnote', CreditNoteController::class)->only(['index', 'create', 'show'])->names([
        'index' => 'creditnote',
        'create' => 'create-creditnote',
        'show' => 'show-creditnote',
    ]);

    Route::resource('sale-guide', SaleGuideController::class)->only(['index', 'create', 'show'])->names([
        'index' => 'sale-guide',
        'create' => 'create-sale-guide',
        'show' => 'show-sale-guide',
    ]);

    Route::resource('debts-to-cash', CuentasxcobrarController::class)->only(['index'])->names([
        'index' => 'debts-to-cash',
    ]);
    Route::get('debts-to-cash/pdf-debts-to-cash', [CuentasxcobrarController::class, 'pdf'])->name('pdf-debts-to-cash');
    Route::get('debts-to-cash/excel-debts-to-cash', [CuentasxcobrarController::class, 'excel'])->name('excel-debts-to-cash');
    Route::get('debts-to-cash/pdf-debts-to-cash2', [CuentasxcobrarController::class, 'pdf2'])->name('pdf-debts-to-cash2');

    Route::resource('comisiones', ComisionesController::class)->only(['index'])->names([
        'index' => 'comisiones',
    ]);
    Route::get('user-comisiones/pdf-comisiones', [ComisionesController::class, 'pdf'])->name('pdf-comisiones');

    Route::resource('comisionessupervisor', ComisionessupervisorController::class)->only(['index'])->names([
        'index' => 'comisionessupervisor',
    ]);
    Route::get('user-comisionessupervisor/pdf-comisionessupervisor', [ComisionessupervisorController::class, 'pdf'])->name('pdf-comisionessupervisor');

    Route::resource('order', OrderController::class)->only(['index', 'create', 'edit', 'show'])->names([
        'index' => 'order',
        'create' => 'create-order',
        'edit' => 'edit-order',
        'show' => 'show-order',
    ]);

    Route::get('user-order/{order}/pdf-order', [OrderController::class, 'pdf'])->name('pdf-order');

    Route::resource('preventative', PreventativeController::class)->only(['index', 'create', 'edit', 'show'])->names([
        'index' => 'preventative',
        'edit' => 'edit-preventative',
        'show' => 'show-preventative',
    ]);

    Route::resource('maintenance', MaintenanceController::class)->only(['index', 'create', 'edit', 'show'])->names([
        'index' => 'maintenance',
        'edit' => 'edit-maintenance',
        'create' => 'create-maintenance',
        'show' => 'show-maintenance',
    ]);

    Route::resource('installation', InstallationController::class)->only(['index', 'create', 'edit', 'show'])->names([
        'index' => 'installation',
        'edit' => 'edit-installation',
        'create' => 'create-installation',
        'show' => 'show-installation',
    ]);
    Route::get('user-installation/{installation}/pdf-installation', [InstallationController::class, 'pdf'])->name('pdf-installation');
    Route::get('user-installation/{installation}/pdf-list-installation', [InstallationController::class, 'pdfList'])->name('pdf-list-installation');
    Route::get('user-installation/{installation}/pdf-follow', [InstallationController::class, 'pdfFollow'])->name('pdf-follow');

    Route::resource('proximo-mantenimiento', ProximomantenimientoController::class)->only(['index'])->names([
        'index' => 'proximo-mantenimiento',
    ]);

    Route::get('user-proximomantenimiento/pdf-proximomantenimiento', [ProximomantenimientoController::class, 'pdf'])->name('pdf-proximomantenimiento');

    /*REPORTES*/
    Route::resource('report-lot', ReportLotController::class)->only(['index'])->names([
        'index' => 'report-lot',
    ]);
    Route::get('user-pdf-report-kardex-lot/{lot}/pdf', [ReportLotController::class, 'pdf'])->name('pdf-report-kardex-lot');

    Route::resource('report-serie', ReportProductSerieController::class)->only(['index'])->names([
        'index' => 'report-serie',
    ]);

    Route::resource('report-stock', ReportStockController::class)->only(['index'])->names([
        'index' => 'report-stock',
    ]);

    Route::get('user-pdf-report-stock/pdf', [ReportStockController::class, 'pdf'])->name('report-pdf-stock');

    Route::resource('report-purchase', ReportPurchaseController::class)->only(['index'])->names([
        'index' => 'report-purchase',
    ]);
    Route::get('user-pdf-report-purchase/pdf', [ReportPurchaseController::class, 'pdf'])->name('report-pdf-purchase');

    Route::resource('report-product-supplier', ReportProductSupplierController::class)->only(['index'])->names([
        'index' => 'report-product-supplier',
    ]);

    Route::get('user-pdf-report-product-supplier/pdf', [ReportProductSupplierController::class, 'pdf'])->name('report-pdf-product-supplier');
    Route::get('user-pdf-report-product-supplier-resumen/pdf', [ReportProductSupplierController::class, 'pdfResumen'])->name('report-pdf-product-supplier-resumen');

    Route::resource('report-supplier', ReportSupplierController::class)->only(['index'])->names([
        'index' => 'report-supplier',
    ]);

    Route::get('user-pdf-report-supplier/pdf', [ReportSupplierController::class, 'pdf'])->name('report-pdf-supplier');

    Route::resource('report-requirement', ReportRequirementController::class)->only(['index'])->names([
        'index' => 'report-requirement',
    ]);
    Route::get('user-pdf-report-requirement/pdf', [ReportRequirementController::class, 'pdf'])->name('report-pdf-requirement');

    Route::resource('report-exchange', ReportExchangeController::class)->only(['index'])->names([
        'index' => 'report-exchange-rate',
    ]);
    Route::get('user-pdf-report-exchange/pdf', [ReportExchangeController::class, 'pdf'])->name('report-pdf-exchange');

    //RUTAS QUE NO SE UTILIZARAN
    Route::get('user/profile', [IndexController::class, 'index'])->name('profile.show');
    Route::get('comprobante', [AnularfacturaController::class, 'index2'])->name('comprobante');
    Route::get('annul-facturas', [AnularfacturaController::class, 'index'])->name('annul-facturas');
    Route::get('annul-notas', [AnularfacturaController::class, 'indexNota'])->name('annul-notas');
    //Route::get('report-supplier', [IndexController::class, 'index'])->name('report-supplier');
    //Route::get('report-product-supplier', [IndexController::class, 'index'])->name('report-product-supplier');
    //Route::get('report-requirement', [IndexController::class, 'index'])->name('report-requirement');
    //Route::get('report-exhange-rate', [IndexController::class, 'index'])->name('report-exchange-rate');
    //Route::get('report-purchase', [IndexController::class, 'index'])->name('report-purchase');
    // Route::get('maintenance', [IndexController::class, 'index'])->name('maintenance');



});
