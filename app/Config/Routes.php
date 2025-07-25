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
    $routes->get('exportOpenPOGabung', 'ExcelController::exportOpenPOGabung');
    $routes->get('exportPoBoking', 'ExcelController::exportPoBooking');


    $routes->post('getMasterData', 'MasterdataController::getMasterData');
    $routes->get('material/(:num)', 'MasterdataController::material/$1');
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
    $routes->post('exportPoNylon', 'ExcelController::generateOpenPONylon');
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

    $routes->post('komplain_pemasukan', 'WarehouseController::prosesComplain');
    //
    $routes->post('warehouse/search', 'WarehouseController::search');
    $routes->post('warehouse/sisaKapasitas', 'WarehouseController::getSisaKapasitas');
    $routes->post('warehouse/getCluster', 'WarehouseController::getClusterbyId');
    $routes->get('warehouse/getNamaCluster', 'WarehouseController::getNamaCluster');
    $routes->post('warehouse/updateCluster', 'WarehouseController::updateCluster');
    $routes->get('warehouse/getNoModel', 'WarehouseController::getNoModel');
    $routes->post('warehouse/savePindahOrder', 'WarehouseController::savePindahOrder');
    $routes->post('warehouse/getPindahOrder', 'WarehouseController::getPindahOrder');
    $routes->post('warehouse/savePindahCluster', 'WarehouseController::savePindahCluster');
    $routes->post('warehouse/getPindahCluster', 'WarehouseController::getPindahCluster');
    $routes->post('warehouse/updateNoModel', 'WarehouseController::updateNoModel');
    $routes->get('warehouse/reportPoBenang', 'WarehouseController::reportPoBenang');
    $routes->get('warehouse/filterPoBenang', 'WarehouseController::filterPoBenang');
    $routes->get('warehouse/exportPoBenang', 'ExcelController::exportPoBenang');
    $routes->get('warehouse/reportDatangBenang', 'WarehouseController::reportDatangBenang');
    $routes->get('warehouse/filterDatangBenang', 'WarehouseController::filterDatangBenang');
    $routes->get('warehouse/exportDatangBenang', 'ExcelController::exportDatangBenang');
    $routes->get('warehouse/exportExcel', 'ExcelController::excelStockMaterial');
    $routes->get('warehouse/reportPengiriman', 'WarehouseController::reportPengiriman');
    $routes->get('warehouse/filterPengiriman', 'WarehouseController::filterPengiriman');
    $routes->get('warehouse/exportPengiriman', 'ExcelController::exportPengiriman');
    $routes->get('warehouse/reportGlobal', 'WarehouseController::reportGlobal');
    $routes->get('warehouse/filterReportGlobal', 'WarehouseController::filterReportGlobal');
    $routes->get('warehouse/exportGlobalReport', 'ExcelController::exportGlobalReport');
    $routes->get('warehouse/reportGlobalStockBenang', 'WarehouseController::reportGlobalStockBenang');
    $routes->get('warehouse/filterReportGlobalBenang', 'WarehouseController::filterReportGlobalBenang');
    $routes->get('warehouse/exportReportGlobalBenang', 'ExcelController::exportReportGlobalBenang');
    $routes->get('warehouse/reportSisaPakaiBenang', 'WarehouseController::reportSisaPakaiBenang');
    $routes->get('warehouse/filterSisaPakaiBenang', 'WarehouseController::filterSisaPakaiBenang');
    $routes->get('warehouse/exportReportSisaPakaiBenang', 'ExcelController::exportReportSisaPakaiBenang');
    $routes->get('warehouse/reportSisaPakaiNylon', 'WarehouseController::reportSisaPakaiNylon');
    $routes->get('warehouse/filterSisaPakaiNylon', 'WarehouseController::filterSisaPakaiNylon');
    $routes->get('warehouse/exportReportSisaPakaiNylon', 'ExcelController::exportReportSisaPakaiNylon');
    $routes->get('warehouse/reportSisaPakaiSpandex', 'WarehouseController::reportSisaPakaiSpandex');
    $routes->get('warehouse/filterSisaPakaiSpandex', 'WarehouseController::filterSisaPakaiSpandex');
    $routes->get('warehouse/exportReportSisaPakaiSpandex', 'ExcelController::exportReportSisaPakaiSpandex');
    $routes->get('warehouse/reportSisaPakaiKaret', 'WarehouseController::reportSisaPakaiKaret');
    $routes->get('warehouse/filterSisaPakaiKaret', 'WarehouseController::filterSisaPakaiKaret');
    $routes->get('warehouse/exportReportSisaPakaiKaret', 'ExcelController::exportReportSisaPakaiKaret');
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

    //
    $routes->post('getStockByParams', 'PemesananController::getStockByParams');
    $routes->get('pemesanan', 'PemesananController::index');
    $routes->get('pemesanan/(:any)/(:any)', 'PemesananController::pemesanan/$1/$2');
    $routes->post('pemesanan/filter', 'PemesananController::filterPemesanan');
    $routes->get('pemesananperarea/(:any)', 'PemesananController::pemesananPerArea/$1');
    $routes->get('detailpemesanan/(:any)/(:any)', 'PemesananController::detailPemesanan/$1/$2');
    $routes->get('selectClusterWarehouse/(:any)', 'PemesananController::selectClusterWarehouse/$1');
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
    $routes->post('reset_pengiriman/(:any)/(:any)', 'PemesananController::resetPengirimanArea/$1/$2');
    $routes->post('hapus_pengiriman', 'PemesananController::hapusListPengiriman');
    $routes->post('proses_pengiriman', 'PemesananController::prosesPengirimanArea');
    $routes->get('pemesanan/reportPemesananArea', 'PemesananController::reportPemesananArea');
    $routes->get('pemesanan/filterPemesananArea', 'PemesananController::filterPemesananArea');
    $routes->get('pemesanan/exportPemesananArea', 'ExcelController::excelPemesananArea');
    $routes->post('pemesanan/listBarangKeluarPertgl', 'PemesananController::listBarangKeluarPertgl');
    $routes->get('pemesanan/exportListBarangKeluar', 'ExcelController::exportListBarangKeluar');
    $routes->post('pemesanan/listPemesananSpandexKaretPertgl', 'PemesananController::listPemesananSpandexKaretPertgl');
    $routes->get('pemesanan/exportListPemesananSpdxKaretPertgl', 'ExcelController::exportListPemesananSpdxKaretPertgl');
    $routes->get('pemesanan/sisaKebutuhanArea', 'PemesananController::sisaKebutuhanArea');
    // $routes->get('pemesanan/sisaKebutuhanArea_filter', 'PemesananController::sisaKebutuhanArea');

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
    //Retur
    $routes->get('retur', 'ReturController::index');
    $routes->post('retur/approve', 'ReturController::approve');
    $routes->post('retur/reject', 'ReturController::reject');
    $routes->get('retur/listBarcodeRetur', 'ReturController::listBarcodeRetur');
    $routes->get('retur/detailBarcodeRetur/(:any)', 'ReturController::detailBarcodeRetur/$1');
    $routes->get('retur/generateBarcodeRetur/(:any)', 'PdfController::generateBarcodeRetur/$1');
    $routes->get('retur/reportReturArea', 'ReturController::reportReturArea');
    $routes->get('retur/filterReturArea', 'ReturController::filterReturArea');
    $routes->get('retur/exportReturArea', 'ExcelController::exportReturArea');

    //Po Plus
    $routes->get('poplus', 'PoTambahanController::index');
    $routes->get('poplus/detail', 'PoTambahanController::detailPoPlus');
    $routes->post('approvePoPlusArea', 'PoTambahanController::prosesApprovePoPlusArea');
    $routes->post('rejectPoPlusArea', 'PoTambahanController::prosesRejectPoPlusArea');
    $routes->get('poplus/reportPoTambahan', 'PoTambahanController::reportPoTambahan');
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
    $routes->get('filterstatusbahanbaku/(:any)', 'ScheduleController::filterstatusbahanbaku/$1');
});

