@extends('layouts.admin')

@section('title', 'Panel Pengelolaan · BeasiswaKu')

@section('content')
<div class="admin-layout">
    <aside class="admin-sidebar">
        <a class="brand" href="{{ route('admin.dashboard') }}" aria-label="Beranda panel admin">
            <span class="brand-mark">♧</span>
            <span>BeasiswaKu<small>Admin Panel</small></span>
        </a>
        <b>MENU UTAMA</b>
        <a class="admin-active" href="{{ route('admin.dashboard') }}">▦ &nbsp;Beranda</a>
        <button class="logout" type="button" data-open-logout>⇥ &nbsp;Logout</button>
    </aside>

    <section class="admin-content">
        <div class="admin-top">
            <span>⌂ / Beranda</span>
            <b>Selamat datang, Admin!</b>
            <span>{{ auth()->user()->name }}</span>
        </div>

        <section class="hero admin-hero">
            <div>
                <h1>Selamat Datang di Panel Pengelolaan BeasiswaKu</h1>
                <p>Pantau status direktori beasiswa dan batas waktu pendaftaran aktif hari ini secara terpadu.</p>
            </div>
            <button class="button" type="button" data-open-scholarship data-mode="create">＋ Tambah Beasiswa Baru</button>
        </section>

        <div class="stats">
            @foreach([['KATALOG KESELURUHAN','Total Beasiswa Terdaftar',$stats['total'],'Program'],['STATUS PEMBUKAAN','Beasiswa Aktif Buka',$stats['active'],'Program'],['SEGMENTASI SASARAN','Distribusi Jenjang',$stats['levels'],'Kategori Tingkat'],['PRIORITAS WAKTU','Mendekati Batas Akhir',$stats['soon'],'Program']] as $stat)
                <div class="stat"><small>{{ $stat[0] }}</small><span>{{ $stat[1] }}</span><b>{{ $stat[2] }} <small>{{ $stat[3] }}</small></b></div>
            @endforeach
        </div>

        <form class="admin-tools" method="get" action="{{ route('admin.dashboard') }}">
            <input name="q" value="{{ request('q') }}" placeholder="Cari beasiswa atau penyelenggara...">
            <button class="button" type="submit">Cari</button>
        </form>
        @if(session('success'))<div class="flash admin-flash" role="status">{{ session('success') }}</div>@endif

        <div class="admin-table-wrap">
            <table>
                <thead><tr><th>No</th><th>Nama Beasiswa</th><th>Penyelenggara</th><th>Jenjang</th><th>Batas Pendaftaran</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($scholarships as $i => $scholarship)
                        <tr>
                            <td>{{ $scholarships->firstItem()+$i }}</td>
                            <td><b>{{ $scholarship->name }}</b></td>
                            <td>{{ $scholarship->provider }}</td>
                            <td>{{ $scholarship->level }}</td>
                            <td>{{ $scholarship->deadline->translatedFormat('d F Y') }}</td>
                            <td>{{ $scholarship->is_active ? 'Aktif' : 'Arsip' }}</td>
                            <td class="actions">
                                <button class="text-action" type="button" data-open-scholarship data-mode="edit"
                                    data-url="{{ route('admin.scholarships.update', $scholarship) }}"
                                    data-scholarship="{{ base64_encode(json_encode($scholarship->only(['name','provider','level','deadline','summary','description','requirements','benefits','application_url','is_active']))) }}">Ubah</button>
                                <button class="text-action danger-action" type="button" data-open-delete
                                    data-url="{{ route('admin.scholarships.destroy', $scholarship) }}"
                                    data-name="{{ $scholarship->name }}">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty-cell">Data beasiswa belum tersedia.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $scholarships->links() }}
    </section>
</div>

<dialog class="admin-modal" id="scholarship-modal" aria-labelledby="scholarship-modal-title">
    <form class="edit-card modal-card" id="scholarship-form" method="post">
        @csrf
        <input type="hidden" name="_method" id="scholarship-method" value="POST" disabled>
        <button class="modal-close" type="button" data-close-modal aria-label="Tutup">×</button>
        <h1 id="scholarship-modal-title">Tambah Beasiswa Baru</h1>
        <p>Isi informasi berikut dengan data resmi program beasiswa.</p>
        <div class="modal-error" role="alert" hidden></div>
        <label>Nama beasiswa<input class="field" name="name" required maxlength="180"></label>
        <label>Penyelenggara<input class="field" name="provider" required maxlength="180"></label>
        <div class="two-fields">
            <label>Jenjang<select class="field" name="level" required>@foreach(['D3','D4','S1','S2','S3'] as $level)<option value="{{ $level }}">{{ $level }}</option>@endforeach</select></label>
            <label>Batas pendaftaran<input class="field" type="date" name="deadline" required></label>
        </div>
        <label>Ringkasan<textarea class="field" name="summary" required maxlength="500"></textarea></label>
        <label>Deskripsi<textarea class="field" name="description" rows="4" required></textarea></label>
        <label>Persyaratan (satu per baris)<textarea class="field" name="requirements_text" rows="4"></textarea></label>
        <label>Manfaat (satu per baris)<textarea class="field" name="benefits_text" rows="4"></textarea></label>
        <label>URL pendaftaran<input class="field" type="url" name="application_url" maxlength="2048"></label>
        <label class="checkline"><input type="checkbox" name="is_active" value="1" checked> Tampilkan sebagai beasiswa aktif</label>
        <div class="modal-actions"><button class="button secondary-button" type="button" data-close-modal>Batal</button><button class="button" type="submit">Simpan Beasiswa</button></div>
    </form>
</dialog>

<dialog class="admin-modal confirm-modal" id="delete-modal" aria-labelledby="delete-modal-title">
    <div class="confirm-card">
        <button class="modal-close" type="button" data-close-modal aria-label="Tutup">×</button>
        <h2 id="delete-modal-title">Hapus beasiswa?</h2>
        <p>Data <strong data-delete-name></strong> akan dihapus dari katalog.</p>
        <div class="modal-error" role="alert" hidden></div>
        <div class="modal-actions"><button class="button secondary-button" type="button" data-close-modal>Batal</button><button class="button danger-button" type="button" data-confirm-delete>Hapus Beasiswa</button></div>
    </div>
</dialog>

<dialog class="admin-modal confirm-modal" id="logout-modal" aria-labelledby="logout-modal-title">
    <div class="confirm-card">
        <button class="modal-close" type="button" data-close-modal aria-label="Tutup">×</button>
        <h2 id="logout-modal-title">Keluar dari panel?</h2>
        <p>Sesi administrator akan diakhiri.</p>
        <div class="modal-error" role="alert" hidden></div>
        <div class="modal-actions"><button class="button secondary-button" type="button" data-close-modal>Batal</button><button class="button" type="button" data-confirm-logout>Logout</button></div>
    </div>
</dialog>
@endsection
