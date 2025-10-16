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
    $routes->get('masterdata/reportMasterOrder', 'MasterdataController::reportMasterOrder');
    $routes->get('masterdata/filterMasterOrder', 'MasterdataController::filterMasterOrder');
    $routes->get('masterdata/excelMasterOrder', 'ExcelController::excelMasterOrder');
    $routes->get('masterdata/reportKebutuhanBahanBaku', 'MasterdataController::reportKebutuhanBahanBaku');
    $routes->get('masterdata/filterReportKebutuhanBahanBaku', 'MasterdataController::filterReportKebutuhanBahanBaku');
    $routes->get('masterdata/excelReportKebutuhanBahanBaku', 'ExcelController::excelReportKebutuhanBahanBaku');
    $routes->get('masterdata/poGabungan', 'PoGabunganController::index');
    $routes->get('masterdata/poGabungan/(:any)', 'PoGabunganController::poGabungan/$1');
    $routes->get('masterdata/poGabunganDetail/(:any)', 'PoGabunganController::poGabunganDetail/$1');
    $routes->get('masterdata/poBooking', 'PoBookingController::index');
    $routes->get('masterdata/poBooking/create', 'PoBookingController::create');
    $routes->get('masterdata/poBooking/getItemType', 'PoBookingController::getItemType');
    $routes->get('masterdata/poBooking/getKodeWarna', 'PoBookingController::getKodeWarna');
    $routes->get('masterdata/poBooking/getColor', 'PoBookingController::getColor');
    $routes->post('masterdata/poBooking/saveOpenPoBooking', 'PoBookingController::saveOpenPoBooking');
    // $routes->get('masterdata/poBooking/exportPoBooking', 'PdfController::generateOpenPOBooking');
    $routes->get('masterdata/poBooking/exportPoBooking', 'ExcelController::generateOpenPOBookingExcel');
    $routes->get('masterdata/poBooking/detail', 'PoBookingController::detail');
    $routes->post('masterdata/poBooking/updatePoBooking', 'PoBookingController::updatePoBooking');
    $routes->post('masterdata/poBooking/deletePoBooking', 'PoBookingController::deletePoBooking');
    $routes->get('masterdata/poManual', 'PoManualController::index');
    $routes->get('masterdata/poManual/create', 'PoManualController::create');
    $routes->get('masterdata/poManual/getNoOrderByModel', 'PoManualController::getNoOrderByModel');
    $routes->post('masterdata/poManual/saveOpenPoManual', 'PoManualController::saveOpenPoManual');
    $routes->get('masterdata/poManual/detail', 'PoManualController::detail');
    $routes->post('masterdata/poManual/updatePoManual', 'PoManualController::updatePoManual');
    $routes->post('masterdata/poManual/deletePoManual', 'PoManualController::deletePoManual');
    // $routes->get('masterdata/poManual/exportPoManual', 'PdfController::generateOpenPOManual');
    $routes->get('masterdata/poManual/exportPoManual', 'ExcelController::generateOpenPOManualExcel');
    $routes->get('masterdata/cekStockOrder/(:any)/(:any)/(:any)', 'PoGabunganController::cekStockOrder/$1/$2/$3');
    $routes->post('openPO/saveOpenPOGabungan', 'PoGabunganController::saveOpenPOGabungan');
    $routes->get('listPoGabungan', 'PoGabunganController::listPoGabungan');
    $routes->get('getPoGabungan/(:any)', 'PoGabunganController::getPoGabungan/$1');
    $routes->post('updatePoGabungan', 'PoGabunganController::updatePoGabungan');
    $routes->post('deletePoGabungan/(:num)', 'PoGabunganController::deletePoGabungan/$1');
    // $routes->get('exportOpenPOGabung', 'PdfController::exportOpenPOGabung');
    $routes->get('exportOpenPOGabung', 'ExcelController::exportOpenPOGabungNew');
    $routes->get('exportPoBoking', 'ExcelController::exportPoBooking');


    $routes->post('getMasterData', 'MasterdataController::getMasterData');
    $routes->get('material/(:num)', 'MasterdataController::material/$1');
    $routes->get('material/exportTotalKebutuhan/(:num)', 'ExcelController::exportTotalKebutuhan/$1');
    $routes->post('tampilMaterial', 'MasterdataController::tampilMaterial');
    $routes->get('getMaterialDetails/(:num)', 'MasterdataController::getMaterialDetails/$1');
    $routes->post('tambahMaterial', 'MaterialController::tambahMaterial');
    $routes->post('updateMaterial', 'MasterdataController::updateMaterial');
    $routes->get('deleteMaterial/(:num)/(:num)', 'MasterdataController::deleteMaterial/$1/$2');
    $routes->post('material/deleteSelected', 'MaterialController::deleteSelected');
    $routes->get('openPO/(:num)', 'MasterdataController::openPO/$1');
    $routes->post('openPO/saveOpenPO/(:num)', 'MasterdataController::saveOpenPO/$1');
    $routes->post('updateArea/(:num)', 'MaterialController::updateArea/$1');
    // $routes->post('exportOpenPO/(:any)/(:any)', 'MasterdataController::exportOpenPO/$1/$2');
    $routes->get('listOpenPO/(:any)', 'MaterialController::listOpenPO/$1');
    $routes->post('updatePo', 'MaterialController::updatePo');
    // $routes->get('exportOpenPO/(:any)', 'PdfController::generateOpenPO/$1');
    $routes->get('exportOpenPO/(:any)', 'ExcelController::generateOpenPOExcel/$1');
    // $routes->post('exportPoNylon', 'ExcelController::generateOpenPONylon');
    $routes->post('exportPoNylon', 'ExcelController::generateOpenPONylonNew');
    $routes->get('getPoDetails/(:num)', 'MaterialController::getPoDetails/$1');
    $routes->delete('deletePo/(:num)', 'MaterialController::deletePo/$1');
    $routes->post('splitMaterial', 'MaterialController::splitMaterial');
    $routes->post('materialTypeEdit', 'MaterialController::materialTypeEdit');

    $routes->post('import/mu', 'MasterdataController::importMU');
    $routes->post('revise/mu', 'MasterdataController::reviseMU');

    $routes->get('masterMaterial', 'MastermaterialController::index');
    $routes->post('tampilMasterMaterial', 'MastermaterialController::tampilMasterMaterial');
    $routes->get('getMasterMaterialDetails', 'MastermaterialController::getMasterMaterialDetails');
    $routes->post('updateMasterMaterial', 'MastermaterialController::updateMasterMaterial');
    $routes->post('saveMasterMaterial', 'MastermaterialController::saveMasterMaterial');
    $routes->get('deleteMasterMaterial', 'MastermaterialController::deleteMasterMaterial');

    $routes->get('masterWarnaBenang', 'MasterWarnaBenangController::index');
    $routes->post('getMasterWarnaBenang', 'MasterWarnaBenangController::getMasterWarnaBenang');
    $routes->get('getMasterWarnaBenangDetails', 'MasterWarnaBenangController::getMasterWarnaBenangDetails');
    $routes->post('updateMasterWarnaBenang', 'MasterWarnaBenangController::updateMasterWarnaBenang');
    $routes->post('saveMasterWarnaBenang', 'MasterWarnaBenangController::saveMasterWarnaBenang');
    $routes->get('deleteMasterWarnaBenang', 'MasterWarnaBenangController::deleteMasterWarnaBenang');

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
    $routes->get('schedule/formsample', 'ScheduleController::createsample');

    // $routes->get('schedule/getWarna', 'ScheduleController::getWarnabyItemTypeandKodeWarna');
    $routes->get('schedule/getPO', 'ScheduleController::getPO');
    $routes->get('schedule/getPODetails', 'ScheduleController::getPODetails');
    $routes->get('schedule/getQtyPO', 'ScheduleController::getQtyPO');
    $routes->get('schedule/getNoModel', 'ScheduleController::getNoModel');
    $routes->post('schedule/saveSchedule', 'ScheduleController::saveSchedule');
    $routes->post('schedule/saveScheduleSample', 'ScheduleController::saveScheduleSample');
    $routes->get('schedule/editSchedule', 'ScheduleController::editSchedule');
    $routes->post('schedule/updateSchedule', 'ScheduleController::updateSchedule');
    $routes->post('schedule/getPindahMesin', 'ScheduleController::getPindahMesin');
    $routes->post('schedule/updateMesinSchedule', 'ScheduleController::updateMesinSchedule');
    $routes->post('schedule/updateTglSchedule', 'ScheduleController::updateTglSchedule');
    $routes->post('schedule/deleteSchedule', 'ScheduleController::deleteSchedule');
    $routes->get('schedule/getStock', 'ScheduleController::getStock');
    $routes->get('schedule/getKeterangan', 'ScheduleController::getKeterangan');
    $routes->post('updateSchedule/(:num)', 'CelupController::updateSchedule/$1');
    $routes->get('reqschedule', 'ScheduleController::reqschedule');
    $routes->get('schedule/reqschedule', 'ScheduleController::reqschedule');
    $routes->post('schedule/reqschedule', 'ScheduleController::reqschedule');
    $routes->get('schedule/reqschedule/show/(:num)', 'CelupController::editStatus/$1');
    $routes->get('schedule/reportSchBenang', 'ScheduleController::reportSchBenang');
    $routes->get('schedule/filterSchBenang', 'ScheduleController::filterSchBenang');
    $routes->get('schedule/exportScheduleBenang', 'ExcelController::exportScheduleBenang');
    $routes->get('schedule/reportSchNylon', 'ScheduleController::reportSchNylon');
    $routes->get('schedule/filterSchNylon', 'ScheduleController::filterSchNylon');
    $routes->get('schedule/exportScheduleNylon', 'ExcelController::exportScheduleNylon');
    $routes->get('schedule/reportSchWeekly', 'ScheduleController::reportSchWeekly');
    $routes->get('schedule/filterSchWeekly', 'ScheduleController::filterSchWeekly');
    $routes->get('schedule/exportScheduleWeekly', 'ExcelController::exportScheduleWeekly');
    $routes->get('schedule/reportDataTagihanBenang', 'ScheduleController::reportDataTagihanBenang');
    $routes->get('schedule/filterTagihanBenang', 'ScheduleController::filterTagihanBenang');
    $routes->get('schedule/exportTagihanBenang', 'ExcelController::exportTagihanBenang');
    $routes->get('schedule/exportReqSchedule', 'ExcelController::exportReqSchedule');
    $routes->post('getDataSchedule', 'ScheduleController::getDataSchedule');
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
    $routes->get('pemasukan/getDataByCluster', 'PemesananController::getDataByCluster');
    $routes->post('reset_pemasukan', 'WarehouseController::reset_pemasukan');
    $routes->post('hapus_pemasukan', 'WarehouseController::hapusListPemasukan');
    $routes->post('proses_pemasukan', 'WarehouseController::prosesPemasukan');

    $routes->get('getItemTypeByModel/(:any)', 'WarehouseController::getItemTypeByModel/$1');
    $routes->get('getKodeWarnaByModelAndItemType', 'WarehouseController::getKodeWarna');
    $routes->get('getWarnaDanLot', 'WarehouseController::getWarnaDanLot');
    $routes->get('getNoKarung', 'WarehouseController::getNoKarung');
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
    $routes->post('simpanPengeluaranJalur/(:any)', 'WarehouseController::simpanPengeluaranJalur/$1');
    $routes->post('hapusPengeluaranJalur', 'WarehouseController::hapusPengeluaranJalur');

    $routes->post('komplain_pemasukan', 'WarehouseController::prosesComplain');
    //
    $routes->post('warehouse/search', 'WarehouseController::search');
    $routes->post('warehouse/sisaKapasitas', 'WarehouseController::getSisaKapasitas');
    $routes->post('warehouse/getCluster', 'WarehouseController::getClusterbyId');
    $routes->get('warehouse/getNamaCluster', 'WarehouseController::getNamaCluster');
    $routes->post('warehouse/updateCluster', 'WarehouseController::updateCluster');
    $routes->get('warehouse/getNoModel', 'WarehouseController::getNoModel');
    $routes->post('warehouse/savePindahOrder', 'WarehouseController::savePindahOrder');
    $routes->post('warehouse/savePindahOrderTest', 'WarehouseController::savePindahOrderTest');
    $routes->post('warehouse/getPindahOrder', 'WarehouseController::getPindahOrder');
    $routes->post('warehouse/getPindahOrderTest', 'WarehouseController::getPindahOrderTest');
    $routes->post('warehouse/savePindahCluster', 'WarehouseController::savePindahCluster');
    $routes->post('warehouse/getPindahCluster', 'WarehouseController::getPindahCluster');
    $routes->post('warehouse/updateNoModel', 'WarehouseController::updateNoModel');
    // $routes->get('warehouse/reportPoBenang', 'WarehouseController::reportPoBenang');
    $routes->get('warehouse/reportPo/(:any)', 'WarehouseController::reportPoBenang/$1');
    $routes->get('warehouse/filterPoBenang', 'WarehouseController::filterPoBenang');
    $routes->get('warehouse/exportPoBenang', 'ExcelController::exportPoBenang');
    $routes->get('warehouse/reportDatangBenang', 'WarehouseController::reportDatangBenang');
    $routes->get('warehouse/filterDatangBenang', 'WarehouseController::filterDatangBenang');
    $routes->get('warehouse/exportDatangBenang', 'ExcelController::exportDatangBenang');
    $routes->get('warehouse/getKeteranganDatang', 'WarehouseController::getKeteranganDatang');
    $routes->post('warehouse/updateKeteranganDatang', 'WarehouseController::updateKeteranganDatang');
    $routes->get('warehouse/exportExcel', 'ExcelController::excelStockMaterial');
    $routes->get('warehouse/reportPengiriman', 'WarehouseController::reportPengiriman');
    $routes->get('warehouse/filterPengiriman', 'WarehouseController::filterPengiriman');
    $routes->get('warehouse/exportPengiriman', 'ExcelController::exportPengiriman');
    $routes->get('warehouse/reportGlobal', 'WarehouseController::reportGlobal');
    $routes->get('warehouse/filterReportGlobal', 'WarehouseController::filterReportGlobal');
    $routes->get('warehouse/exportGlobalReport', 'ExcelController::exportGlobalReport');
    $routes->get('warehouse/reportGlobalNylon', 'WarehouseController::reportGlobalNylon');
    $routes->get('warehouse/filterReportGlobalNylon', 'WarehouseController::filterReportGlobalNylon');
    $routes->get('warehouse/reportGlobalStockBenang', 'WarehouseController::reportGlobalStockBenang');
    $routes->get('warehouse/filterReportGlobalBenang', 'WarehouseController::filterReportGlobalBenang');
    $routes->get('warehouse/exportReportGlobalBenang', 'ExcelController::exportReportGlobalBenang');
    $routes->get('warehouse/reportSisaPakaiBenang', 'WarehouseController::reportSisaPakaiBenang');
    $routes->get('warehouse/filterSisaPakaiBenang', 'WarehouseController::filterSisaPakai');
    $routes->get('warehouse/exportReportSisaPakaiBenang', 'ExcelController::exportReportSisaPakai');
    $routes->get('warehouse/reportSisaPakaiNylon', 'WarehouseController::reportSisaPakaiNylon');
    $routes->get('warehouse/filterSisaPakaiNylon', 'WarehouseController::filterSisaPakai');
    $routes->get('warehouse/exportReportSisaPakaiNylon', 'ExcelController::exportReportSisaPakai');
    $routes->get('warehouse/reportSisaPakaiSpandex', 'WarehouseController::reportSisaPakaiSpandex');
    $routes->get('warehouse/filterSisaPakaiSpandex', 'WarehouseController::filterSisaPakai');
    $routes->get('warehouse/exportReportSisaPakaiSpandex', 'ExcelController::exportReportSisaPakai');
    $routes->get('warehouse/reportSisaPakaiKaret', 'WarehouseController::reportSisaPakaiKaret');
    $routes->get('warehouse/filterSisaPakaiKaret', 'WarehouseController::filterSisaPakai');
    $routes->get('warehouse/exportReportSisaPakaiKaret', 'ExcelController::exportReportSisaPakai');
    $routes->get('warehouse/historyPindahOrder', 'WarehouseController::historyPindahOrder');
    $routes->get('warehouse/exportHistoryPindahOrder', 'ExcelController::exportHistoryPindahOrder');
    $routes->get('pemesanan/historyPinjamOrder', 'PemesananController::HistoryPinjamOrder');
    $routes->get('pemesanan/exportHistoryPinjamOrder', 'ExcelController::exportHistoryPinjamOrder');
    $routes->get('warehouse/reportSisaDatangBenang', 'WarehouseController::reportSisaDatangBenang');
    $routes->get('warehouse/exportReportSisaDatangBenang', 'ExcelController::exportReportSisaDatangBenang');
    $routes->get('warehouse/reportSisaDatangNylon', 'WarehouseController::reportSisaDatangNylon');
    $routes->get('warehouse/exportReportSisaDatangNylon', 'ExcelController::exportReportSisaDatangNylon');
    $routes->get('warehouse/reportSisaDatangSpandex', 'WarehouseController::reportSisaDatangSpandex');
    $routes->get('warehouse/exportReportSisaDatangSpandex', 'ExcelController::exportReportSisaDatangSpandex');
    $routes->get('warehouse/reportSisaDatangKaret', 'WarehouseController::reportSisaDatangKaret');
    $routes->get('warehouse/exportReportSisaDatangKaret', 'ExcelController::exportReportSisaDatangKaret');
    $routes->get('warehouse/reportBenangMingguan', 'WarehouseController::reportBenangMingguan');
    $routes->get('warehouse/filterBenangMingguan', 'WarehouseController::filterBenangMingguan');
    $routes->get('warehouse/exportReportBenangMingguan', 'ExcelController::exportReportBenang');
    $routes->get('warehouse/reportBenangBulanan', 'WarehouseController::reportBenangBulanan');
    $routes->get('warehouse/filterBenangBulanan', 'WarehouseController::filterBenangBulanan');
    $routes->get('warehouse/exportReportBenangBulanan', 'ExcelController::exportReportBenang');
    $routes->get('warehouse/listOtherBarcode', 'WarehouseController::listOtherBarcode');
    $routes->get('warehouse/detailOtherBarcode/(:any)', 'WarehouseController::detailOtherBarcode/$1');
    $routes->get('warehouse/generateOtherBarcode/(:any)', 'DomPdfController::generateOtherBarcode/$1');
    $routes->get('warehouse/listPindahOrderBarcode', 'WarehouseController::listPindahOrderBarcode');
    $routes->get('warehouse/detailPindahOrderBarcode/(:any)', 'WarehouseController::detailPindahOrderBarcode/$1');
    $routes->get('warehouse/generatePindahOrderBarcode/(:any)', 'DomPdfController::generatePindahOrderBarcode/$1');
    $routes->get('warehouse/reportDatangNylon', 'WarehouseController::reportDatangNylon');
    $routes->get('warehouse/filterDatangNylon', 'WarehouseController::filterDatangNylon');
    $routes->get('warehouse/exportDatangNylon', 'ExcelController::exportDatangNylon');
    $routes->get('warehouse/reportPemakaianNylon', 'WarehouseController::reportPemakaianNylon');
    $routes->get('warehouse/filterPemakaianNylon', 'WarehouseController::filterPemakaianNylon');
    $routes->get('warehouse/exportPemakaianNylon', 'ExcelController::exportPemakaianNylon');

    $routes->get('warehouse/reportOtherOut', 'WarehouseController::reportOtherOut');
    $routes->get('warehouse/filterOtherOut', 'WarehouseController::filterOtherOut');
    $routes->get('warehouse/exportOtherOut', 'ExcelController::exportOtherOut');
    $routes->get('warehouse/returCelup', 'WarehouseController::returCelup');
    $routes->get('warehouse/reportHistoryReturCelup', 'WarehouseController::reportHistoryReturCelup');
    $routes->get('warehouse/filterHistoryReturCelup', 'WarehouseController::filterHistoryReturCelup');
    $routes->get('warehouse/exportHistoryReturCelup', 'ExcelController::exportHistoryReturCelup');
    $routes->get('warehouse/reportStockOrderBenang', 'WarehouseController::reportStockOrderBenang');
    $routes->get('warehouse/filterStockOrderBenang', 'WarehouseController::filterStockOrderBenang');
    $routes->get('warehouse/exportStockOrderBenang', 'ExcelController::exportStockOrderBenang');

    $routes->post('warehouse/savePengeluaranSelainOrder', 'WarehouseController::savePengeluaranSelainOrder');
    $routes->get('otherIn', 'WarehouseController::otherIn');
    $routes->post('otherIn/saveOtherIn', 'WarehouseController::saveOtherIn');
    $routes->get('otherIn/getItemTypeForOtherIn/(:any)', 'WarehouseController::getItemTypeForOtherIn/$1');
    $routes->post('otherIn/getKodeWarnaForOtherIn', 'WarehouseController::getKodeWarnaForOtherIn');
    $routes->post('otherIn/getWarnaForOtherIn', 'WarehouseController::getWarnaForOtherIn');
    $routes->get('otherIn/listBarcode', 'WarehouseController::listBarcode');
    $routes->post('otherIn/listBarcode/filter', 'WarehouseController::listBarcodeFilter');
    $routes->get('otherIn/detailListBarcode/(:any)', 'WarehouseController::detailListBarcode/$1');
    $routes->get('otherIn/printBarcode/(:any)', 'PdfController::printBarcodeOtherBon/$1');
    $routes->post('warehouse/saveReturCelup', 'WarehouseController::saveReturCelup');

    //
    $routes->post('getStockByParams', 'PemesananController::getStockByParams');
    $routes->get('pemesanan', 'PemesananController::index');
    $routes->get('pemesanan/(:any)/(:any)', 'PemesananController::pemesanan/$1/$2');
    $routes->post('pemesanan/filter', 'PemesananController::filterPemesanan');
    $routes->post('pemesanan/getFilterArea', 'PemesananController::getFilterArea');
    $routes->get('pemesananperarea/(:any)', 'PemesananController::pemesananPerArea/$1');
    $routes->get('detailpemesanan/(:any)/(:any)/(:any)', 'PemesananController::detailPemesanan/$1/$2/$3');
    $routes->get('selectClusterWarehouse/(:any)', 'PemesananController::selectClusterWarehouse/$1');
    $routes->post('pemesanan/saveKetGbnInPemesanan', 'PemesananController::saveKetGbnInPemesanan');
    $routes->get('pinjamOrder', 'PemesananController::pinjamOrder');
    $routes->get('pinjamOrder/options', 'PemesananController::optionsPinjamOrder');
    $routes->get('pinjamOrder/getNoModel', 'PemesananController::getNoModelPinjamOrder');
    $routes->get('pinjamOrder/getCluster', 'PemesananController::getClusterPinjamOrder');
    // $routes->get('pinjamOrder/detail', 'PemesananController::detailPinjamOrder');
    // $routes->get('pinjamOrder/options/(:segment)?', 'PemesananController::optionsPinjamOrder/$1');
    // $routes->get('pinjamOrder/options/(:any)', 'PemesananController::optionsPinjamOrder');
    $routes->post('saveUsage', 'PemesananController::saveUsage');
    $routes->get('pengiriman_area', 'PemesananController::pengirimanArea');
    $routes->post('pengiriman_area', 'PemesananController::pengirimanArea');
    $routes->get('pengiriman_area_manual', 'PemesananController::pengirimanAreaManual');
    $routes->get('pengiriman/getItemTypes', 'PemesananController::getItemTypes');
    $routes->get('pengiriman/getKodeWarna', 'PemesananController::getKodeWarna');
    $routes->get('pengiriman/getWarna', 'PemesananController::getWarna');
    $routes->post('pengiriman/saveSessionDeliveryArea', 'PemesananController::saveSessionDeliveryArea');
    $routes->post('pengiriman/removeSessionDelivery', 'PemesananController::removeSessionDelivery');
    $routes->post('updateStatusKirim', 'PemesananController::updateStatusKirim');
    $routes->post('reset_pengiriman/(:any)/(:any)', 'PemesananController::resetPengirimanArea/$1/$2');
    $routes->post('hapus_pengiriman', 'PemesananController::hapusListPengiriman');
    $routes->post('proses_pengiriman', 'PemesananController::prosesPengirimanArea');
    $routes->get('pemesanan/reportPemesananArea', 'PemesananController::reportPemesananArea');
    $routes->get('pemesanan/filterPemesananArea', 'PemesananController::filterPemesananArea');
    $routes->get('pemesanan/exportPemesananArea', 'ExcelController::excelPemesananArea');
    $routes->post('pemesanan/listBarangKeluarPertgl', 'PemesananController::listBarangKeluarPertgl');
    $routes->post('pemesanan/filterListBarangKeluarPertgl', 'PemesananController::filterListBarangKeluarPertgl');
    $routes->get('pemesanan/detailListBarangKeluar', 'PemesananController::detailListBarangKeluar');
    $routes->get('pemesanan/exportListBarangKeluar', 'ExcelController::exportListBarangKeluar');
    $routes->get('pemesanan/exportPdfListBarangKeluar', 'PdfController::exportListBarangKeluar');
    $routes->post('pemesanan/listPemesananSpandexKaretPertgl', 'PemesananController::listPemesananSpandexKaretPertgl');
    $routes->get('pemesanan/exportListPemesananSpdxKaretPertgl', 'ExcelController::exportListPemesananSpdxKaretPertgl');
    $routes->get('pemesanan/exportPdfListPemesananSpdxKaretPertgl', 'PdfController::exportListPemesananSpdxKaretPertgl');
    $routes->get('pemesanan/sisaKebutuhanArea', 'PemesananController::sisaKebutuhanArea');
    $routes->get('reportPermintaanBahanBaku', 'ExcelController::reportPermintaanBahanBaku');
    // $routes->get('pemesanan/sisaKebutuhanArea_filter', 'PemesananController::sisaKebutuhanArea');
    $routes->get('pemesanan/reportSisaKebutuhanArea', 'ExcelController::reportSisaKebutuhanArea');

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
    $routes->get('po/listTrackingPo/(:any)', 'TrackingPoCoveringController::TrackingPo/$1');
    $routes->get('po/exportPO/(:any)', 'ExcelController::generateOpenPOCoveringExcel/$1');
    $routes->get('pesanKeCovering/(:any)', 'CoveringPemesananController::pesanKeCovering/$1');
    $routes->get('updatePesanKeCovering/(:any)', 'CoveringPemesananController::updatePesanKeCovering/$1');
    //Retur
    $routes->get('retur', 'ReturController::index');
    $routes->post('retur/approve', 'ReturController::approve');
    $routes->post('retur/reject', 'ReturController::reject');
    $routes->get('retur/listBarcodeRetur', 'ReturController::listBarcodeRetur');
    $routes->get('retur/detailBarcodeRetur/(:any)', 'ReturController::detailBarcodeRetur/$1');
    $routes->get('retur/generateBarcodeRetur/(:any)', 'DomPdfController::generateBarcodeRetur/$1');
    $routes->get('retur/reportReturArea', 'ReturController::reportReturArea');
    $routes->get('retur/filterReturArea', 'ReturController::filterReturArea');
    $routes->get('retur/exportReturArea', 'ExcelController::exportReturArea');
    $routes->get('retur/returSample', 'ReturController::returSample');
    $routes->get('retur/getItemTypeForReturSample', 'ReturController::getItemTypeForReturSample');
    $routes->post('retur/saveReturSample', 'ReturController::saveReturSample');

    //Po Plus
    $routes->get('poplus', 'PoTambahanController::index');
    $routes->get('poplus/detail', 'PoTambahanController::detailPoPlus');
    $routes->post('approvePoPlusArea', 'PoTambahanController::prosesApprovePoPlusArea');
    $routes->post('rejectPoPlusArea', 'PoTambahanController::prosesRejectPoPlusArea');
    $routes->get('poplus/reportPoTambahan', 'PoTambahanController::reportPoTambahan');
    $routes->post('poplus/reportPoTambahan', 'PoTambahanController::reportPoTambahan');
    $routes->get('poplus/exportPoTambahan', 'ExcelController::exportPoTambahan');

    // tambahan waktu
    $routes->get('pemesanan/requestAdditionalTime', 'PemesananController::requestAdditionalTime');
    $routes->get('pemesanan/getCountStatusRequest', 'PemesananController::getCountStatusRequest');
    $routes->post('pemesanan/additional-time/accept', 'PemesananController::additionalTimeAccept');
    $routes->post('pemesanan/additional-time/reject', 'PemesananController::additionalTimeReject');

    $routes->get('pemesanan/permintaanKaretCovering', 'PemesananController::permintaanKaretCovering');
    $routes->get('pemesanan/permintaanSpandexCovering', 'PemesananController::permintaanSpandexCovering');
    $routes->get('pemesanan/getFilterPemesananKaret', 'PemesananController::getFilterPemesananKaret');
    $routes->get('pemesanan/getFilterPemesananSpandex', 'PemesananController::getFilterPemesananSpandex');
    $routes->get('pemesanan/exportPermintaanKaret', 'ExcelController::exportPermintaanKaret');
    $routes->get('pemesanan/exportPermintaanSpandex', 'ExcelController::exportPermintaanSpandex');

    $routes->get('statusBahanBaku', 'ScheduleController::statusBahanBaku');
    $routes->get('filterstatusbahanbaku', 'ScheduleController::filterstatusbahanbaku');


    // pengaduan
    $routes->get('pengaduan', 'ApiController::getpengaduan');
});

