<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::index');
$routes->get('/login', 'AuthController::index');
$routes->post('/logout', 'AuthController::logout');
$routes->post('authverify', 'AuthController::login');
// $routes->get('generate', 'CelupController::generate');


// gbn routes
$routes->post('schedule/validateSisaJatah', 'ScheduleController::validateSisaJatah');
// $routes->get('schedule/getQtyPO', 'ScheduleController::getQtyPO');

$routes->group('/gbn', ['filter' => 'gbn'], function ($routes) {
    $routes->get('', 'DashboardGbnController::index');
    // $routes->get('getGroupData', 'DashboardGbnController::getGroupData');
    $routes->post('getGroupData', 'DashboardGbnController::getGroupData');
    $routes->get('masterdata', 'MasterdataController::index');
    $routes->post('tampilMasterOrder', 'MasterdataController::tampilMasterOrder');
    $routes->get('getOrderDetails/(:num)', 'MasterdataController::getOrderDetails/$1');
    $routes->post('updateOrder', 'MasterdataController::updateOrder');
    $routes->post('deleteOrder', 'MasterdataController::deleteOrder');

    $routes->get('material/(:num)', 'MasterdataController::material/$1');
    $routes->post('tampilMaterial', 'MasterdataController::tampilMaterial');
    $routes->get('getMaterialDetails/(:num)', 'MasterdataController::getMaterialDetails/$1');
    $routes->post('tambahMaterial', 'MaterialController::tambahMaterial');
    $routes->post('updateMaterial', 'MasterdataController::updateMaterial');
    $routes->get('deleteMaterial/(:num)/(:num)', 'MasterdataController::deleteMaterial/$1/$2');
    $routes->get('openPO/(:num)', 'MasterdataController::openPO/$1');
    $routes->post('openPO/saveOpenPO/(:num)', 'MasterdataController::saveOpenPO/$1');
    $routes->post('updateArea/(:num)', 'MaterialController::updateArea/$1');
    // $routes->post('exportOpenPO/(:any)/(:any)', 'MasterdataController::exportOpenPO/$1/$2');
    $routes->get('listOpenPO/(:any)', 'MaterialController::listOpenPO/$1');
    $routes->post('updatePo', 'MaterialController::updatePo');
    $routes->get('exportOpenPO/(:any)', 'PdfController::generateOpenPO/$1');
    $routes->get('getPoDetails/(:num)', 'MaterialController::getPoDetails/$1');
    $routes->delete('deletePo/(:num)', 'MaterialController::deletePo/$1');
    $routes->post('splitMaterial', 'MaterialController::splitMaterial');

    $routes->post('import/mu', 'MasterdataController::importMU');
    $routes->post('revise/mu', 'MasterdataController::reviseMU');


    $routes->get('masterMaterial', 'MastermaterialController::index');
    $routes->post('tampilMasterMaterial', 'MastermaterialController::tampilMasterMaterial');
    $routes->get('getMasterMaterialDetails', 'MastermaterialController::getMasterMaterialDetails');
    $routes->post('updateMasterMaterial', 'MastermaterialController::updateMasterMaterial');
    $routes->post('saveMasterMaterial', 'MastermaterialController::saveMasterMaterial');
    $routes->get('deleteMasterMaterial', 'MastermaterialController::deleteMasterMaterial');

    $routes->get('schedule', 'ScheduleController::index');
    $routes->get('schedule/acrylic', 'ScheduleController::acrylic');
    $routes->get('schedule/nylon', 'ScheduleController::nylon');
    $routes->get('schedule/sample', 'ScheduleController::sample');
    $routes->get('schedule/getScheduleDetails/(:any)/(:any)/(:any)', 'ScheduleController::getScheduleDetails/$1/$2/$3');
    $routes->get('schedule/form', 'ScheduleController::create');
    $routes->get('schedule/getItemType', 'ScheduleController::getItemType');
    $routes->get('schedule/getKodeWarna', 'ScheduleController::getKodeWarna');
    $routes->get('schedule/getWarna', 'ScheduleController::getWarna');
    // $routes->get('schedule/getQtyPO', 'ScheduleController::getQtyPO');

    // $routes->get('schedule/getWarna', 'ScheduleController::getWarnabyItemTypeandKodeWarna');
    $routes->get('schedule/getPO', 'ScheduleController::getPO');
    $routes->get('schedule/getPODetails', 'ScheduleController::getPODetails');
    $routes->get('schedule/getQtyPO', 'ScheduleController::getQtyPO');
    $routes->get('schedule/getNoModel', 'ScheduleController::getNoModel');
    $routes->post('schedule/saveSchedule', 'ScheduleController::saveSchedule');
    $routes->get('schedule/editSchedule', 'ScheduleController::editSchedule');
    $routes->post('schedule/updateSchedule', 'ScheduleController::updateSchedule');
    $routes->post('schedule/updateTglSchedule', 'ScheduleController::updateTglSchedule');
    $routes->post('schedule/deleteSchedule', 'ScheduleController::deleteSchedule');

    $routes->get('schedule/reqschedule', 'ScheduleController::reqschedule');
    $routes->post('schedule/reqschedule', 'ScheduleController::reqschedule');
    $routes->get('schedule/reqschedule/show/(:num)', 'ScheduleController::showschedule/$1');
    $routes->get('schedule/reportSchBenang', 'ScheduleController::reportSchBenang');
    $routes->get('schedule/filterSchBenang', 'ScheduleController::filterSchBenang');
    $routes->get('schedule/exportScheduleBenang', 'ExcelController::exportScheduleBenang');

    // $routes->post('schedule/validateSisaJatah', 'ScheduleController::validateSisaJatah');

    $routes->get('mesin/mesinCelup', 'MesinCelupController::mesinCelup');
    $routes->post('mesin/saveDataMesin', 'MesinCelupController::saveDataMesin');
    $routes->get('mesin/getMesinDetails/(:num)', 'MesinCelupController::getMesinDetails/$1');
    $routes->post('mesin/cekNoMesin', 'MesinCelupController::cekNoMesin');
    $routes->post('mesin/updateDataMesin', 'MesinCelupController::updateDataMesin');
    $routes->get('mesin/deleteDataMesin/(:num)', 'MesinCelupController::deleteDataMesin/$1');

    $routes->get('warehouse', 'WarehouseController::index');
    $routes->get('pemasukan', 'WarehouseController::pemasukan');
    $routes->post('pemasukan', 'WarehouseController::pemasukan');
    $routes->get('pemasukan/getDataByIdStok/(:any)', 'PemesananController::getDataByIdStok/$1');
    $routes->post('reset_pemasukan', 'WarehouseController::reset_pemasukan');
    $routes->post('hapus_pemasukan', 'WarehouseController::hapusListPemasukan');
    $routes->post('proses_pemasukan', 'WarehouseController::prosesPemasukan');
    $routes->get('getItemTypeByModel/(:any)', 'WarehouseController::getItemTypeByModel/$1');
    $routes->get('getKodeWarnaByModelAndItemType', 'WarehouseController::getKodeWarna');
    $routes->get('getWarnaDanLot', 'WarehouseController::getWarnaDanLot');
    $routes->get('getKgsDanCones', 'WarehouseController::getKgsDanCones');
    $routes->post('getcluster', 'WarehouseController::getCluster');
    $routes->post('proses_pemasukan_manual', 'WarehouseController::prosesPemasukanManual');
    $routes->get('pengeluaran_jalur', 'WarehouseController::pengeluaranJalur');
    $routes->post('pengeluaran_jalur', 'WarehouseController::pengeluaranJalur');
    $routes->post('reset_pengeluaran', 'WarehouseController::resetPengeluaranJalur');
    $routes->post('hapus_pengeluaran', 'WarehouseController::hapusListPengeluaran');
    $routes->post('proses_pengeluaran_jalur', 'WarehouseController::prosesPengeluaranJalur');
    $routes->get('getItemTypeForOut/(:any)', 'WarehouseController::getItemTypeForOut/$1');
    $routes->get('getKodeWarnaForOut', 'WarehouseController::getKodeWarnaForOut');
    $routes->get('getWarnaDanLotForOut', 'WarehouseController::getWarnaDanLotForOut');
    $routes->get('getKgsCnsClusterForOut', 'WarehouseController::getKgsCnsClusterForOut');
    $routes->post('proses_pengeluaran_manual', 'WarehouseController::prosesPengeluaranJalurManual');
    $routes->post('savePengeluaranJalur', 'WarehouseController::savePengeluaranJalur');
    $routes->post('simpanPengeluaranJalur', 'WarehouseController::simpanPengeluaranJalur');

    $routes->post('komplain_pemasukan', 'WarehouseController::prosesComplain');
    //
    $routes->post('warehouse/search', 'WarehouseController::search');
    $routes->post('warehouse/sisaKapasitas', 'WarehouseController::getSisaKapasitas');
    $routes->post('warehouse/getCluster', 'WarehouseController::getClusterbyId');
    $routes->post('warehouse/updateCluster', 'WarehouseController::updateCluster');
    $routes->post('warehouse/getNoModel', 'WarehouseController::getNoModel');
    $routes->post('warehouse/updateNoModel', 'WarehouseController::updateNoModel');
    $routes->get('warehouse/reportPoBenang', 'WarehouseController::reportPoBenang');
    $routes->get('warehouse/filterPoBenang', 'WarehouseController::filterPoBenang');
    $routes->get('warehouse/exportPoBenang', 'ExcelController::exportPoBenang');
    $routes->get('warehouse/reportDatangBenang', 'WarehouseController::reportDatangBenang');
    $routes->get('warehouse/filterDatangBenang', 'WarehouseController::filterDatangBenang');
    $routes->get('warehouse/exportDatangBenang', 'ExcelController::exportDatangBenang');
    //
    $routes->post('getStockByParams', 'PemesananController::getStockByParams');
    $routes->get('pemesanan', 'PemesananController::index');
    $routes->get('pemesanan/(:any)/(:any)', 'PemesananController::pemesanan/$1/$2');
    $routes->post('pemesanan/filter', 'PemesananController::filterPemesanan');
    $routes->get('pemesananperarea/(:any)', 'PemesananController::pemesananPerArea/$1');
    $routes->get('detailpemesanan/(:any)/(:any)', 'PemesananController::detailPemesanan/$1/$2');
    $routes->get('selectClusterWarehouse/(:any)', 'PemesananController::selectClusterWarehouse/$1');
    $routes->post('saveUsage', 'PemesananController::saveUsage');
    $routes->get('pengiriman_area', 'PemesananController::pengirimanArea');
    $routes->post('pengiriman_area', 'PemesananController::pengirimanArea');
    $routes->post('reset_pengiriman/(:any)/(:any)', 'PemesananController::resetPengirimanArea/$1/$2');
    $routes->post('hapus_pengiriman', 'PemesananController::hapusListPengiriman');
    $routes->post('proses_pengiriman', 'PemesananController::prosesPengirimanArea');

    $routes->get('pph', 'PphController::tampilPerModel');
    $routes->get('tampilPerStyle', 'PphController::tampilPerStyle');
    $routes->get('tampilPerDays', 'PphController::tampilPerDays');
    $routes->get('pphPerhari', 'PphController::pphPerhari');
    $routes->post('tampilPerDays', 'PphController::tampilPerDays');
    $routes->get('tampilPerModel/(:any)', 'PphController::tampilPerModel/$1');
    $routes->get('getDataModel', 'PphController::getDataModel');
    $routes->get('pphinisial', 'PphController::pphinisial');
    $routes->get('getDataPerhari', 'PphController::getDataPerhari');
    // $routes->post('tampilPerModel/(:any)', 'PphController::tampilPerModel/$1');
    $routes->get('excelPPHNomodel/(:any)/(:any)', 'ExcelController::excelPPHNomodel/$1/$2');
    $routes->get('excelPPHInisial/(:any)/(:any)', 'ExcelController::excelPPHInisial/$1/$2');
    $routes->get('excelPPHDays/(:any)/(:any)', 'ExcelController::excelPPHDays/$1/$2');
    //PO Covering
    $routes->get('poCovering', 'POCoveringController::index');
    $routes->get('po/exportPO/(:any)', 'PdfController::generateOpenPOCovering/$1');
});