// celup routes
$routes->group('/celup', ['filter' => 'celup'], function ($routes) {
    $routes->get('', 'DashboardCelupController::index');
    $routes->get('getStackedChartData', 'DashboardCelupController::getStackedChartData');
    // $routes->get('schedule', 'ScheduleController::index');
    $routes->get('schedule/acrylic', 'ScheduleController::acrylic');
    $routes->get('schedule/nylon', 'ScheduleController::nylon');
    $routes->get('reqschedule', 'ScheduleController::reqschedule');
    $routes->post('schedule', 'ScheduleController::reqschedule');
    $routes->get('schedule', 'ScheduleController::index');
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
    // $routes->get('printBon/(:num)', 'DomPdfController::printBon/$1');
    $routes->get('printBarcode/(:num)', 'DomPdfController::printBarcode/$1');
    $routes->get('printBon/(:num)', 'PdfController::printBon/$1');
    $routes->get('generate/(:num)', 'CelupController::generateBarcode/$1');
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
    $routes->get('deleteDuplicate/mu/(:any)', 'MasterdataController::deleteDuplicateMu/$1');

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
    $routes->post('warehouse/getNoModel/(:any)', 'WarehouseController::getNoModel/$1');
    $routes->post('warehouse/updateNoModel', 'WarehouseController::updateNoModel');
    $routes->get('warehouse/reportPoBenang', 'WarehouseController::reportPoBenang');
    $routes->get('warehouse/filterPoBenang', 'WarehouseController::filterPoBenang');
    $routes->get('warehouse/exportPoBenang', 'ExcelController::exportPoBenang');
    $routes->get('warehouse/reportDatangBenang', 'WarehouseController::reportDatangBenang');
    $routes->get('warehouse/filterDatangBenang', 'WarehouseController::filterDatangBenang');
    $routes->get('warehouse/exportDatangBenang', 'ExcelController::exportDatangBenang');
    $routes->get('warehouse/exportExcel', 'ExcelController::excelStockMaterial');
    $routes->get('warehouse/reportPengiriman', 'WarehouseController::reportPengiriman');
    $routes->get('warehouse/filterPengiriman', 'WarehouseController::filterPengiriman');
    $routes->get('warehouse/exportPengiriman', 'ExcelController::exportPengiriman');
    $routes->get('warehouse/reportGlobal', 'WarehouseController::reportGlobal');
    $routes->get('warehouse/filterReportGlobal', 'WarehouseController::filterReportGlobal');
    $routes->get('warehouse/exportGlobalReport', 'ExcelController::exportGlobalReport');
    $routes->get('warehouse/reportGlobalStockBenang', 'WarehouseController::reportGlobalStockBenang');
    $routes->get('warehouse/filterReportGlobalBenang', 'WarehouseController::filterReportGlobalBenang');
    $routes->get('warehouse/exportReportGlobalBenang', 'ExcelController::exportReportGlobalBenang');
    $routes->get('warehouse/reportSisaPakaiBenang', 'WarehouseController::reportSisaPakaiBenang');
    $routes->get('warehouse/filterSisaPakaiBenang', 'WarehouseController::filterSisaPakaiBenang');
    $routes->get('warehouse/exportReportSisaPakaiBenang', 'ExcelController::exportReportSisaPakaiBenang');
    $routes->get('warehouse/reportSisaPakaiNylon', 'WarehouseController::reportSisaPakaiNylon');
    $routes->get('warehouse/filterSisaPakaiNylon', 'WarehouseController::filterSisaPakaiNylon');
    $routes->get('warehouse/exportReportSisaPakaiNylon', 'ExcelController::exportReportSisaPakaiNylon');
    $routes->get('warehouse/reportSisaPakaiSpandex', 'WarehouseController::reportSisaPakaiSpandex');
    $routes->get('warehouse/filterSisaPakaiSpandex', 'WarehouseController::filterSisaPakaiSpandex');
    $routes->get('warehouse/exportReportSisaPakaiSpandex', 'ExcelController::exportReportSisaPakaiSpandex');
    $routes->get('warehouse/reportSisaPakaiKaret', 'WarehouseController::reportSisaPakaiKaret');
    $routes->get('warehouse/filterSisaPakaiKaret', 'WarehouseController::filterSisaPakaiKaret');
    $routes->get('warehouse/exportReportSisaPakaiKaret', 'ExcelController::exportReportSisaPakaiKaret');

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

    //Po Plus
    $routes->get('poplus', 'MasterdataController::poPlus');
    $routes->get('poplus/detail', 'MasterdataController::detailPoPlus');
    $routes->post('approvePoPlusArea', 'MasterdataController::prosesApprovePoPlusArea');

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

    $routes->get('pemesanan', 'PemesananController::pemesananArea');
    $routes->get('filter_pemesananarea', 'PemesananController::pemesananArea');
    $routes->post('getUpdateListPemesanan', 'PemesananController::getUpdateListPemesanan');
    $routes->post('updateListPemesanan', 'ApiController::updatePemesananArea');
    $routes->get('pemesanan/reportPemesananArea', 'PemesananController::reportPemesananArea');
    $routes->get('pemesanan/filterPemesananArea', 'PemesananController::filterPemesananArea');
    $routes->get('pemesanan/exportPemesananArea', 'ExcelController::excelPemesananArea');

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

    // retur
    $routes->get('retur', 'ReturController::returArea');


    $routes->post('outCelup/saveBon/', 'CelupController::saveBon');
    $routes->get('generate/(:num)', 'CelupController::generateBarcode/$1');
    $routes->get('printBon/(:num)', 'PdfController::printBon/$1');
    // god routes
    $routes->get('importStock', 'GodController::index');
    $routes->post('importStock/upload', 'GodController::importStock');
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
        $routes->get('cekStokPerstyle/(:any)/(:any)', 'ApiController::cekStokPerstyle/$1/$2');
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
    }
);