// celup routes
$routes->group('/celup', ['filter' => 'celup'], function ($routes) {
    $routes->get('', 'DashboardCelupController::index');
    $routes->get('getStackedChartData', 'DashboardCelupController::getStackedChartData');
    // $routes->get('schedule', 'ScheduleController::index');
    $routes->get('schedule/acrylic', 'ScheduleController::acrylic');
    $routes->get('schedule/nylon', 'ScheduleController::nylon');
    $routes->get('reqschedule', 'ScheduleController::reqschedule');
    $routes->post('getDataEditSchedule', 'ScheduleController::getDataEditSchedule');
    $routes->post('schedule', 'ScheduleController::reqschedule');
    $routes->get('schedule', 'ScheduleController::index');
    $routes->get('edit/(:num)', 'CelupController::editStatus/$1');
    $routes->post('updateSchedule/(:num)', 'CelupController::updateSchedule/$1');
    $routes->get('schedule/getScheduleDetails/(:any)/(:any)/(:any)', 'ScheduleController::getScheduleDetails/$1/$2/$3');
    $routes->get('schedule/editSchedule', 'ScheduleController::editSchedule');
    $routes->get('mesin/mesinCelup', 'MesinCelupController::mesinCelup');
    //Report
    $routes->get('schedule/reportSchWeekly', 'ScheduleController::reportSchWeekly');
    $routes->get('schedule/filterSchWeekly', 'ScheduleController::filterSchWeekly');
    $routes->get('schedule/exportScheduleWeekly', 'ExcelController::exportScheduleWeekly');

    $routes->get('outCelup', 'CelupController::outCelup');
    $routes->get('outCelup/getDetail/(:num)', 'CelupController::getDetail/$1');
    $routes->get('outCelup/editBon/(:num)', 'CelupController::editBon/$1');
    $routes->post('outCelup/updateBon/(:num)', 'CelupController::updateBon/$1');
    $routes->delete('outCelup/deleteBon/(:num)', 'CelupController::deleteBon/$1');
    $routes->get('outCelup/deleteKarung/(:num)', 'CelupController::deleteKarung/$1');
    // $routes->get('insertBon/(:num)', 'CelupController::insertBon/$1');
    $routes->get('createBon', 'CelupController::createBon');
    $routes->post('createBon/getItem/(:num)', 'CelupController::getItem/$1');
    $routes->get('createBon/cekNoKarung', 'CelupController::cekNoKarung');

    $routes->post('outCelup/saveBon/', 'CelupController::saveBon');
    $routes->get('retur', 'CelupController::retur');
    $routes->post('retur', 'CelupController::retur');
    $routes->get('editretur/(:num)', 'CelupController::editRetur/$1');
    $routes->post('proseseditretur/(:num)', 'CelupController::prosesEditRetur/$1');
    $routes->get('createRetur/(:num)', 'CelupController::createRetur/$1');
    $routes->post('saveBonRetur', 'CelupController::saveBonRetur');
    // $routes->get('printBon/(:num)', 'DomPdfController::printBon/$1');
    $routes->get('printBarcode/(:num)', 'DomPdfController::printBarcode/$1');
    $routes->get('printBon/(:num)', 'PdfController::printBon/$1');
    $routes->get('generate/(:num)', 'CelupController::generateBarcode/$1');
    // pengaduan
    $routes->get('pengaduan', 'ApiController::getpengaduan');
});