// celup routes
$routes->group('/celup', ['filter' => 'celup'], function ($routes) {
    $routes->get('', 'DashboardCelupController::index');
    $routes->get('getStackedChartData', 'DashboardCelupController::getStackedChartData');
    $routes->get('schedule', 'ScheduleController::index');
    $routes->get('schedule/acrylic', 'ScheduleController::acrylic');
    $routes->get('schedule/nylon', 'ScheduleController::nylon');
    $routes->get('reqschedule', 'ScheduleController::reqschedule');
    $routes->post('schedule', 'CelupController::schedule');
    $routes->get('edit/(:num)', 'CelupController::editStatus/$1');
    $routes->post('updateSchedule/(:num)', 'CelupController::updateSchedule/$1');
    $routes->get('schedule/getScheduleDetails/(:any)/(:any)/(:any)', 'ScheduleController::getScheduleDetails/$1/$2/$3');
    $routes->get('schedule/editSchedule', 'ScheduleController::editSchedule');
    $routes->get('mesin/mesinCelup', 'MesinCelupController::mesinCelup');

    $routes->get('outCelup', 'CelupController::outCelup');
    $routes->get('outCelup/getDetail/(:num)', 'CelupController::getDetail/$1');
    $routes->get('outCelup/editBon/(:num)', 'CelupController::editBon/$1');
    $routes->post('outCelup/updateBon/(:num)', 'CelupController::updateBon/$1');
    $routes->delete('outCelup/deleteBon/(:num)', 'CelupController::deleteBon/$1');
    // $routes->get('insertBon/(:num)', 'CelupController::insertBon/$1');
    $routes->get('createBon', 'CelupController::createBon');
    $routes->post('createBon/getItem/(:num)', 'CelupController::getItem/$1');

    $routes->post('outCelup/saveBon/', 'CelupController::saveBon');
    $routes->get('retur', 'CelupController::retur');
    $routes->post('retur', 'CelupController::retur');
    $routes->get('editretur/(:num)', 'CelupController::editRetur/$1');
    $routes->post('proseseditretur/(:num)', 'CelupController::prosesEditRetur/$1');
    $routes->get('printBon/(:num)', 'PdfController::printBon/$1');
});



