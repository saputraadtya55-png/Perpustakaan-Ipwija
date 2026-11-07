<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Perpustakaan - Universitas IPWIJA</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fa-solid fa-book-open me-2"></i>Perpustakaan IPWIJA
            </a>
            <div class="d-flex align-items-center">
                <span class="text-white me-3">
                    <i class="fa-solid fa-user-circle me-1"></i> {{ Auth::user()->name ?? 'Admin' }}
                </span>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-5 flex-grow-1">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="m-0 font-weight-bold text-primary">
                    <i class="fa-solid fa-list me-2"></i>Manajemen Data Buku
                </h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahBuku">
                    <i class="fa-solid fa-plus me-1"></i> Tambah Buku Baru
                </button>
            </div>
            
            <div class="card-body">
                <!-- Search Bar (Elasticsearch) -->
                <form action="{{ route('dashboard') }}" method="GET" class="row g-2 mb-4">
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Cari judul buku via Elasticsearch..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-secondary">Cari</button>
                    </div>
                </form>

                <!-- Status Notifikasi / Alert -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Tabel Data Buku -->
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle border">
                        <thead class="table-primary">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Judul Buku</th>
                                <th>Pengarang</th>
                                <th>Tahun Terbit</th>
                                <th class="text-center" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($buku ?? [] as $item)
                                {{-- PENGAMAN: Melewati iterasi jika data tidak sengaja ber-tipe string --}}
                                @if(is_string($item))
                                    @continue
                                @endif

                                @php
                                    // Ekstraksi data baik berupa Object Eloquent maupun Array
                                    $id = is_object($item) ? $item->id : ($item['id'] ?? '');
                                    $judul = is_object($item) ? $item->judul_buku : ($item['judul_buku'] ?? '');
                                    $pengarang = is_object($item) ? $item->pengarang : ($item['pengarang'] ?? '');
                                    $tahun = is_object($item) ? $item->tahun_terbit : ($item['tahun_terbit'] ?? '');
                                @endphp

                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-semibold">{{ $judul }}</td>
                                    <td>{{ $pengarang }}</td>
                                    <td><span class="badge bg-info text-dark">{{ $tahun }}</span></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning me-1" 
                                                onclick="editBuku({{ $id }}, '{{ addslashes($judul) }}', '{{ addslashes($pengarang) }}', '{{ $tahun }}')">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="hapusBuku({{ $id }})">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                        
                                        <form id="delete-form-{{ $id }}" action="/buku/{{ $id }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fa-solid fa-folder-open fa-2x mb-2"></i><br>
                                        Belum ada data buku tersimpan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Buku -->
    <div class="modal fade" id="modalTambahBuku" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/buku" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Tambah Buku Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Judul Buku</label>
                            <input type="text" name="judul_buku" class="form-control" required placeholder="Contoh: Pemrograman Web dengan Laravel">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pengarang</label>
                            <input type="text" name="pengarang" class="form-control" required placeholder="Contoh: M. Maulana Rachman">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tahun Terbit</label>
                            <input type="number" name="tahun_terbit" class="form-control" min="1900" max="2099" required placeholder="2026">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Buku</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Buku -->
    <div class="modal fade" id="modalEditBuku" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formEditBuku" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Data Buku</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Judul Buku</label>
                            <input type="text" id="edit_judul_buku" name="judul_buku" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pengarang</label>
                            <input type="text" id="edit_pengarang" name="pengarang" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tahun Terbit</label>
                            <input type="number" id="edit_tahun_terbit" name="tahun_terbit" class="form-control" min="1900" max="2099" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Update Buku</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center text-muted py-3 border-top bg-white mt-auto">
        <small>© Universitas IPWIJA - 2026 - Nim - Nama Lengkap</small>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Fungsi Pop-up Edit Buku
        function editBuku(id, judul, pengarang, tahun) {
            document.getElementById('formEditBuku').action = '/buku/' + id;
            document.getElementById('edit_judul_buku').value = judul;
            document.getElementById('edit_pengarang').value = pengarang;
            document.getElementById('edit_tahun_terbit').value = tahun;
            
            var modalEdit = new bootstrap.Modal(document.getElementById('modalEditBuku'));
            modalEdit.show();
        }

        // Fungsi Hapus Buku
        function hapusBuku(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data buku akan dihapus dari MySQL, Redis, dan Elasticsearch!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
</body>
</html>