// covering routes
$routes->group('/covering', ['filter' => 'covering'], function ($routes) {
    $routes->get('po-manual/covering', 'PoManualController::createCovering');
    $routes->post('po-manual/covering/save', 'PoManualController::saveCovering');
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
    $routes->get('schedule/reqschedule', 'CoveringWarehouseController::reqschedule');
    $routes->get('schedule/reqschedule/show/(:num)', 'CelupController::editStatus/$1');
    $routes->post('schedule/reqschedule', 'ScheduleController::reqschedule');



    $routes->get('mesin/mesinCelup', 'MesinCelupController::mesinCelup');
    $routes->post('mesin/saveDataMesin', 'MesinCelupController::saveDataMesin');
    $routes->get('mesin/getMesinDetails/(:num)', 'MesinCelupController::getMesinDetails/$1');
    $routes->post('mesin/cekNoMesin', 'MesinCelupController::cekNoMesin');
    $routes->post('mesin/updateDataMesin', 'MesinCelupController::updateDataMesin');
    $routes->get('mesin/deleteDataMesin/(:num)', 'MesinCelupController::deleteDataMesin/$1');

    $routes->get('masterMaterial', 'MastermaterialController::index');
    $routes->post('tampilMasterMaterial', 'MastermaterialController::tampilMasterMaterial');
    $routes->get('getMasterMaterialDetails', 'MastermaterialController::getMasterMaterialDetails');
    $routes->post('updateMasterMaterial', 'MastermaterialController::updateMasterMaterial');
    $routes->post('saveMasterMaterial', 'MastermaterialController::saveMasterMaterial');
    $routes->get('deleteMasterMaterial', 'MastermaterialController::deleteMasterMaterial');
    $routes->get('po/bukaPoCovering', 'CoveringController::bukaPoCovering');
    $routes->post('po/getDetailByTglPO', 'CoveringController::getDetailByTglPO');
    $routes->get('po/getItemType/(:any)', 'CoveringController::getItemType/$1');
    $routes->get('po/getKodeWarna', 'CoveringController::getKodeWarna');
    $routes->get('po/getColor', 'CoveringController::getColor');
    $routes->get('po/getTotalKgPo', 'CoveringController::getTotalKgPo');
    $routes->post('po/saveOpenPOCovering', 'CoveringController::saveOpenPOCovering');
    $routes->get('poDetail/(:any)', 'CoveringController::poDetail/$1');
    $routes->get('getDetailByNoModel/(:any)/(:any)', 'CoveringController::getDetailByNoModel/$1/$2');
    $routes->post('po/simpanKeSession', 'CoveringController::simpanKeSession');
    $routes->post('po/savePOCovering', 'CoveringController::savePOCovering');
    $routes->get('po/deletePOCovering/(:any)', 'CoveringController::unsetSession/$1');
    // $routes->get('po/exportPO/(:any)', 'PdfController::generateOpenPOCovering/$1');
    $routes->get('po/exportPO/(:any)', 'ExcelController::generateOpenPOCoveringExcel/$1');
    $routes->get('po/listTrackingPo', 'TrackingPoCoveringController::listTrackingPo');
    $routes->get('po/listTrackingPo/(:any)', 'TrackingPoCoveringController::TrackingPo/$1');
    $routes->post('po/updateListTrackingPo/(:any)', 'TrackingPoCoveringController::updateListTrackingPo/$1');
    $routes->get('po/detailPoCovering/(:any)', 'CoveringController::detailPoCovering/$1');
    $routes->post('po/updateDetailPoCovering/(:any)', 'CoveringController::updateDetailPoCovering/$1');
    $routes->get('po/deleteDetailPoCovering/(:num)', 'CoveringController::deleteDetailPoCovering/$1');
    $routes->get('po/deleteMultipleDetailPoCovering', 'CoveringController::deleteMultipleDetailPoCovering/$1');

    // warehouse barang jadi
    $routes->get('warehouse', 'CoveringWarehouseController::index');
    $routes->post('warehouse/tambahStock', 'CoveringWarehouseController::create');
    $routes->post('warehouse/updateStock', 'CoveringWarehouseController::updateStock');
    $routes->post('warehouse/updateEditStock', 'CoveringWarehouseController::updateEditStock');
    $routes->get('warehouse/getStock/(:any)', 'CoveringWarehouseController::getStock/$1');
    $routes->get('warehouse/reportPemasukan', 'CoveringWarehouseController::reportPemasukan');
    $routes->get('warehouse/excelPemasukanCovering', 'ExcelController::excelPemasukanCovering');
    $routes->get('warehouse/reportPengeluaran', 'CoveringWarehouseController::reportPengeluaran');
    $routes->get('warehouse/excelPengeluaranCovering', 'ExcelController::excelPengeluaranCovering');

    // bahanbaku
    $routes->get('warehouse/reportPemasukanBb', 'CoveringWarehouseBBController::reportPemasukanBb');
    $routes->get('warehouse/excelPemasukanBb', 'ExcelController::excelPemasukanBb');
    $routes->get('warehouse/reportPengeluaranBb', 'CoveringWarehouseBBController::reportPengeluaranBb');
    $routes->get('warehouse/excelPengeluaranBb', 'ExcelController::excelPengeluaranBb');

    $routes->get('warehouse/pengeluaran_jalur', 'CoveringController::pengeluaranJalur');
    $routes->get('warehouse/pengiriman_area', 'CoveringController::pengirimanArea');
    $routes->post('warehouse/exportStock', 'ExcelController::exportStock');
    $routes->post('warehouse/exportStockPdf', 'PdfController::exportStockPdf');
    // import stok barang jadi
    $routes->post('warehouse/importStokBarangJadi', 'CoveringWarehouseController::importStokBarangJadi');
    $routes->post('warehouse/importStokCovering', 'CoveringWarehouseController::importStokCovering');

    // delete stok barang jadi
    $routes->post('warehouse/deleteStokBarangJadi/(:num)', 'CoveringWarehouseController::deleteStokBarangJadi/$1');
    $routes->get('warehouse/templateStokBarangJadi', 'CoveringWarehouseController::templateStokBarangJadi');
    $routes->get('warehouse/templateStokBahanBaku', 'CoveringWarehouseBBController::templateStokBahanBaku');

    $routes->get('warehouse/(:any)', 'CoveringWarehouseController::getStockByMesin/$1');


    // warehouse bahan baku
    $routes->get('warehouseBB', 'CoveringWarehouseBBController::index');
    $routes->post('warehouseBB/store', 'CoveringWarehouseBBController::store');
    $routes->post('warehouseBB/update/(:any)', 'CoveringWarehouseBBController::update/$1');
    $routes->post('warehouseBB/pemasukan', 'CoveringWarehouseBBController::pemasukan');
    $routes->post('warehouseBB/pengeluaran', 'CoveringWarehouseBBController::pengeluaran');
    $routes->get('warehouseBB/BahanBakuCovPdf', 'PdfController::BahanBakuCovPdf');
    $routes->get('warehouseBB/BahanBakuCovExcel', 'ExcelController::BahanBakuCovExcel');
    $routes->get('warehouseBB/deleteBahanBakuCov/(:num)', 'CoveringWarehouseBBController::deleteBahanBakuCov/$1');
    $routes->post('warehouse/importStokBahanBakuJenis', 'CoveringWarehouseBBController::importStokBahanBakuJenis');
    $routes->post('warehouse/importStokBahanBaku', 'CoveringWarehouseBBController::importStokBahanBaku');
    $routes->get('warehouseBB/nylon', 'CoveringWarehouseBBController::nylon');
    $routes->get('warehouseBB/polyester', 'CoveringWarehouseBBController::polyester');
    $routes->get('warehouseBB/recycledPolyester', 'CoveringWarehouseBBController::recycledPolyester');
    $routes->get('warehouseBB/spandex', 'CoveringWarehouseBBController::spandex');
    $routes->get('warehouseBB/rubber', 'CoveringWarehouseBBController::rubber');

    //Pemesanan
    $routes->get('pemesanan', 'CoveringPemesananController::index');
    $routes->get('pemesanan/(:any)', 'CoveringPemesananController::pemesanan/$1');
    $routes->get('detailPemesanan/(:any)', 'CoveringPemesananController::detailPemesanan/$1');
    $routes->get('reportPemesananKaretCovering', 'CoveringPemesananController::reportPemesananKaretCovering');
    $routes->get('filterPemesananKaretCovering', 'CoveringPemesananController::filterPemesananKaretCovering');
    $routes->get('excelPemesananCovering', 'ExcelController::excelPemesananCovering');
    $routes->get('reportPemesananSpandexCovering', 'CoveringPemesananController::reportPemesananSpandexCovering');
    $routes->get('filterPemesananSpandexCovering', 'CoveringPemesananController::filterPemesananSpandexCovering');
    $routes->get('excelPemesananCoveringPerArea', 'ExcelController::excelPemesananCoveringPerArea');
    $routes->get('exportPemesananSandexKaretCovering', 'PdfController::exportPemesananSandexKaretCovering');

    $routes->post('updatePemesanan/(:any)', 'CoveringPemesananController::updatePemesanan/$1');
    $routes->get('generatePengeluaranSpandexKaretCovering/(:any)/(:any)', 'PdfController::generatePengeluaranSpandexKaretCovering/$1/$2');
    $routes->get('getCodePemesanan', 'CoveringPemesananController::getCodePemesanan');
    $routes->get('getColorPemesanan', 'CoveringPemesananController::getColorPemesanan');
    // pengaduan
    $routes->get('pengaduan', 'ApiController::getpengaduan');
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
    $routes->post('getMasterData', 'MasterdataController::getMasterData');

    $routes->get('masterdata', 'MasterdataController::index');
    $routes->post('tampilMasterOrder', 'MasterdataController::tampilMasterOrder');
    $routes->get('getOrderDetails/(:num)', 'MasterdataController::getOrderDetails/$1');
    $routes->post('updateOrder', 'MasterdataController::updateOrder');
    $routes->post('deleteOrder', 'MasterdataController::deleteOrder');
    $routes->get('masterdata/reportMasterOrder', 'MasterdataController::reportMasterOrder');
    $routes->get('masterdata/filterMasterOrder', 'MasterdataController::filterMasterOrder');
    $routes->get('masterdata/excelMasterOrder', 'ExcelController::excelMasterOrder');
    $routes->get('masterdata/poGabungan', 'PoGabunganController::index');
    $routes->get('masterdata/poGabungan/(:any)', 'PoGabunganController::poGabungan/$1');
    $routes->get('masterdata/poGabunganDetail/(:any)', 'PoGabunganController::poGabunganDetail/$1');
    $routes->get('masterdata/poBooking', 'PoBookingController::index');
    $routes->get('masterdata/poBooking/create', 'PoBookingController::create');
    $routes->get('masterdata/poBooking/getItemType', 'PoBookingController::getItemType');
    $routes->get('masterdata/poBooking/getKodeWarna', 'PoBookingController::getKodeWarna');
    $routes->get('masterdata/poBooking/getColor', 'PoBookingController::getColor');
    $routes->post('masterdata/poBooking/saveOpenPoBooking', 'PoBookingController::saveOpenPoBooking');
    // $routes->get('masterdata/poBooking/exportPoBooking', 'PdfController::generateOpenPOBooking');
    $routes->get('masterdata/poBooking/exportPoBooking', 'ExcelController::generateOpenPOBookingExcel');
    $routes->get('masterdata/poBooking/detail', 'PoBookingController::detail');
    $routes->post('masterdata/poBooking/updatePoBooking', 'PoBookingController::updatePoBooking');
    $routes->post('masterdata/poBooking/deletePoBooking', 'PoBookingController::deletePoBooking');
    $routes->get('masterdata/poManual', 'PoManualController::index');
    $routes->get('masterdata/poManual/create', 'PoManualController::create');
    $routes->get('masterdata/poManual/getNoOrderByModel', 'PoManualController::getNoOrderByModel');
    $routes->post('masterdata/poManual/saveOpenPoManual', 'PoManualController::saveOpenPoManual');
    $routes->get('masterdata/poManual/detail', 'PoManualController::detail');
    $routes->post('masterdata/poManual/updatePoManual', 'PoManualController::updatePoManual');
    $routes->post('masterdata/poManual/deletePoManual', 'PoManualController::deletePoManual');
    // $routes->get('masterdata/poManual/exportPoManual', 'PdfController::generateOpenPOManual');
    $routes->get('masterdata/poManual/exportPoManual', 'ExcelController::generateOpenPOManualExcel');
    $routes->get('masterdata/cekStockOrder/(:any)/(:any)/(:any)', 'PoGabunganController::cekStockOrder/$1/$2/$3');
    $routes->post('openPO/saveOpenPOGabungan', 'PoGabunganController::saveOpenPOGabungan');
    $routes->get('listPoGabungan', 'PoGabunganController::listPoGabungan');
    $routes->get('getPoGabungan/(:any)', 'PoGabunganController::getPoGabungan/$1');
    $routes->post('updatePoGabungan', 'PoGabunganController::updatePoGabungan');
    $routes->post('deletePoGabungan/(:num)', 'PoGabunganController::deletePoGabungan/$1');
    // $routes->get('exportOpenPOGabung', 'PdfController::exportOpenPOGabung');
    $routes->get('exportOpenPOGabung', 'ExcelController::exportOpenPOGabungNew');
    // $routes->post('exportPoNylon', 'ExcelController::generateOpenPONylon');
    $routes->post('exportPoNylon', 'ExcelController::generateOpenPONylonNew');
    $routes->get('exportPoBoking', 'ExcelController::exportPoBooking');

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
    $routes->post('splitMaterial', 'MaterialController::splitMaterial');

    $routes->post('import/mu', 'MasterdataController::importMU');
    $routes->post('revise/mu', 'MasterdataController::reviseMU');
    $routes->get('deleteDuplicate/mu/(:any)', 'MasterdataController::deleteDuplicateMu/$1');

    $routes->get('masterMaterial', 'MastermaterialController::index');
    $routes->post('tampilMasterMaterial', 'MastermaterialController::tampilMasterMaterial');
    $routes->get('getMasterMaterialDetails', 'MastermaterialController::getMasterMaterialDetails');
    $routes->post('updateMasterMaterial', 'MastermaterialController::updateMasterMaterial');
    $routes->post('saveMasterMaterial', 'MastermaterialController::saveMasterMaterial');
    $routes->get('deleteMasterMaterial', 'MastermaterialController::deleteMasterMaterial');

    $routes->get('masterBuyer', 'MasterBuyerController::index');
    $routes->post('tampilMasterBuyer', 'MasterBuyerController::tampilMasterBuyer');
    $routes->get('getMasterBuyerDetails', 'MasterBuyerController::getMasterBuyerDetails');
    $routes->post('updateMasterBuyer', 'MasterBuyerController::updateMasterBuyer');
    $routes->post('saveMasterBuyer', 'MasterBuyerController::saveMasterBuyer');
    $routes->get('deleteMasterBuyer', 'MasterBuyerController::deleteMasterBuyer');

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
    $routes->get('schedule/reportSchBenang', 'ScheduleController::reportSchBenang');
    $routes->get('schedule/filterSchBenang', 'ScheduleController::filterSchBenang');
    $routes->get('schedule/exportScheduleBenang', 'ExcelController::exportScheduleBenang');
    $routes->get('schedule/reportSchNylon', 'ScheduleController::reportSchNylon');
    $routes->get('schedule/filterSchNylon', 'ScheduleController::filterSchNylon');
    $routes->get('schedule/exportScheduleNylon', 'ExcelController::exportScheduleNylon');
    $routes->get('schedule/reportSchWeekly', 'ScheduleController::reportSchWeekly');
    $routes->get('schedule/filterSchWeekly', 'ScheduleController::filterSchWeekly');
    $routes->get('schedule/exportScheduleWeekly', 'ExcelController::exportScheduleWeekly');
    $routes->get('schedule/reportDataTagihanBenang', 'ScheduleController::reportDataTagihanBenang');
    $routes->get('schedule/filterTagihanBenang', 'ScheduleController::filterTagihanBenang');
    $routes->get('schedule/exportTagihanBenang', 'ExcelController::exportTagihanBenang');

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
    $routes->get('getNoKarung', 'WarehouseController::getNoKarung');
    $routes->get('getKgsDanCones', 'WarehouseController::getKgsDanCones');
    $routes->post('getcluster', 'WarehouseController::getCluster');
    $routes->post('proses_pemasukan_manual', 'WarehouseController::prosesPemasukanManual');
    $routes->get('pengeluaran_jalur', 'WarehouseController::pengeluaranJalur');
    $routes->post('pengeluaran_jalur', 'WarehouseController::pengeluaranJalur');
    $routes->post('reset_pengeluaran', 'WarehouseController::resetPengeluaranJalur');
    $routes->post('hapus_pengeluaran', 'WarehouseController::hapusListPengeluaran');
    $routes->get('pengiriman_area', 'PemesananController::pengirimanArea');
    $routes->post('pengiriman_area', 'PemesananController::pengirimanArea');
    $routes->get('pengiriman_area_manual', 'PemesananController::pengirimanAreaManual');
    $routes->get('pengiriman/getItemTypes', 'PemesananController::getItemTypes');
    $routes->get('pengiriman/getKodeWarna', 'PemesananController::getKodeWarna');
    $routes->get('pengiriman/getWarna', 'PemesananController::getWarna');
    $routes->post('pengiriman/saveSessionDeliveryArea', 'PemesananController::saveSessionDeliveryArea');
    $routes->post('pengiriman/removeSessionDelivery', 'PemesananController::removeSessionDelivery');
    $routes->post('updateStatusKirim', 'PemesananController::updateStatusKirim');
    $routes->post('reset_pengiriman/(:any)/(:any)', 'PemesananController::resetPengirimanArea/$1/$2');
    $routes->post('hapus_pengiriman', 'PemesananController::hapusListPengiriman');
    $routes->post('proses_pengiriman', 'PemesananController::prosesPengirimanArea');
    $routes->get('pengeluaran', 'WarehouseController::pengeluaran');

    $routes->post('warehouse/search', 'WarehouseController::search');
    $routes->post('warehouse/sisaKapasitas', 'WarehouseController::getSisaKapasitas');
    $routes->post('warehouse/getCluster', 'WarehouseController::getClusterbyId');
    $routes->get('warehouse/getNamaCluster', 'WarehouseController::getNamaCluster');
    $routes->post('warehouse/updateCluster', 'WarehouseController::updateCluster');
    $routes->get('warehouse/getNoModel', 'WarehouseController::getNoModel');
    $routes->post('warehouse/savePindahOrder', 'WarehouseController::savePindahOrder');
    $routes->post('warehouse/savePindahOrderTest', 'WarehouseController::savePindahOrderTest');
    $routes->post('warehouse/getPindahOrder', 'WarehouseController::getPindahOrder');
    $routes->post('warehouse/getPindahOrderTest', 'WarehouseController::getPindahOrderTest');
    $routes->post('warehouse/savePindahCluster', 'WarehouseController::savePindahCluster');
    $routes->post('warehouse/getPindahCluster', 'WarehouseController::getPindahCluster');
    $routes->post('warehouse/updateNoModel', 'WarehouseController::updateNoModel');
    // $routes->get('warehouse/reportPoBenang', 'WarehouseController::reportPoBenang');
    $routes->get('warehouse/reportPo/(:any)', 'WarehouseController::reportPoBenang/$1');
    $routes->get('warehouse/filterPoBenang', 'WarehouseController::filterPoBenang');
    $routes->get('warehouse/exportPoBenang', 'ExcelController::exportPoBenang');
    $routes->get('warehouse/reportDatangBenang', 'WarehouseController::reportDatangBenang');
    $routes->get('warehouse/filterDatangBenang', 'WarehouseController::filterDatangBenang');
    $routes->get('warehouse/exportDatangBenang', 'ExcelController::exportDatangBenang');
    $routes->get('warehouse/getKeteranganDatang', 'WarehouseController::getKeteranganDatang');
    $routes->post('warehouse/updateKeteranganDatang', 'WarehouseController::updateKeteranganDatang');
    $routes->get('warehouse/exportExcel', 'ExcelController::excelStockMaterial');
    $routes->get('warehouse/reportPengiriman', 'WarehouseController::reportPengiriman');
    $routes->get('warehouse/filterPengiriman', 'WarehouseController::filterPengiriman');
    $routes->get('warehouse/reportOtherOut', 'WarehouseController::reportOtherOut');
    $routes->get('warehouse/filterOtherOut', 'WarehouseController::filterOtherOut');
    $routes->get('warehouse/exportPengiriman', 'ExcelController::exportPengiriman');
    $routes->get('warehouse/reportGlobal', 'WarehouseController::reportGlobal');
    $routes->get('warehouse/filterReportGlobal', 'WarehouseController::filterReportGlobal');
    $routes->get('warehouse/exportGlobalReport', 'ExcelController::exportGlobalReport');
    $routes->get('warehouse/reportGlobalNylon', 'WarehouseController::reportGlobalNylon');
    $routes->get('warehouse/filterReportGlobalNylon', 'WarehouseController::filterReportGlobalNylon');
    $routes->get('warehouse/reportGlobalStockBenang', 'WarehouseController::reportGlobalStockBenang');
    $routes->get('warehouse/filterReportGlobalBenang', 'WarehouseController::filterReportGlobalBenang');
    $routes->get('warehouse/exportReportGlobalBenang', 'ExcelController::exportReportGlobalBenang');
    $routes->get('warehouse/reportSisaPakaiBenang', 'WarehouseController::reportSisaPakaiBenang');
    $routes->get('warehouse/filterSisaPakaiBenang', 'WarehouseController::filterSisaPakai');
    $routes->get('warehouse/exportReportSisaPakaiBenang', 'ExcelController::exportReportSisaPakai');
    $routes->get('warehouse/reportSisaPakaiNylon', 'WarehouseController::reportSisaPakaiNylon');
    $routes->get('warehouse/filterSisaPakaiNylon', 'WarehouseController::filterSisaPakai');
    $routes->get('warehouse/exportReportSisaPakaiNylon', 'ExcelController::exportReportSisaPakai');
    $routes->get('warehouse/reportSisaPakaiSpandex', 'WarehouseController::reportSisaPakaiSpandex');
    $routes->get('warehouse/filterSisaPakaiSpandex', 'WarehouseController::filterSisaPakai');
    $routes->get('warehouse/exportReportSisaPakaiSpandex', 'ExcelController::exportReportSisaPakai');
    $routes->get('warehouse/reportSisaPakaiKaret', 'WarehouseController::reportSisaPakaiKaret');
    $routes->get('warehouse/filterSisaPakaiKaret', 'WarehouseController::filterSisaPakai');
    $routes->get('warehouse/exportReportSisaPakaiKaret', 'ExcelController::exportReportSisaPakai');
    $routes->get('warehouse/historyPindahOrder', 'WarehouseController::historyPindahOrder');
    $routes->get('warehouse/exportHistoryPindahOrder', 'ExcelController::exportHistoryPindahOrder');
    $routes->get('pemesanan/historyPinjamOrder', 'PemesananController::HistoryPinjamOrder');
    $routes->get('pemesanan/exportHistoryPinjamOrder', 'ExcelController::exportHistoryPinjamOrder');
    $routes->get('warehouse/reportSisaDatangBenang', 'WarehouseController::reportSisaDatangBenang');
    $routes->get('warehouse/exportReportSisaDatangBenang', 'ExcelController::exportReportSisaDatangBenang');
    $routes->get('warehouse/reportSisaDatangNylon', 'WarehouseController::reportSisaDatangNylon');
    $routes->get('warehouse/exportReportSisaDatangNylon', 'ExcelController::exportReportSisaDatangNylon');
    $routes->get('warehouse/reportSisaDatangSpandex', 'WarehouseController::reportSisaDatangSpandex');
    $routes->get('warehouse/exportReportSisaDatangSpandex', 'ExcelController::exportReportSisaDatangSpandex');
    $routes->get('warehouse/reportSisaDatangKaret', 'WarehouseController::reportSisaDatangKaret');
    $routes->get('warehouse/exportReportSisaDatangKaret', 'ExcelController::exportReportSisaDatangKaret');
    $routes->get('warehouse/reportBenangMingguan', 'WarehouseController::reportBenangMingguan');
    $routes->get('warehouse/filterBenangMingguan', 'WarehouseController::filterBenangMingguan');
    $routes->get('warehouse/exportReportBenangMingguan', 'ExcelController::exportReportBenang');
    $routes->get('warehouse/reportBenangBulanan', 'WarehouseController::reportBenangBulanan');
    $routes->get('warehouse/filterBenangBulanan', 'WarehouseController::filterBenangBulanan');
    $routes->get('warehouse/exportReportBenangBulanan', 'ExcelController::exportReportBenang');
    $routes->get('warehouse/listOtherBarcode', 'WarehouseController::listOtherBarcode');
    $routes->get('warehouse/detailOtherBarcode/(:any)', 'WarehouseController::detailOtherBarcode/$1');
    $routes->get('warehouse/generateOtherBarcode/(:any)', 'DomPdfController::generateOtherBarcode/$1');
    $routes->get('warehouse/reportDatangNylon', 'WarehouseController::reportDatangNylon');
    $routes->get('warehouse/filterDatangNylon', 'WarehouseController::filterDatangNylon');
    $routes->get('warehouse/exportDatangNylon', 'ExcelController::exportDatangNylon');
    $routes->get('warehouse/reportPemakaianNylon', 'WarehouseController::reportPemakaianNylon');
    $routes->get('warehouse/filterPemakaianNylon', 'WarehouseController::filterPemakaianNylon');
    $routes->get('warehouse/exportPemakaianNylon', 'ExcelController::exportPemakaianNylon');
    $routes->get('warehouse/reportIndri', 'WarehouseController::reportIndri');
    $routes->get('warehouse/filterReportIndri', 'WarehouseController::filterReportIndri');
    $routes->get('warehouse/exportReportIndri', 'ExcelController::exportReportIndri');

    $routes->get('warehouse/reportOtherOut', 'WarehouseController::reportOtherOut');
    $routes->get('warehouse/filterOtherOut', 'WarehouseController::filterOtherOut');
    $routes->get('warehouse/exportOtherOut', 'ExcelController::exportOtherOut');

    $routes->post('warehouse/savePengeluaranSelainOrder', 'WarehouseController::savePengeluaranSelainOrder');
    $routes->get('otherIn', 'WarehouseController::otherIn');
    $routes->post('otherIn/saveOtherIn', 'WarehouseController::saveOtherIn');
    $routes->get('otherIn/getItemTypeForOtherIn/(:any)', 'WarehouseController::getItemTypeForOtherIn/$1');
    $routes->post('otherIn/getKodeWarnaForOtherIn', 'WarehouseController::getKodeWarnaForOtherIn');
    $routes->post('otherIn/getWarnaForOtherIn', 'WarehouseController::getWarnaForOtherIn');
    $routes->get('otherIn/listBarcode', 'WarehouseController::listBarcode');
    $routes->post('otherIn/listBarcode/filter', 'WarehouseController::listBarcodeFilter');
    $routes->get('otherIn/detailListBarcode/(:any)', 'WarehouseController::detailListBarcode/$1');
    $routes->get('otherIn/printBarcode/(:any)', 'PdfController::printBarcodeOtherBon/$1');
    $routes->get('importPemasukan', 'WarehouseController::importPemasukan');
    $routes->post('warehouse/saveReturCelup', 'WarehouseController::saveReturCelup');

    //Po Plus
    $routes->get('poplus', 'PoTambahanController::index');
    $routes->get('poplus/detail', 'PoTambahanController::detailPoPlus');
    $routes->post('approvePoPlusArea', 'PoTambahanController::prosesApprovePoPlusArea');
    $routes->post('rejectPoPlusArea', 'PoTambahanController::prosesRejectPoPlusArea');
    $routes->get('poplus/reportPoTambahan', 'PoTambahanController::reportPoTambahan');
    $routes->post('poplus/reportPoTambahan', 'PoTambahanController::reportPoTambahan');
    $routes->get('poplus/exportPoTambahan', 'ExcelController::exportPoTambahan');
    $routes->get('poplus/form_potambahan', 'GodController::formPoTambahan');
    $routes->get('poplus/getNoModelByArea', 'GodController::getNoModelByArea');
    $routes->get('poTambahanDetail/(:any)/(:any)', 'GodController::poTambahanDetail/$1/$2');
    $routes->post('savePoTambahan', 'GodController::savePoTambahan');

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

    $routes->get('importPemesanan', 'GodController::importPemesanan');
    // routes
    $routes->get('pemesanan', 'PemesananController::pemesananArea');                 // view
    $routes->get('filter_pemesananarea', 'PemesananController::pemesananArea');      // tetap untuk kompatibilitas
    $routes->get('pemesanan/data', 'PemesananController::pemesananAreaData');        // endpoint DataTables (AJAX)

    $routes->post('getUpdateListPemesanan', 'PemesananController::getUpdateListPemesanan');
    $routes->post('updateListPemesanan', 'ApiController::updatePemesananArea');
    $routes->get('pemesanan/reportPemesananArea', 'PemesananController::reportPemesananArea');
    $routes->get('pemesanan/filterPemesananArea', 'PemesananController::filterPemesananArea');
    $routes->get('pemesanan/exportPemesananArea', 'ExcelController::excelPemesananArea');
    $routes->get('pemesanan/ubahTanggalPemesanan', 'PemesananController::ubahTanggalPemesanan');
    $routes->post('pemesanan/updateRangeSeluruhArea', 'PemesananController::updateRangeSeluruhArea');
    $routes->post('pemesanan/updateRangeAreaTertentu', 'PemesananController::updateRangeAreaTertentu');

    // tambahan waktu
    $routes->get('pemesanan/requestAdditionalTime', 'PemesananController::requestAdditionalTime');
    $routes->get('pemesanan/getCountStatusRequest', 'PemesananController::getCountStatusRequest');
    $routes->post('pemesanan/additional-time/accept', 'PemesananController::additionalTimeAccept');
    $routes->post('pemesanan/additional-time/reject', 'PemesananController::additionalTimeReject');

    $routes->get('pemesanan/permintaanKaretCovering', 'PemesananController::permintaanKaretCovering');
    $routes->get('pemesanan/permintaanSpandexCovering', 'PemesananController::permintaanSpandexCovering');
    $routes->get('pemesanan/getFilterPemesananKaret', 'PemesananController::getFilterPemesananKaret');
    $routes->get('pemesanan/getFilterPemesananSpandex', 'PemesananController::getFilterPemesananSpandex');
    $routes->get('pemesanan/exportPermintaanKaret', 'ExcelController::exportPermintaanKaret');
    $routes->get('pemesanan/exportPermintaanSpandex', 'ExcelController::exportPermintaanSpandex');
    $routes->get('pemesanan/sisaKebutuhanArea', 'PemesananController::sisaKebutuhanArea');
    $routes->post('pemesanan/listBarangKeluarPertgl', 'PemesananController::listBarangKeluarPertgl');
    $routes->post('pemesanan/filterListBarangKeluarPertgl', 'PemesananController::filterListBarangKeluarPertgl');
    $routes->get('pemesanan/detailListBarangKeluar', 'PemesananController::detailListBarangKeluar');
    $routes->get('pemesanan/exportListBarangKeluar', 'ExcelController::exportListBarangKeluar');
    $routes->get('pemesanan/exportPdfListBarangKeluar', 'PdfController::exportListBarangKeluar');
    $routes->post('pemesanan/listPemesananSpandexKaretPertgl', 'PemesananController::listPemesananSpandexKaretPertgl');
    $routes->get('pemesanan/exportListPemesananSpdxKaretPertgl', 'ExcelController::exportListPemesananSpdxKaretPertgl');
    $routes->get('pemesanan/exportPdfListPemesananSpdxKaretPertgl', 'PdfController::exportListPemesananSpdxKaretPertgl');

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

    //Retur
    $routes->get('retur', 'ReturController::returArea');
    $routes->get('retur', 'ReturController::index');
    $routes->post('retur/approve', 'ReturController::approve');
    $routes->post('retur/reject', 'ReturController::reject');
    $routes->get('retur/listBarcodeRetur', 'ReturController::listBarcodeRetur');
    $routes->get('retur/detailBarcodeRetur/(:any)', 'ReturController::detailBarcodeRetur/$1');
    $routes->get('retur/generateBarcodeRetur/(:any)', 'DomPdfController::generateBarcodeRetur/$1');
    $routes->get('retur/reportReturArea', 'ReturController::reportReturArea');
    $routes->get('retur/filterReturArea', 'ReturController::filterReturArea');
    $routes->get('retur/exportReturArea', 'ExcelController::exportReturArea');

    //Retur GBN
    $routes->get('returGbn', 'MonitoringController::retur');
    $routes->post('returGbn', 'MonitoringController::retur');

    $routes->post('outCelup/saveBon/', 'CelupController::saveBon');
    $routes->get('generate/(:num)', 'CelupController::generateBarcode/$1');
    $routes->get('printBon/(:num)', 'PdfController::printBon/$1');
    // god routes
    $routes->get('importStock', 'GodController::index');
    $routes->post('importStock/upload', 'GodController::importStock');
    $routes->get('masterWarnaBenang', 'GodController::masterWarnaBenang');
    $routes->post('importMasterWarnaBenang/upload', 'GodController::importMasterWarnaBenang');

    $routes->get('pengeluaranSementara', 'GodController::pengeluaranSementara');
    $routes->post('pengeluaranSementara/upload', 'GodController::uploadPengeluaranSementara');

    $routes->post('prosesImportPemasukan', 'GodController::prosesImportPemasukan');

    $routes->post('prosesImportPemesanan', 'GodController::prosesImportPemesanan');

    // pengaduan
    $routes->get('pengaduan', 'ApiController::getpengaduan');
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
        $routes->get('pengaduan/exportPdf/(:num)', 'ApiController::pengaduanExport/$1');
        $routes->get('statusbahanbaku', 'ApiController::statusbahanbaku');
        $routes->get('cekBahanBaku/(:any)', 'ApiController::cekBahanBaku/$1');
        $routes->get('cekStok/(:any)', 'ApiController::cekStok/$1');
        $routes->get('cekStokPerstyle/(:any)/(:any)', 'ApiController::cekStokPerstyle/$1/$2');
        $routes->get('getMU/(:any)/(:any)/(:any)', 'ApiController::getMaterialForPemesanan/$1/$2/$3');
        $routes->get('getMaterialForPPH/(:any)', 'ApiController::getMaterialForPPH/$1');
        $routes->get('getMaterialForPPHByAreaAndNoModel/(:segment)/(:segment)', 'ApiController::getMaterialForPPHByAreaAndNoModel/$1/$2');
        $routes->post('insertQtyCns', 'ApiController::insertQtyCns');
        $routes->post('saveListPemesanan', 'ApiController::saveListPemesanan');
        $routes->get('listPemesanan/(:any)', 'ApiController::listPemesanan/$1');
        $routes->get('listReportPemesanan/(:any)/(:any)', 'ApiController::listReportPemesanan/$1/$2');
        $routes->post('getUpdateListPemesanan', 'ApiController::getUpdateListPemesanan');
        $routes->post('updateListPemesanan', 'ApiController::updateListPemesanan');
        $routes->post('kirimPemesanan', 'ApiController::kirimPemesanan');
        // $routes->get('getMaterialForPPH/(:any)/(:any)', 'ApiController::getMaterialForPPH/$1/$2');
        $routes->get('stockbahanbaku/(:any)', 'ApiController::stockbahanbaku/$1');
        $routes->post('hapusOldPemesanan', 'ApiController::hapusOldPemesanan');
        $routes->get('pph', 'ApiController::pph');
        $routes->post('assignArea', 'MaterialController::assignArea');
        $routes->get('pphperhari', 'ApiController::getMU');
        $routes->get('requestAdditionalTime/(:any)', 'ApiController::requestAdditionalTime/$1');
        $routes->get('getStyleSizeByBb', 'ApiController::getStyleSizeByBb');
        $routes->get('getPengirimanArea', 'ApiController::getPengirimanArea');
        $routes->post('getGwBulk', 'ApiController::getGwBulk');
        $routes->get('getKategoriRetur', 'ApiController::getKategoriRetur');
        $routes->post('saveRetur', 'ApiController::saveRetur');
        $routes->get('getTotalPengiriman', 'ApiController::getTotalPengiriman');
        $routes->post('warehouse/search', 'WarehouseController::search');
        $routes->get('warehouse/exportExcel', 'ExcelController::excelStockMaterial');
        $routes->get('poTambahanDetail/(:any)/(:any)', 'ApiController::poTambahanDetail/$1/$2');
        $routes->post('savePoTambahan', 'ApiController::savePoTambahan');
        $routes->get('filterPoTambahan', 'ApiController::filterPoTambahan');
        $routes->get('cekMaterial/(:any)', 'ApiController::cekMaterial/$1');
        $routes->get('listRetur/(:any)', 'ApiController::listRetur/$1');
        $routes->get('filterTglPakai/(:any)', 'ApiController::filterTglPakai/$1');
        $routes->get('dataPemesananArea', 'ApiController::getDataPemesanan');
        $routes->get('getNoModelByPoTambahan', 'ApiController::getNoModelByPoTambahan');
        $routes->get('getStyleSizeByPoTambahan', 'ApiController::getStyleSizeByPoTambahan');
        $routes->get('getPcsPoTambahan', 'ApiController::getPcsPoTambahan');
        $routes->get('getMUPoTambahan', 'ApiController::getMUPoTambahan');
        $routes->get('apiexportGlobalReport/(:any)', 'ExcelController::apiexportGlobalReport/$1');
        $routes->get('getKgTambahan', 'ApiController::getKgTambahan');
        $routes->get('getPemesananByAreaModel', 'ApiController::getPemesananByAreaModel');
        $routes->get('getReturByAreaModel', 'ApiController::getReturByAreaModel');
        $routes->get('getKgPoTambahan', 'ApiController::getKgPoTambahan');
        $routes->get('getMaterialByNoModel/(:any)', 'ApiController::getMaterialByNoModel/$1');
        $routes->get('getMUForRosso/(:any)/(:any)/(:any)', 'ApiController::getMaterialForPemesananRosso/$1/$2/$3');
        $routes->get('listExportRetur/(:any)', 'ApiController::listExportRetur/$1');
        $routes->get('getGWAktual', 'ApiController::getGWAktual');
        $routes->get('saveGWAktual', 'ApiController::saveGWAktual');

        $routes->get('filterDatangBenang', 'ApiController::filterDatangBenang');
        $routes->get('filterPoBenang', 'ApiController::filterPoBenang');
        $routes->get('filterPengiriman', 'ApiController::filterPengiriman');
        $routes->get('filterReportGlobal', 'ApiController::filterReportGlobal');
        $routes->get('filterReportGlobalBenang', 'ApiController::filterReportGlobalBenang');
        $routes->get('filterReportGlobalNylon', 'ApiController::filterReportGlobalNylon');
        $routes->get('filterSisaPakai', 'ApiController::filterSisaPakai');
        $routes->get('reportSisaDatangBenang', 'ApiController::reportSisaDatangBenang');
        $routes->get('reportSisaDatangNylon', 'ApiController::reportSisaDatangNylon');
        $routes->get('reportSisaDatangSpandex', 'ApiController::reportSisaDatangSpandex');
        $routes->get('reportSisaDatangKaret', 'ApiController::reportSisaDatangKaret');
        $routes->get('filterBenangMingguan', 'ApiController::filterBenangMingguan');
        $routes->get('filterBenangBulanan', 'ApiController::filterBenangBulanan');
        $routes->get('historyPindahOrder', 'ApiController::historyPindahOrder');
        $routes->get('getMasterRangePemesanan', 'ApiController::getMasterRangePemesanan');
        $routes->get('filterReportKebutuhanBahanBaku', 'ApiController::filterReportKebutuhanBahanBaku');
        $routes->get('excelReportKebutuhanBahanBaku', 'ApiController::excelReportKebutuhanBahanBaku');
    }
);