// covering routes
$routes->group('/covering', ['filter' => 'covering'], function ($routes) {
    $routes->get('', 'CoveringController::index');
    $routes->get('memo', 'CoveringController::memo');
    $routes->get('mesinCov', 'MesinCoveringController::mesinCovering');
    $routes->post('mesinCov/saveDataMesin', 'MesinCoveringController::saveDataMesin');
    $routes->get('mesinCov/getMesinCovDetails/(:any)', 'MesinCoveringController::getMesinCovDetails/$1');
    $routes->post('mesinCov/updateDataMesin', 'MesinCoveringController::updateDataMesin');
    $routes->get('deleteDataMesinCov/(:num)', 'MesinCoveringController::deleteDataMesin/$1');

    $routes->get('po', 'CoveringController::po');
    $routes->get('schedule', 'CoveringController::schedule');
    $routes->get('schedule/getScheduleDetails/(:any)/(:any)/(:any)', 'ScheduleController::getScheduleDetails/$1/$2/$3');
    $routes->get('schedule/form', 'ScheduleController::create');
    $routes->get('schedule/getItemType', 'ScheduleController::getItemType');
    $routes->get('schedule/getKodeWarna', 'ScheduleController::getKodeWarna');
    $routes->get('schedule/getWarna', 'ScheduleController::getWarna');
    // $routes->get('schedule/getQtyPO', 'ScheduleController::getQtyPO');

    // $routes->get('schedule/getWarna', 'ScheduleController::getWarnabyItemTypeandKodeWarna');
    $routes->get('schedule/getPO', 'ScheduleController::getPO');
    $routes->get('schedule/getPODetails', 'ScheduleController::getPODetails');
    $routes->get('schedule/getQtyPO', 'ScheduleController::getQtyPO');
    $routes->get('schedule/getNoModel', 'ScheduleController::getNoModel');
    $routes->post('schedule/saveSchedule', 'ScheduleController::saveSchedule');
    $routes->get('schedule/editSchedule', 'ScheduleController::editSchedule');
    $routes->post('schedule/updateSchedule', 'ScheduleController::updateSchedule');
    $routes->post('schedule/updateTglSchedule', 'ScheduleController::updateTglSchedule');
    $routes->post('schedule/deleteSchedule', 'ScheduleController::deleteSchedule');

    $routes->get('mesin/mesinCelup', 'MesinCelupController::mesinCelup');
    $routes->post('mesin/saveDataMesin', 'MesinCelupController::saveDataMesin');
    $routes->get('mesin/getMesinDetails/(:num)', 'MesinCelupController::getMesinDetails/$1');
    $routes->post('mesin/cekNoMesin', 'MesinCelupController::cekNoMesin');
    $routes->post('mesin/updateDataMesin', 'MesinCelupController::updateDataMesin');
    $routes->get('mesin/deleteDataMesin/(:num)', 'MesinCelupController::deleteDataMesin/$1');

    $routes->get('poDetail/(:any)', 'CoveringController::poDetail/$1');
    $routes->get('getDetailByNoModel/(:any)/(:any)', 'CoveringController::getDetailByNoModel/$1/$2');
    $routes->post('po/simpanKeSession', 'CoveringController::simpanKeSession');
    $routes->post('po/savePOCovering', 'CoveringController::savePOCovering');
    $routes->get('po/deletePOCovering/(:any)', 'CoveringController::unsetSession/$1');
    $routes->get('po/exportPO/(:any)', 'PdfController::generateOpenPOCovering/$1');

    // warehouse
    $routes->get('warehouse', 'CoveringWarehouseController::index');
    $routes->post('warehouse/tambahStock', 'CoveringWarehouseController::create');
    $routes->post('warehouse/updateStock', 'CoveringWarehouseController::updateStock');
    $routes->post('warehouse/updateEditStock', 'CoveringWarehouseController::updateEditStock');
    $routes->get('warehouse/getStock/(:any)', 'CoveringWarehouseController::getStock/$1');
    $routes->get('warehouse/reportPemasukan', 'CoveringWarehouseController::reportPemasukan');
    $routes->get('warehouse/reportPengeluaran', 'CoveringWarehouseController::reportPengeluaran');
    $routes->get('warehouse/pengeluaran_jalur', 'CoveringController::pengeluaranJalur');
    $routes->get('warehouse/pengiriman_area', 'CoveringController::pengirimanArea');

    //Pemesanan
    $routes->get('pemesanan', 'CoveringPemesananController::index');
    $routes->get('pemesanan/(:any)', 'CoveringPemesananController::pemesanan/$1');
    $routes->get('detailPemesanan/(:any)', 'CoveringPemesananController::detailPemesanan/$1');
});


