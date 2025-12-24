<?php

namespace App\Services;

use Config\Services;
use App\Models\MaterialModel;

class QtyPcsService
{
    protected $materialModel;

    public function __construct()
    {
        $this->materialModel = new MaterialModel();
    }

    public function getQtyPcs(array $filteredData, $jenis = null)
    {
        $client = Services::curlrequest();

        $noModel = array_unique(array_column($filteredData, 'no_model'));

        if (empty($noModel)) {
            return [];
        }

        $getQtyUrl = api_url('capacity') . 'getQtyOrderByNoModel';

        $qtyResponse = $client->post($getQtyUrl, [
            'json' => [
                'models' => $noModel
            ],
            'http_errors' => false
        ]);

        $qtyData = json_decode($qtyResponse->getBody(), true) ?? [];

        $sumQtyBySize = [];

        foreach ($qtyData as $row) {
            $mastermodel = $row['mastermodel'];
            $size = $row['size'];
            $qty  = (int) $row['qty'];

            if (!isset($sumQtyBySize[$mastermodel][$size])) {
                $sumQtyBySize[$mastermodel][$size] = 0;
            }

            $sumQtyBySize[$mastermodel][$size] += $qty;
        }

        $map = [];

        foreach ($filteredData as $data) {
            $key = $data['no_model']
                . '|' . $data['item_type']
                . '|' . $data['kode_warna']
                . '|' . $data['color'];

            $getStyle = $this->materialModel->getStyleSizeByBb(
                $data['no_model'],
                $data['item_type'],
                $data['kode_warna'],
                $data['color']
            );

            $ttlKeb = 0;
            $ttlQty = 0;

            foreach ($getStyle as $dataStyle) {
                $styleSize = $dataStyle['style_size'];
                $qty = $sumQtyBySize[$data['no_model']][$styleSize] ?? 0;

                if ($qty <= 0) {
                    continue;
                }

                if (
                    isset($dataStyle['item_type'])
                    && stripos($dataStyle['item_type'], 'JHT') !== false
                ) {
                    $kebutuhan = $dataStyle['kgs'] ?? 0;
                } else {
                    $kebutuhan = (
                        ($qty * $dataStyle['gw'] * $dataStyle['composition'] / 100 / 1000)
                        * (1 + ($dataStyle['loss'] / 100))
                    );
                }

                $ttlKeb += $kebutuhan;
                $ttlQty += $qty;
            }

            $map[$key] = [
                'kg_po'          => round($ttlKeb, 2),
                'qty_po'         => $ttlQty,
                'from_capacity' => !empty($qtyData),
            ];
        }

        return $map;
    }
}