$routes->group('/kantordepan', ['filter' => 'kantordepan'], function ($routes) {
    $routes->get('Report', 'DashboardKantorController::index');
    $routes->get('reportPo/(:any)', 'WarehouseController::reportPoBenang/$1');
    $routes->get('reportDatangBenang', 'WarehouseController::reportDatangBenang');
    $routes->get('reportDatangNylon', 'WarehouseController::reportDatangNylon');
    $routes->get('reportGlobal', 'WarehouseController::reportGlobal');
    $routes->get('reportGlobalNylon', 'WarehouseController::reportGlobalNylon');
    $routes->get('reportGlobalStockBenang', 'WarehouseController::reportGlobalStockBenang');
    $routes->get('reportPemakaianNylon', 'WarehouseController::reportPemakaianNylon');
    $routes->get('reportSisaPakaiBenang', 'WarehouseController::reportSisaPakaiBenang');
    $routes->get('reportSisaPakaiNylon', 'WarehouseController::reportSisaPakaiNylon');
    $routes->get('reportSisaPakaiSpandex', 'WarehouseController::reportSisaPakaiSpandex');
    $routes->get('reportSisaPakaiKaret', 'WarehouseController::reportSisaPakaiKaret');
    $routes->get('reportSisaDatangBenang', 'WarehouseController::reportSisaDatangBenang');
    $routes->get('reportSisaDatangNylon', 'WarehouseController::reportSisaDatangNylon');
    $routes->get('reportSisaDatangSpandex', 'WarehouseController::reportSisaDatangSpandex');
    $routes->get('reportSisaDatangKaret', 'WarehouseController::reportSisaDatangKaret');
    $routes->get('reportBenangMingguan', 'WarehouseController::reportBenangMingguan');
    $routes->get('reportBenangBulanan', 'WarehouseController::reportBenangBulanan');


    $routes->get('warehouse/filterPoBenang', 'WarehouseController::filterPoBenang');
    $routes->get('warehouse/exportPoBenang', 'ExcelController::exportPoBenang');
    $routes->get('warehouse/reportDatangBenang', 'WarehouseController::reportDatangBenang');
    $routes->get('warehouse/filterDatangBenang', 'WarehouseController::filterDatangBenang');
    $routes->get('warehouse/exportDatangBenang', 'ExcelController::exportDatangBenang');
    $routes->get('warehouse/getKeteranganDatang', 'WarehouseController::getKeteranganDatang');
    $routes->post('warehouse/updateKeteranganDatang', 'WarehouseController::updateKeteranganDatang');
    $routes->get('warehouse/exportExcel', 'ExcelController::excelStockMaterial');
    $routes->get('warehouse/reportPengiriman', 'WarehouseController::reportPengiriman');
    $routes->get('warehouse/filterPengiriman', 'WarehouseController::filterPengiriman');
    $routes->get('warehouse/reportOtherOut', 'WarehouseController::reportOtherOut');
    $routes->get('warehouse/filterOtherOut', 'WarehouseController::filterOtherOut');
    $routes->get('warehouse/exportPengiriman', 'ExcelController::exportPengiriman');
    $routes->get('warehouse/reportGlobal', 'WarehouseController::reportGlobal');
    $routes->get('warehouse/filterReportGlobal', 'WarehouseController::filterReportGlobal');
    $routes->get('warehouse/exportGlobalReport', 'ExcelController::exportGlobalReport');
    $routes->get('warehouse/reportGlobalNylon', 'WarehouseController::reportGlobalNylon');
    $routes->get('warehouse/filterReportGlobalNylon', 'WarehouseController::filterReportGlobalNylon');
    $routes->get('warehouse/reportGlobalStockBenang', 'WarehouseController::reportGlobalStockBenang');
    $routes->get('warehouse/filterReportGlobalBenang', 'WarehouseController::filterReportGlobalBenang');
    $routes->get('warehouse/exportReportGlobalBenang', 'ExcelController::exportReportGlobalBenang');
    $routes->get('warehouse/reportSisaPakaiBenang', 'WarehouseController::reportSisaPakaiBenang');
    $routes->get('warehouse/filterSisaPakaiBenang', 'WarehouseController::filterSisaPakai');
    $routes->get('warehouse/exportReportSisaPakaiBenang', 'ExcelController::exportReportSisaPakai');
    $routes->get('warehouse/reportSisaPakaiNylon', 'WarehouseController::reportSisaPakaiNylon');
    $routes->get('warehouse/filterSisaPakaiNylon', 'WarehouseController::filterSisaPakai');
    $routes->get('warehouse/exportReportSisaPakaiNylon', 'ExcelController::exportReportSisaPakai');
    $routes->get('warehouse/reportSisaPakaiSpandex', 'WarehouseController::reportSisaPakaiSpandex');
    $routes->get('warehouse/filterSisaPakaiSpandex', 'WarehouseController::filterSisaPakai');
    $routes->get('warehouse/exportReportSisaPakaiSpandex', 'ExcelController::exportReportSisaPakai');
    $routes->get('warehouse/reportSisaPakaiKaret', 'WarehouseController::reportSisaPakaiKaret');
    $routes->get('warehouse/filterSisaPakaiKaret', 'WarehouseController::filterSisaPakai');
    $routes->get('warehouse/exportReportSisaPakaiKaret', 'ExcelController::exportReportSisaPakai');
    $routes->get('warehouse/historyPindahOrder', 'WarehouseController::historyPindahOrder');
    $routes->get('warehouse/exportHistoryPindahOrder', 'ExcelController::exportHistoryPindahOrder');
    $routes->get('pemesanan/historyPinjamOrder', 'PemesananController::HistoryPinjamOrder');
    $routes->get('pemesanan/exportHistoryPinjamOrder', 'ExcelController::exportHistoryPinjamOrder');
    $routes->get('warehouse/reportSisaDatangBenang', 'WarehouseController::reportSisaDatangBenang');
    $routes->get('warehouse/exportReportSisaDatangBenang', 'ExcelController::exportReportSisaDatangBenang');
    $routes->get('warehouse/reportSisaDatangNylon', 'WarehouseController::reportSisaDatangNylon');
    $routes->get('warehouse/exportReportSisaDatangNylon', 'ExcelController::exportReportSisaDatangNylon');
    $routes->get('warehouse/reportSisaDatangSpandex', 'WarehouseController::reportSisaDatangSpandex');
    $routes->get('warehouse/exportReportSisaDatangSpandex', 'ExcelController::exportReportSisaDatangSpandex');
    $routes->get('warehouse/reportSisaDatangKaret', 'WarehouseController::reportSisaDatangKaret');
    $routes->get('warehouse/exportReportSisaDatangKaret', 'ExcelController::exportReportSisaDatangKaret');
    $routes->get('warehouse/reportBenangMingguan', 'WarehouseController::reportBenangMingguan');
    $routes->get('warehouse/filterBenangMingguan', 'WarehouseController::filterBenangMingguan');
    $routes->get('warehouse/exportReportBenangMingguan', 'ExcelController::exportReportBenang');
    $routes->get('warehouse/reportBenangBulanan', 'WarehouseController::reportBenangBulanan');
    $routes->get('warehouse/filterBenangBulanan', 'WarehouseController::filterBenangBulanan');
    $routes->get('warehouse/exportReportBenangBulanan', 'ExcelController::exportReportBenang');
    $routes->get('warehouse/listOtherBarcode', 'WarehouseController::listOtherBarcode');
    $routes->get('warehouse/detailOtherBarcode/(:any)', 'WarehouseController::detailOtherBarcode/$1');
    $routes->get('warehouse/generateOtherBarcode/(:any)', 'DomPdfController::generateOtherBarcode/$1');
    $routes->get('warehouse/reportDatangNylon', 'WarehouseController::reportDatangNylon');
    $routes->get('warehouse/filterDatangNylon', 'WarehouseController::filterDatangNylon');
    $routes->get('warehouse/exportDatangNylon', 'ExcelController::exportDatangNylon');
    $routes->get('warehouse/reportPemakaianNylon', 'WarehouseController::reportPemakaianNylon');
    $routes->get('warehouse/filterPemakaianNylon', 'WarehouseController::filterPemakaianNylon');
    $routes->get('warehouse/exportPemakaianNylon', 'ExcelController::exportPemakaianNylon');
    $routes->get('warehouse/reportIndri', 'WarehouseController::reportIndri');
    $routes->get('warehouse/filterReportIndri', 'WarehouseController::filterReportIndri');
    $routes->get('warehouse/exportReportIndri', 'ExcelController::exportReportIndri');

    $routes->get('pengaduan', 'ApiController::getpengaduan');
});