// monitoring routes
$routes->group('/monitoring', ['filter' => 'monitoring'], function ($routes) {
    $routes->get('', 'MonitoringController::index');
    // User
    $routes->get('user', 'MonitoringController::user');
    $routes->post('tambahUser', 'MonitoringController::tambahUser');
    $routes->get('getUserDetails/(:num)', 'MonitoringController::getUserDetails/$1');
    $routes->post('updateUser', 'MonitoringController::updateUser');
    $routes->get('deleteUser/(:num)', 'MonitoringController::deleteUser/$1');

    // Gudang Benang
    $routes->get('masterdata', 'MasterdataController::index');
    $routes->post('tampilMasterOrder', 'MasterdataController::tampilMasterOrder');
    $routes->get('getOrderDetails/(:num)', 'MasterdataController::getOrderDetails/$1');
    $routes->post('updateOrder', 'MasterdataController::updateOrder');
    $routes->post('deleteOrder', 'MasterdataController::deleteOrder');

    $routes->get('material/(:any)', 'MasterdataController::material/$1');
    $routes->post('tampilMaterial', 'MasterdataController::tampilMaterial');
    $routes->get('getMaterialDetails/(:num)', 'MasterdataController::getMaterialDetails/$1');
    $routes->post('tambahMaterial', 'MaterialController::tambahMaterial');
    $routes->post('updateMaterial', 'MasterdataController::updateMaterial');
    $routes->get('deleteMaterial/(:num)/(:num)', 'MasterdataController::deleteMaterial/$1/$2');
    $routes->get('openPO/(:num)', 'MasterdataController::openPO/$1');
    $routes->post('openPO/saveOpenPO/(:num)', 'MasterdataController::saveOpenPO/$1');
    $routes->post('updateArea/(:num)', 'MaterialController::updateArea/$1');
    $routes->get('exportOpenPO/(:any)', 'PdfController::generateOpenPO/$1');

    $routes->post('import/mu', 'MasterdataController::importMU');
    $routes->post('revise/mu', 'MasterdataController::reviseMU');

    $routes->get('masterMaterial', 'MastermaterialController::index');
    $routes->post('tampilMasterMaterial', 'MastermaterialController::tampilMasterMaterial');
    $routes->get('getMasterMaterialDetails', 'MastermaterialController::getMasterMaterialDetails');
    $routes->post('updateMasterMaterial', 'MastermaterialController::updateMasterMaterial');
    $routes->post('saveMasterMaterial', 'MastermaterialController::saveMasterMaterial');
    $routes->get('deleteMasterMaterial', 'MastermaterialController::deleteMasterMaterial');

    $routes->get('schedule', 'ScheduleController::index');
    $routes->get('schedule/acrylic', 'ScheduleController::acrylic');
    $routes->get('schedule/nylon', 'ScheduleController::nylon');
    $routes->get('schedule/sample', 'ScheduleController::sample');
    $routes->get('schedule/getScheduleDetails/(:any)/(:any)/(:any)', 'ScheduleController::getScheduleDetails/$1/$2/$3');
    $routes->get('schedule/form', 'ScheduleController::create');
    $routes->get('schedule/getItemType', 'ScheduleController::getItemType');
    $routes->get('schedule/getKodeWarna', 'ScheduleController::getKodeWarna');
    $routes->get('schedule/getWarna', 'ScheduleController::getWarna');
    $routes->get('schedule/getPO', 'ScheduleController::getPO');
    $routes->get('schedule/getPODetails', 'ScheduleController::getPODetails');
    $routes->get('schedule/getQtyPO', 'ScheduleController::getQtyPO');
    $routes->get('schedule/getNoModel', 'ScheduleController::getNoModel');
    $routes->post('schedule/saveSchedule', 'ScheduleController::saveSchedule');
    $routes->get('schedule/editSchedule', 'ScheduleController::editSchedule');
    $routes->post('schedule/updateSchedule', 'ScheduleController::updateSchedule');
    $routes->post('schedule/updateTglSchedule', 'ScheduleController::updateTglSchedule');
    $routes->post('schedule/deleteSchedule', 'ScheduleController::deleteSchedule');

    $routes->get('schedule/reqschedule', 'ScheduleController::reqschedule');
    $routes->post('schedule/reqschedule', 'ScheduleController::reqschedule');
    $routes->get('schedule/reqschedule/show/(:num)', 'ScheduleController::showschedule/$1');

    $routes->get('mesin/mesinCelup', 'MesinCelupController::mesinCelup');
    $routes->post('mesin/saveDataMesin', 'MesinCelupController::saveDataMesin');
    $routes->get('mesin/getMesinDetails/(:num)', 'MesinCelupController::getMesinDetails/$1');
    $routes->post('mesin/cekNoMesin', 'MesinCelupController::cekNoMesin');
    $routes->post('mesin/updateDataMesin', 'MesinCelupController::updateDataMesin');
    $routes->get('mesin/deleteDataMesin/(:num)', 'MesinCelupController::deleteDataMesin/$1');

    $routes->get('warehouse', 'WarehouseController::index');
    $routes->get('pemasukan', 'WarehouseController::pemasukan');
    $routes->post('pemasukan', 'WarehouseController::pemasukan');
    $routes->post('reset_pemasukan', 'WarehouseController::reset_pemasukan');
    $routes->post('hapus_pemasukan', 'WarehouseController::hapusListPemasukan');
    $routes->post('proses_pemasukan', 'WarehouseController::prosesPemasukan');
    $routes->get('getItemTypeByModel/(:any)', 'WarehouseController::getItemTypeByModel/$1');
    $routes->get('getKodeWarnaByModelAndItemType', 'WarehouseController::getKodeWarna');
    $routes->get('getWarnaDanLot', 'WarehouseController::getWarnaDanLot');
    $routes->get('getKgsDanCones', 'WarehouseController::getKgsDanCones');
    $routes->post('getcluster', 'WarehouseController::getCluster');
    $routes->post('proses_pemasukan_manual', 'WarehouseController::prosesPemasukanManual');
    $routes->get('pengeluaran_jalur', 'WarehouseController::pengeluaranJalur');
    $routes->post('pengeluaran_jalur', 'WarehouseController::pengeluaranJalur');
    $routes->post('reset_pengeluaran', 'WarehouseController::resetPengeluaranJalur');
    $routes->post('hapus_pengeluaran', 'WarehouseController::hapusListPengeluaran');
    $routes->get('pengiriman_area', 'WarehouseController::pengirimanArea');
    $routes->get('pengeluaran', 'WarehouseController::pengeluaran');
    $routes->post('warehouse/search', 'WarehouseController::search');
    $routes->post('warehouse/sisaKapasitas', 'WarehouseController::getSisaKapasitas');
    $routes->post('warehouse/getCluster', 'WarehouseController::getClusterbyId');
    $routes->post('warehouse/updateCluster', 'WarehouseController::updateCluster');
    $routes->post('warehouse/getNoModel', 'WarehouseController::getNoModel');
    $routes->post('warehouse/updateNoModel', 'WarehouseController::updateNoModel');

    // $routes->get('pph', 'PphController::index');
    $routes->get('pph', 'PphController::tampilPerModel');
    $routes->get('tampilPerStyle', 'PphController::tampilPerStyle');
    $routes->get('tampilPerDays', 'PphController::tampilPerDays');
    $routes->get('pphPerhari', 'PphController::pphPerhari');
    $routes->post('tampilPerDays', 'PphController::tampilPerDays');
    $routes->get('tampilPerModel/(:any)', 'PphController::tampilPerModel/$1');
    $routes->get('getDataModel', 'PphController::getDataModel');
    $routes->get('pphinisial', 'PphController::pphinisial');
    $routes->get('getDataPerhari', 'PphController::getDataPerhari');
    // $routes->post('tampilPerModel/(:any)', 'PphController::tampilPerModel/$1');
    $routes->get('excelPPHNomodel/(:any)/(:any)', 'ExcelController::excelPPHNomodel/$1/$2');
    $routes->get('excelPPHInisial/(:any)/(:any)', 'ExcelController::excelPPHInisial/$1/$2');
    $routes->get('excelPPHDays/(:any)/(:any)', 'ExcelController::excelPPHDays/$1/$2');
    //Celup
    $routes->get('schedule', 'ScheduleController::index');
    $routes->get('schedule/acrylic', 'ScheduleController::acrylic');
    $routes->get('schedule/nylon', 'ScheduleController::nylon');
    $routes->get('reqschedule', 'CelupController::schedule');
    $routes->post('schedule', 'CelupController::schedule');
    $routes->get('edit/(:num)', 'CelupController::editStatus/$1');
    $routes->post('updateSchedule/(:num)', 'CelupController::updateSchedule/$1');
    $routes->get('schedule/getScheduleDetails/(:any)/(:any)/(:any)', 'ScheduleController::getScheduleDetails/$1/$2/$3');
    $routes->get('schedule/editSchedule', 'ScheduleController::editSchedule');
    $routes->get('mesin/mesinCelup', 'MesinCelupController::mesinCelup');

    $routes->get('outCelup', 'CelupController::outCelup');
    $routes->get('outCelup/getDetail/(:num)', 'CelupController::getDetail/$1');
    $routes->get('outCelup/editBon/(:num)', 'CelupController::editBon/$1');
    $routes->post('outCelup/updateBon/(:num)', 'CelupController::updateBon/$1');
    $routes->delete('outCelup/deleteBon/(:num)', 'CelupController::deleteBon/$1');
    $routes->get('createBon', 'CelupController::createBon');
    $routes->post('createBon/getItem/(:num)', 'CelupController::getItem/$1');

    $routes->post('outCelup/saveBon/', 'CelupController::saveBon');
    $routes->get('retur', 'CelupController::retur');
    $routes->get('generate/(:num)', 'CelupController::generateBarcode/$1');
    $routes->get('printBon/(:num)', 'PdfController::printBon/$1');
});

