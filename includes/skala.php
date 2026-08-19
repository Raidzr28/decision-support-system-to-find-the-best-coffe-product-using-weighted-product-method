<?php
// Referensi skala penilaian 1-5 tiap kriteria (sesuai Tabel 3.2 - 3.9 BAB III)
// Dipakai untuk dropdown input nilai alternatif & tooltip.
$SKALA_KRITERIA = [
    'C1' => ['nama'=>'Cita Rasa','tipe'=>'Benefit', 1=>'Tidak Enak',2=>'Kurang Enak',3=>'Cukup Enak',4=>'Enak',5=>'Sangat Enak'],
    'C2' => ['nama'=>'Aroma','tipe'=>'Benefit', 1=>'Tidak Harum',2=>'Kurang Harum',3=>'Cukup Harum',4=>'Harum',5=>'Sangat Harum'],
    'C3' => ['nama'=>'Harga','tipe'=>'Cost', 1=>'Diatas Rp 50.000',2=>'Rp 40.001 - 50.000',3=>'Rp 30.001 - 40.000',4=>'Rp 20.001 - 30.000',5=>'Dibawah Rp 20.000'],
    'C4' => ['nama'=>'Popularitas','tipe'=>'Benefit', 1=>'≤25 porsi/bln',2=>'26-50 porsi/bln',3=>'51-75 porsi/bln',4=>'76-100 porsi/bln',5=>'≥100 porsi/bln'],
    'C5' => ['nama'=>'Ketersediaan Bahan Baku','tipe'=>'Benefit', 1=>'Sangat Sulit',2=>'Sulit',3=>'Cukup Mudah',4=>'Mudah',5=>'Sangat Mudah'],
    'C6' => ['nama'=>'Waktu Penyajian','tipe'=>'Cost', 1=>'>15 menit',2=>'11-15 menit',3=>'8-10 menit',4=>'4-7 menit',5=>'≤3 menit'],
    'C7' => ['nama'=>'Nilai Gizi','tipe'=>'Benefit', 1=>'Sangat Rendah',2=>'Rendah',3=>'Cukup',4=>'Tinggi',5=>'Sangat Tinggi'],
    'C8' => ['nama'=>'Kepuasan Pelanggan','tipe'=>'Benefit', 1=>'Rating <1,5',2=>'Rating 1,5-2,4',3=>'Rating 2,5-3,4',4=>'Rating 3,5-4,4',5=>'Rating 4,5-5,0'],
];
