<?php
// =====================================================================
// FUNGSI INTI METODE WEIGHTED PRODUCT (WP)
// Diterapkan PERSIS sesuai algoritma BAB III dokumen penelitian.
// =====================================================================
//
// Tahapan (sesuai bagan algoritma Gambar 3.2):
//  1. Input Data (Alternatif, Kriteria, Bobot, Nilai Alternatif X)
//  2. Menentukan Alternatif & Kriteria
//  3. Menentukan Bobot Kriteria
//  4. Menentukan Jenis Kriteria (Benefit / Cost)
//  5. Membentuk Matriks Keputusan X = [Xij]
//  6. Normalisasi Bobot          : Wj = wj / Σwj            (rumus 3.7)
//  7. Menghitung Nilai Vektor S  : Si = Π (Xij ^ Wj)        (rumus 2.2)
//        - Kriteria BENEFIT  : pangkat positif  (+Wj)
//        - Kriteria COST     : pangkat negatif  (-Wj)
//  8. Menghitung Nilai Vektor V  : Vi = Si / Σ Si           (rumus 3.8)
//  9. Perangkingan (terbesar -> terkecil)
// 10. Output: rekomendasi menu kopi favorit
// =====================================================================

/**
 * Menjalankan perhitungan Weighted Product.
 *
 * @param array $kriteria   Array kriteria: tiap elemen [kode, nama, bobot, atribut]
 * @param array $alternatif Array alternatif: tiap elemen punya id, kode, nama_menu, nama_pembeli,
 *                          waktu_pembelian, dan c1..c8 (nilai per kriteria, urut sesuai $kriteria)
 * @return array Hasil lengkap setiap tahap.
 */
function hitung_weighted_product($kriteria, $alternatif)
{
    // ----- Tahap 6: Normalisasi Bobot (Wj = wj / Σwj) -----
    $total_bobot = 0.0;
    foreach ($kriteria as $k) {
        $total_bobot += (float)$k['bobot'];
    }

    $bobot_normal = [];   // index by kode kriteria
    foreach ($kriteria as $k) {
        $wj = $total_bobot > 0 ? ((float)$k['bobot'] / $total_bobot) : 0.0;
        $bobot_normal[$k['kode']] = $wj;
    }

    // ----- Tahap 7: Hitung Vektor S -----
    // Si = Π ( Xij ^ (±Wj) ) ; cost = pangkat negatif, benefit = positif
    $vektor_s = [];
    $detail_s = [];   // rincian per alternatif per kriteria (untuk ditampilkan)
    foreach ($alternatif as $a) {
        $s = 1.0;
        $rincian = [];
        foreach ($kriteria as $idx => $k) {
            $kode  = $k['kode'];
            $field = 'c' . ($idx + 1);                  // c1..c8 sesuai urutan kriteria
            $xij   = (float)$a[$field];
            $wj    = $bobot_normal[$kode];
            // Cost -> pangkat negatif, Benefit -> pangkat positif
            $pangkat = ($k['atribut'] === 'cost') ? (-1 * $wj) : $wj;
            $faktor  = pow($xij, $pangkat);
            $s *= $faktor;
            $rincian[$kode] = [
                'x'       => $xij,
                'wj'      => $wj,
                'pangkat' => $pangkat,
                'faktor'  => $faktor,
            ];
        }
        $vektor_s[$a['id']] = $s;
        $detail_s[$a['id']] = $rincian;
    }

    // ----- Tahap 8: Hitung Vektor V (Vi = Si / Σ Si) -----
    $total_s = array_sum($vektor_s);
    $vektor_v = [];
    foreach ($vektor_s as $id => $s) {
        $vektor_v[$id] = $total_s > 0 ? ($s / $total_s) : 0.0;
    }

    // ----- Tahap 9: Susun hasil + Perangkingan -----
    $hasil = [];
    foreach ($alternatif as $a) {
        $id = $a['id'];
        $hasil[] = [
            'id'              => $id,
            'kode'            => $a['kode'],
            'nama_menu'       => $a['nama_menu'],
            'nama_pembeli'    => $a['nama_pembeli'],
            'waktu_pembelian' => $a['waktu_pembelian'],
            'nilai'           => $a,
            'detail'          => $detail_s[$id],
            's'               => $vektor_s[$id],
            'v'               => $vektor_v[$id],
        ];
    }

    // Urutkan berdasarkan nilai V terbesar -> terkecil
    usort($hasil, function ($p, $q) {
        if ($q['v'] == $p['v']) return 0;
        return ($q['v'] < $p['v']) ? -1 : 1;
    });

    // Beri nomor ranking
    $rank = 1;
    foreach ($hasil as &$h) {
        $h['ranking'] = $rank++;
    }
    unset($h);

    return [
        'total_bobot'   => $total_bobot,
        'bobot_normal'  => $bobot_normal,
        'total_s'       => $total_s,
        'hasil'         => $hasil,
    ];
}