$routes->options('(:any)', function () {
    return $this->response
        ->setHeader('Access-Control-Allow-Origin', '*')
        ->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE')
        ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
        ->setStatusCode(200);
});

// api routes
$routes->group(
    'api',
    function ($routes) {
        $routes->get('statusbahanbaku/(:any)', 'ApiController::statusbahanbaku/$1');
        $routes->get('cekBahanBaku/(:any)', 'ApiController::cekBahanBaku/$1');
        $routes->get('cekStok/(:any)', 'ApiController::cekStok/$1');
        $routes->get('getMU/(:any)/(:any)/(:any)', 'ApiController::getMaterialForPemesanan/$1/$2/$3');
        $routes->get('getMaterialForPPH/(:any)', 'ApiController::getMaterialForPPH/$1');
        $routes->get('getMaterialForPPHByAreaAndNoModel/(:segment)/(:segment)', 'ApiController::getMaterialForPPHByAreaAndNoModel/$1/$2');
        $routes->post('insertQtyCns', 'ApiController::insertQtyCns');
        $routes->post('saveListPemesanan', 'ApiController::saveListPemesanan');
        $routes->get('listPemesanan/(:any)', 'ApiController::listPemesanan/$1');
        $routes->post('getUpdateListPemesanan', 'ApiController::getUpdateListPemesanan');
        $routes->post('updateListPemesanan', 'ApiController::updateListPemesanan');
        $routes->post('kirimPemesanan', 'ApiController::kirimPemesanan');
        // $routes->get('getMaterialForPPH/(:any)/(:any)', 'ApiController::getMaterialForPPH/$1/$2');
        $routes->get('stockbahanbaku/(:any)', 'ApiController::stockbahanbaku/$1');
        $routes->post('hapusOldPemesanan', 'ApiController::hapusOldPemesanan');
        $routes->get('pph/(:any)', 'ApiController::pph/$1');
        $routes->post('assignArea', 'MaterialController::assignArea');
    }
);
