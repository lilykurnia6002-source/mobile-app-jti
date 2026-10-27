<?php

/*
|--------------------------------------------------------------------------
| Pesan Validasi Bahasa Indonesia
|--------------------------------------------------------------------------
|
| Dipakai agar `data.errors` pada respons 422 berbunyi seperti contoh di
| konsep_api.md ("Kolom tanggal wajib diisi.").
|
*/

return [

    'required' => 'Kolom :attribute wajib diisi.',
    'filled' => 'Kolom :attribute wajib memiliki nilai.',
    'present' => 'Kolom :attribute harus ada.',
    'string' => 'Kolom :attribute harus berupa teks.',
    'integer' => 'Kolom :attribute harus berupa bilangan bulat.',
    'numeric' => 'Kolom :attribute harus berupa angka.',
    'boolean' => 'Kolom :attribute harus bernilai ya atau tidak.',
    'array' => 'Kolom :attribute harus berupa daftar.',
    'date' => 'Kolom :attribute bukan tanggal yang valid.',
    'date_format' => 'Kolom :attribute tidak sesuai format :format.',
    'email' => 'Kolom :attribute harus berupa alamat email yang valid.',
    'url' => 'Kolom :attribute harus berupa URL yang valid.',
    'image' => 'Kolom :attribute harus berupa gambar.',
    'file' => 'Kolom :attribute harus berupa berkas.',
    'mimes' => 'Kolom :attribute harus berupa berkas bertipe: :values.',
    'max' => [
        'array' => 'Kolom :attribute tidak boleh lebih dari :max item.',
        'file' => 'Kolom :attribute tidak boleh lebih besar dari :max kilobyte.',
        'numeric' => 'Kolom :attribute tidak boleh lebih besar dari :max.',
        'string' => 'Kolom :attribute tidak boleh lebih dari :max karakter.',
    ],
    'min' => [
        'array' => 'Kolom :attribute harus memiliki minimal :min item.',
        'file' => 'Kolom :attribute harus berukuran minimal :min kilobyte.',
        'numeric' => 'Kolom :attribute harus minimal :min.',
        'string' => 'Kolom :attribute harus minimal :min karakter.',
    ],
    'between' => [
        'array' => 'Kolom :attribute harus memiliki antara :min sampai :max item.',
        'file' => 'Kolom :attribute harus berukuran antara :min sampai :max kilobyte.',
        'numeric' => 'Kolom :attribute harus bernilai antara :min sampai :max.',
        'string' => 'Kolom :attribute harus terdiri dari :min sampai :max karakter.',
    ],
    'size' => [
        'array' => 'Kolom :attribute harus berisi :size item.',
        'file' => 'Kolom :attribute harus berukuran :size kilobyte.',
        'numeric' => 'Kolom :attribute harus bernilai :size.',
        'string' => 'Kolom :attribute harus terdiri dari :size karakter.',
    ],
    'in' => 'Kolom :attribute yang dipilih tidak valid.',
    'not_in' => 'Kolom :attribute yang dipilih tidak valid.',
    'regex' => 'Format kolom :attribute tidak valid.',
    'unique' => 'Kolom :attribute sudah terdaftar.',
    'exists' => 'Kolom :attribute yang dipilih tidak ditemukan.',
    'confirmed' => 'Konfirmasi kolom :attribute tidak cocok.',
    'same' => 'Kolom :attribute dan :other harus sama.',
    'different' => 'Kolom :attribute dan :other harus berbeda.',
    'digits' => 'Kolom :attribute harus terdiri dari :digits angka.',
    'digits_between' => 'Kolom :attribute harus terdiri dari :min sampai :max angka.',
    'alpha' => 'Kolom :attribute hanya boleh berisi huruf.',
    'alpha_dash' => 'Kolom :attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'alpha_num' => 'Kolom :attribute hanya boleh berisi huruf dan angka.',
    'after' => 'Kolom :attribute harus berisi tanggal setelah :date.',
    'after_or_equal' => 'Kolom :attribute harus berisi tanggal setelah atau sama dengan :date.',
    'before' => 'Kolom :attribute harus berisi tanggal sebelum :date.',
    'before_or_equal' => 'Kolom :attribute harus berisi tanggal sebelum atau sama dengan :date.',
    'gte' => [
        'array' => 'Kolom :attribute harus memiliki minimal :value item.',
        'file' => 'Kolom :attribute harus berukuran minimal :value kilobyte.',
        'numeric' => 'Kolom :attribute harus lebih besar atau sama dengan :value.',
        'string' => 'Kolom :attribute harus minimal :value karakter.',
    ],
    'lte' => [
        'array' => 'Kolom :attribute tidak boleh lebih dari :value item.',
        'file' => 'Kolom :attribute tidak boleh lebih besar dari :value kilobyte.',
        'numeric' => 'Kolom :attribute tidak boleh lebih besar dari :value.',
        'string' => 'Kolom :attribute tidak boleh lebih dari :value karakter.',
    ],
    'gt' => [
        'array' => 'Kolom :attribute harus memiliki lebih dari :value item.',
        'file' => 'Kolom :attribute harus berukuran lebih dari :value kilobyte.',
        'numeric' => 'Kolom :attribute harus lebih besar dari :value.',
        'string' => 'Kolom :attribute harus lebih dari :value karakter.',
    ],
    'lt' => [
        'array' => 'Kolom :attribute harus memiliki kurang dari :value item.',
        'file' => 'Kolom :attribute harus berukuran kurang dari :value kilobyte.',
        'numeric' => 'Kolom :attribute harus kurang dari :value.',
        'string' => 'Kolom :attribute harus kurang dari :value karakter.',
    ],

    'attributes' => [
        'nama' => 'nama',
        'name' => 'nama',
        'email' => 'email',
        'password' => 'password',
        'no_hp' => 'nomor HP',
        'alamat' => 'alamat',
        'plat_nomor' => 'plat nomor',
        'merk' => 'merk',
        'tipe' => 'tipe',
        'tahun' => 'tahun',
        'tanggal' => 'tanggal',
        'jam_mulai' => 'jam mulai',
        'jam_selesai' => 'jam selesai',
        'kapasitas' => 'kapasitas',
        'status' => 'status',
        'id_mobil' => 'mobil',
        'id_slot' => 'slot',
        'id_layanan' => 'layanan',
        'keluhan' => 'keluhan',
        'detail_layanan' => 'rincian layanan',
        'jumlah' => 'jumlah',
        'per_page' => 'jumlah per halaman',
    ],

];
