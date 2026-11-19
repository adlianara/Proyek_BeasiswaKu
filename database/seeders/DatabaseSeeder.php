<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Scholarship;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $samples = [
            ['name'=>'Bakti BCA','provider'=>'PT Bank Central Asia Tbk (BCA)','level'=>'S1','deadline'=>now()->addDays(30),'summary'=>'Program beasiswa dari BCA yang mendukung pengembangan pendidikan dan potensi generasi muda agar mampu meraih masa depan yang lebih baik.','description'=>'Program beasiswa bagi mahasiswa berprestasi untuk mendukung keberlanjutan studi, pengembangan diri, dan peningkatan kompetensi akademik maupun non-akademik.','requirements'=>['Mahasiswa aktif minimal semester 3.','Memiliki IPK minimal 3,25 (skala 4,00).','Aktif dalam kegiatan organisasi atau kegiatan sosial.','Tidak sedang menerima beasiswa dari pihak lain.'],'benefits'=>['Bantuan biaya pendidikan selama masa studi.','Program pengembangan diri dan mentoring.'],'application_url'=>'https://www.bca.co.id/','is_active'=>true],
            ['name'=>'JAPFA Untuk Anak Negeri','provider'=>'PT Japfa Comfeed Indonesia Tbk','level'=>'S1','deadline'=>now()->addDays(35),'summary'=>'Mendukung akses pendidikan dan pengembangan bakat generasi muda Indonesia melalui bantuan pendidikan dan pembinaan.','description'=>'Program pendidikan dan pembinaan untuk talenta muda Indonesia.','requirements'=>['Mahasiswa aktif.','Memenuhi kriteria akademik penyelenggara.'],'benefits'=>['Bantuan pendidikan.','Pembinaan dan pengembangan bakat.'],'application_url'=>'https://www.japfacomfeed.co.id/','is_active'=>true],
            ['name'=>'BSI Scholarship Inspirasi','provider'=>'Bank Syariah Indonesia','level'=>'S1','deadline'=>now()->addDays(45),'summary'=>'Memberikan dukungan pendidikan dan pembinaan bagi mahasiswa dari keluarga prasejahtera untuk mengembangkan potensi akademik dan kepemimpinan.','description'=>'Beasiswa pendidikan dan pengembangan kepemimpinan mahasiswa.','requirements'=>['Mahasiswa aktif perguruan tinggi.','Memiliki motivasi dan komitmen belajar.'],'benefits'=>['Bantuan pendidikan.','Program kepemimpinan dan mentoring.'],'application_url'=>'https://www.bankbsi.co.id/','is_active'=>true],
            ['name'=>'Beasiswa Unggulan','provider'=>'Pusat Layanan Pembiayaan Pendidikan (Puslapdik)','level'=>'S1','deadline'=>now()->addDays(55),'summary'=>'Program bantuan pendidikan pemerintah bagi masyarakat berprestasi untuk melanjutkan pendidikan tinggi.','description'=>'Dukungan pemerintah untuk membantu mahasiswa Indonesia melanjutkan pendidikan tinggi.','requirements'=>['Prestasi akademik atau non-akademik.','Melengkapi dokumen pendaftaran.'],'benefits'=>['Bantuan biaya pendidikan.'],'application_url'=>'https://beasiswaunggulan.kemdikbud.go.id/','is_active'=>true],
            ['name'=>'EON JW Scholarship','provider'=>'Yayasan Eon Peduli Bersama','level'=>'S1','deadline'=>now()->addDays(64),'summary'=>'Mendukung lulusan SMA/SMK berprestasi yang memiliki keterbatasan finansial untuk melanjutkan pendidikan ke perguruan tinggi.','description'=>'Program dukungan pendidikan bagi calon mahasiswa berprestasi.','requirements'=>['Lulusan SMA atau SMK.','Melengkapi dokumen sesuai panduan.'],'benefits'=>['Dukungan biaya studi.'],'application_url'=>null,'is_active'=>true],
            ['name'=>'Djarum Beasiswa Plus','provider'=>'Djarum Foundation','level'=>'S1','deadline'=>now()->addDays(75),'summary'=>'Memberikan bantuan pendidikan kepada mahasiswa berprestasi disertai pembinaan soft skill, kepemimpinan, dan pengembangan diri.','description'=>'Program beasiswa dan pembinaan karakter bagi mahasiswa Indonesia.','requirements'=>['Mahasiswa S1 semester 4.','IPK sesuai syarat program.'],'benefits'=>['Bantuan dana beasiswa.','Pelatihan soft skill.'],'application_url'=>'https://djarumbeasiswaplus.org/','is_active'=>true],
        ];
        foreach ($samples as $data) { $data['slug'] = \Illuminate\Support\Str::slug($data['name']); Scholarship::updateOrCreate(['slug'=>$data['slug']], $data); }

        if (env('ADMIN_EMAIL') && env('ADMIN_PASSWORD')) {
            User::updateOrCreate(['email' => env('ADMIN_EMAIL')], ['name' => env('ADMIN_NAME', 'Administrator'), 'password' => env('ADMIN_PASSWORD'), 'is_admin' => true]);
        }
    }
}
